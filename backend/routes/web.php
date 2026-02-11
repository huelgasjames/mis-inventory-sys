<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    return view('welcome');
});

// Inventory routes
Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
