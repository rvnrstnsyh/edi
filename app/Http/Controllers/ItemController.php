<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ItemStoreRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ItemController extends Controller
{
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

    public function store(ItemStoreRequest $request): JsonResponse
    {
        try {
            // Validate the request
            $validatedData = $request->validated();
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('items', 'public');
                $validatedData['image_url'] = Storage::url($imagePath);
            }
            // Set initial and current stock to be the same
            $validatedData['current_stock'] = $validatedData['initial_stock'];
            // Create the item
            $item = Item::create($validatedData);

            return response()->json($item, 201);
        } catch (Exception $error) {
            // Delete uploaded images if an error occurs
            if (isset($imagePath)) Storage::disk('public')->delete($imagePath);
            Log::error('Error creating item: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to create item',
                'error' => $error->getMessage()
            ], 500);
        }
    }

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

    public function update(ItemStoreRequest $request, string $id): JsonResponse
    {
        try {
            $item = Item::findOrFail($id);
            $validatedData = $request->validated();
            // Save old image path
            $oldImagePath = null;
            if ($item->image_url) $oldImagePath = str_replace('/storage/', '', $item->image_url);
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) Storage::disk('public')->delete($oldImagePath);
                $imagePath = $request->file('image')->store('items', 'public');
                $validatedData['image_url'] = Storage::url($imagePath);
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
            // If an error occurs, make sure the old image is not deleted
            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) Storage::disk('public')->delete($imagePath);
            Log::error('Error updating item: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to update item',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $item = Item::findOrFail($id);
            // Save image path to delete
            $imagePath = null;
            if ($item->image_url) $imagePath = str_replace('/storage/', '', $item->image_url);
            // Delete items from the database
            $item->delete();
            // Delete image files if any
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
