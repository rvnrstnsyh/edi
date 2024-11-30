<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\AuthenticatedTokenController;

Route::post('login', [AuthenticatedTokenController::class, 'store'])->name('login.api');

Route::middleware(['auth:api'])->group(function () {
    Route::post('me', [AuthenticatedTokenController::class, 'show'])->name('me.api');
    Route::post('refresh', [AuthenticatedTokenController::class, 'update'])->name('refresh.api');
    Route::post('logout', [AuthenticatedTokenController::class, 'destroy'])->name('logout.api');
});

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('item')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('item.index');
        Route::post('/', [ItemController::class, 'store'])->name('item.store');
        Route::get('/{id}', [ItemController::class, 'show'])->name('item.show');
        Route::put('/{id}', [ItemController::class, 'update'])->name('item.update');
        Route::delete('/{id}', [ItemController::class, 'destroy'])->name('item.destroy');
    });
});
