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
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
