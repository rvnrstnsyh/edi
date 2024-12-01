<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionReportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reports/stock",
     *     operationId="getStockReport",
     *     tags={"Reports"},
     *     summary="Generate stock report",
     *     description="Retrieve a comprehensive report of current item stocks",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully generated stock report",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Product Name"),
     *                 @OA\Property(property="category", type="string", example="Electronics"),
     *                 @OA\Property(property="current_stock", type="integer", example=50),
     *                 @OA\Property(property="price", type="number", format="float", example=99.99)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error during stock report generation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to generate stock report"
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
    public function stockReport(): JsonResponse
    {
        try {
            $stockReport = DB::table('items')
                ->select('name', 'category', 'current_stock', 'price')
                ->get();
            return response()->json($stockReport);
        } catch (Exception $error) {
            Log::error('Error generating stock report: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to generate stock report',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/reports/transaction",
     *     operationId="getTransactionReport",
     *     tags={"Reports"},
     *     summary="Generate transaction report",
     *     description="Retrieve a comprehensive report of all transactions with associated item details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully generated transaction report",
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
     *         description="Server error during transaction report generation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to generate transaction report"
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
    public function transactionReport(): JsonResponse
    {
        try {
            $report = Transaction::with('item')
                ->select('id', 'item_id', 'quantity', 'total_price', 'transaction_date')
                ->orderBy('transaction_date', 'desc')
                ->get();
            return response()->json($report);
        } catch (Exception $error) {
            Log::error('Error generating transaction report: ' . $error->getMessage());
            return response()->json([
                'message' => 'Failed to generate transaction report',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
