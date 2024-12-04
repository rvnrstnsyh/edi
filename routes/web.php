<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');
    Route::get('/item-management', fn() => Inertia::render('ItemManagement'))->name('item-management');
    Route::get('/item-management/create', fn() => Inertia::render('ItemManagementCreate'))->name('item-management-create');
    Route::get('/item-management/{id}/manage', fn() => Inertia::render('ItemManagementManage'))->name('item-management-manage');
    Route::get('/point-of-sale', fn() => Inertia::render('PointOfSale'))->name('point-of-sale');
    Route::get('/reports', fn() => Inertia::render('Reports'))->name('reports');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
