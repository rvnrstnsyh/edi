<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\ItemsController;
use App\Http\Controllers\AuthenticatedTokenController;

Route::middleware(['auth:api'])->group(function () {
    Route::get('items', [ItemsController::class, 'index'])->name('items.index');
    Route::get('me', [AuthenticatedTokenController::class, 'me'])->name('me.api');
    Route::post('refresh', [AuthenticatedTokenController::class, 'refresh'])->name('refresh.api');
    Route::post('logout', [AuthenticatedTokenController::class, 'logout'])->name('logout.api');
});
