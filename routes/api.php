<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionReportController;
use App\Http\Controllers\Auth\AuthenticatedTokenController;
use App\Http\Controllers\ImageUploadController;

Route::post('login', [AuthenticatedTokenController::class, 'store'])->name('login.api');

Route::middleware(['auth:api'])->group(function () {
    Route::post('me', [AuthenticatedTokenController::class, 'show'])->name('me.api');
    Route::post('refresh', [AuthenticatedTokenController::class, 'update'])->name('refresh.api');
    Route::post('logout', [AuthenticatedTokenController::class, 'destroy'])->name('logout.api');

    Route::post('/upload-image', [ImageUploadController::class, 'store'])->name('image.store');

    // Item Routes
    Route::prefix('items')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('item.index');
        Route::post('/', [ItemController::class, 'store'])->name('item.store');
        Route::get('/{id}', [ItemController::class, 'show'])->name('item.show');
        Route::put('/{id}', [ItemController::class, 'update'])->name('item.update');
        Route::delete('/{id}', [ItemController::class, 'destroy'])->name('item.destroy');
    });

    // Transaction Routes
    Route::prefix('pos')->group(function () {
        Route::get('transactions', [TransactionController::class, 'index'])->name('transaction.index');
        Route::post('transactions', [TransactionController::class, 'store'])->name('transaction.store');
    });

    // Report Routes
    Route::prefix('reports')->group(function () {
        Route::get('stock', [TransactionReportController::class, 'stockReport'])->name('stock.report');
        Route::get('transaction', [TransactionReportController::class, 'transactionReport'])->name('transaction.report');
    });
});
