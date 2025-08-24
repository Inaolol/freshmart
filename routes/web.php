<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Redirect home to products listing
Route::get('/', function () {
    return redirect()->route('products.index');
});

// Product routes
Route::resource('products', ProductController::class);

// Additional API-like routes for AJAX requests
Route::get('/api/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/api/products/categories', [ProductController::class, 'categories'])->name('products.categories');
