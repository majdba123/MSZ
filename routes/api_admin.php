<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPhotoController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\VendorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Routes for admin-only operations. All routes here are prefixed with
| /api/admin and protected by auth:sanctum + admin middleware.
|
*/

Route::apiResource('vendors', VendorController::class);
Route::patch('vendors/{vendor}/toggle-active', [VendorController::class, 'toggleActive'])->name('vendors.toggle-active');
Route::apiResource('users', UserController::class);
Route::apiResource('products', ProductController::class);
Route::patch('products/{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('products.toggle-active');
Route::patch('products/{product}/status', [ProductController::class, 'updateStatus'])->name('products.update-status');
Route::patch('products/{product}/photos/{photo}/set-primary', [ProductController::class, 'setPrimaryPhoto'])->name('products.set-primary-photo');

// Product Photos (separate API)
Route::get('products/{product}/photos', [ProductPhotoController::class, 'index'])->name('products.photos.index');
Route::post('products/{product}/photos', [ProductPhotoController::class, 'store'])->name('products.photos.store');
Route::delete('products/{product}/photos/{photo}', [ProductPhotoController::class, 'destroy'])->name('products.photos.destroy');
Route::delete('products/{product}/photos', [ProductPhotoController::class, 'bulkDestroy'])->name('products.photos.bulk-destroy');
