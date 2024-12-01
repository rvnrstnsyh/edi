<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\TransactionStoreRequest;

class TransactionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pos/transactions",
     *     operationId="listTransactions",
     *     tags={"Transactions"},
     *     summary="List all transactions",
     *     description="Retrieve a list of all transactions with associated item details, sorted by transaction date in descending order",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved transactions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="item_id", type="integer", example=1),
     *                 @OA\Property(property="quantity", type="integer", example=5),
     *                 @OA\Property(property="total_price", type="number", format="float", example=250.50),
     *                 @OA\Property(property="transaction_date", type="string", format="date-time"),
     *                 @OA\Property(
     *                     property="item",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="price", type="number", format="float")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error during transaction retrieval",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to retrieve transactions"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 description="Detailed error message"
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $transactions = Transaction::with('item')
                ->orderBy('transaction_date', 'desc')
                ->get();
            return response()->json($transactions);
        } catch (Exception $error) {
            Log::error('Error fetching transactions: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve transactions',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/pos/transactions",
     *     operationId="createTransaction",
     *     tags={"Transactions"},
     *     summary="Create a new transaction",
     *     description="Process a new transaction by checking item stock and creating a transaction record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"item_id", "quantity"},
     *             @OA\Property(
     *                 property="item_id",
     *                 type="integer",
     *                 description="ID of the item being transacted",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="quantity",
     *                 type="integer",
     *                 description="Quantity of items to be transacted",
     *                 example=5
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transaction successfully created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="item_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=5),
     *             @OA\Property(property="total_price", type="number", format="float", example=250.50),
     *             @OA\Property(property="transaction_date", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Insufficient stock for the transaction",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Insufficient stock"
     *             ),
     *             @OA\Property(
     *                 property="current_stock",
     *                 type="integer",
     *                 description="Current available stock"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error during transaction processing",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to process transaction"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 description="Detailed error message"
     *             )
     *         )
     *     )
     * )
     */
    public function store(TransactionStoreRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $validatedData = $request->validated();
                // Find the item
                $item = Item::findOrFail($validatedData['item_id']);
                // Check stock availability
                if ($item->current_stock < $validatedData['quantity']) {
                    return response()->json([
                        'message' => 'Insufficient stock',
                        'current_stock' => $item->current_stock
                    ], 400);
                }

                // Calculate total price
                $totalPrice = $item->price * $validatedData['quantity'];
                // Create transaction
                $transaction = Transaction::create([
                    'item_id' => $item->id,
                    'quantity' => $validatedData['quantity'],
                    'total_price' => $totalPrice,
                    'transaction_date' => now()
                ]);
                // Update item stock
                $item->current_stock -= $validatedData['quantity'];
                $item->save();

                return response()->json($transaction, 201);
            });
        } catch (Exception $error) {
            Log::error('Transaction error: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to process transaction',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
