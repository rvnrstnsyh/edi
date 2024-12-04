<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ItemStoreRequest;
use App\Http\Requests\ItemUpdateRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ItemController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/items",
     *     operationId="getItems",
     *     tags={"Items"},
     *     summary="Get all items",
     *     description="Retrieve a list of all items",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of items retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Product Name"),
     *                 @OA\Property(property="description", type="string", example="Product description"),
     *                 @OA\Property(property="price", type="number", format="float", example=99.99),
     *                 @OA\Property(property="initial_stock", type="integer", example=100),
     *                 @OA\Property(property="current_stock", type="integer", example=95),
     *                 @OA\Property(property="image_url", type="string", nullable=true, example="/storage/items/example.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to retrieve item list"),
     *             @OA\Property(property="error", type="string", example="Error details")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $items = Item::all();
            return response()->json($items);
        } catch (Exception $error) {
            Log::error('Error fetching items: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve item list',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/items",
     *     operationId="createItem",
     *     tags={"Items"},
     *     summary="Create a new item",
     *     description="Add a new item to the database",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "description", "price", "initial_stock"},
     *             @OA\Property(property="name", type="string", example="New Product"),
     *             @OA\Property(property="description", type="string", example="Product description"),
     *             @OA\Property(property="price", type="number", format="float", example=99.99),
     *             @OA\Property(property="initial_stock", type="integer", example=100),
     *             @OA\Property(property="image", type="string", format="binary", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="New Product"),
     *             @OA\Property(property="description", type="string", example="Product description"),
     *             @OA\Property(property="price", type="number", format="float", example=99.99),
     *             @OA\Property(property="initial_stock", type="integer", example=100),
     *             @OA\Property(property="current_stock", type="integer", example=100),
     *             @OA\Property(property="image_url", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to create item"),
     *             @OA\Property(property="error", type="string", example="Error details")
     *         )
     *     )
     * )
     */
    public function store(ItemStoreRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('items', 'public');
                $validatedData['image_url'] = Storage::url($imagePath);
            }
            $validatedData['current_stock'] = $validatedData['initial_stock'];
            $item = Item::create($validatedData);

            return response()->json($item, 201);
        } catch (Exception $error) {
            if (isset($imagePath)) Storage::disk('public')->delete($imagePath);
            Log::error('Error creating item: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to create item',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/items/{id}",
     *     operationId="getItem",
     *     tags={"Items"},
     *     summary="Get item by ID",
     *     description="Retrieve details of a specific item by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the item to retrieve",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Product Name"),
     *             @OA\Property(property="description", type="string", example="Product description"),
     *             @OA\Property(property="price", type="number", format="float", example=99.99),
     *             @OA\Property(property="initial_stock", type="integer", example=100),
     *             @OA\Property(property="current_stock", type="integer", example=95),
     *             @OA\Property(property="image_url", type="string", nullable=true, example="/storage/items/example.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Item not found"),
     *             @OA\Property(property="error", type="string", example="Error details")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to retrieve item"),
     *             @OA\Property(property="error", type="string", example="Error details")
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $item = Item::findOrFail($id);
            return response()->json($item);
        } catch (ModelNotFoundException $error) {
            Log::warning('Item not found: ' . $id);
            return response()->json([
                'message' => 'Item not found',
                'error' => $error->getMessage()
            ], 404);
        } catch (Exception $error) {
            Log::error('Error fetching item: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve item',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/items/{id}",
     *     operationId="updateItem",
     *     tags={"Items"},
     *     summary="Update item by ID",
     *     description="Update details of a specific item by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the item to update",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "description", "price", "initial_stock"},
     *             @OA\Property(property="name", type="string", example="Updated Product"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="price", type="number", format="float", example=109.99),
     *             @OA\Property(property="initial_stock", type="integer", example=150),
     *             @OA\Property(property="image", type="string", format="binary", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Updated Product"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="price", type="number", format="float", example=109.99),
     *             @OA\Property(property="initial_stock", type="integer", example=150),
     *             @OA\Property(property="current_stock", type="integer", example=95),
     *             @OA\Property(property="image_url", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Item not found"),
     *             @OA\Property(property="error", type="string", example="Error details")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to update item"),
     *             @OA\Property(property="error", type="string", example="Error details")
     *         )
     *     )
     * )
     */
    public function update(ItemUpdateRequest $request, string $id): JsonResponse
    {
        try {
            $item = Item::findOrFail($id);
            $validatedData = $request->validated();
            if (isset($validatedData['image_url']) && $validatedData['image_url'] !== $item->image_url) {
                $oldImagePath = $item->image_url ? str_replace('/storage/', '', $item->image_url) : null;
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            $item->update($validatedData);
            return response()->json($item);
        } catch (ModelNotFoundException $error) {
            Log::warning('Item not found for update: ' . $id);
            return response()->json([
                'message' => 'Item not found',
                'error' => $error->getMessage()
            ], 404);
        } catch (Exception $error) {
            Log::error('Error updating item: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to update item',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/items/{id}",
     *     operationId="deleteItem",
     *     tags={"Items"},
     *     summary="Delete item by ID",
     *     description="Delete a specific item by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the item to delete",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Item deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Item not found"),
     *             @OA\Property(property="error", type="string", example="Error details")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to delete item"),
     *             @OA\Property(property="error", type="string", example="Error details")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $item = Item::findOrFail($id);
            $imagePath = null;
            if ($item->image_url) $imagePath = str_replace('/storage/', '', $item->image_url);
            $item->delete();

            if ($imagePath && Storage::disk('public')->exists($imagePath)) Storage::disk('public')->delete($imagePath);

            return response()->json(null, 204);
        } catch (ModelNotFoundException $error) {
            Log::warning('Item not found for deletion: ' . $id);
            return response()->json([
                'message' => 'Item not found',
                'error' => $error->getMessage()
            ], 404);
        } catch (Exception $error) {
            Log::error('Error deleting item: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to delete item',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
