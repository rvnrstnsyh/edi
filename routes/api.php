<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\ItemsController;
use App\Http\Controllers\Auth\AuthenticatedTokenController;

Route::post('login', [AuthenticatedTokenController::class, 'store'])->name('login.api');

Route::middleware(['auth:api'])->group(function () {
    Route::post('me', [AuthenticatedTokenController::class, 'show'])->name('me.api');
    Route::post('refresh', [AuthenticatedTokenController::class, 'update'])->name('refresh.api');
    Route::post('logout', [AuthenticatedTokenController::class, 'destroy'])->name('logout.api');
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('items', [ItemsController::class, 'index'])->name('items.index');
});
