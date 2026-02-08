<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPhotoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Vendor API Routes
|--------------------------------------------------------------------------
|
| Routes for vendor-only operations. All routes here are prefixed with
| /api/vendor and protected by auth:sanctum + vendor middleware.
|
*/

Route::apiResource('products', ProductController::class);
Route::patch('products/{product}/photos/{photo}/set-primary', [ProductController::class, 'setPrimaryPhoto'])->name('products.set-primary-photo');

// Product Photos (separate API)
Route::get('products/{product}/photos', [ProductPhotoController::class, 'index'])->name('products.photos.index');
Route::post('products/{product}/photos', [ProductPhotoController::class, 'store'])->name('products.photos.store');
Route::delete('products/{product}/photos/{photo}', [ProductPhotoController::class, 'destroy'])->name('products.photos.destroy');
Route::delete('products/{product}/photos', [ProductPhotoController::class, 'bulkDestroy'])->name('products.photos.bulk-destroy');
