<?php

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
