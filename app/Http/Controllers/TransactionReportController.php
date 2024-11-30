<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionReportController extends Controller
{
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
