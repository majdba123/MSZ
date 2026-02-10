<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->as('auth.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/XSRF-TOKEN', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
});

/*
|--------------------------------------------------------------------------
| Public Product Routes (for clients/users)
|--------------------------------------------------------------------------
*/
Route::prefix('products')->as('products.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\ProductController::class, 'publicIndex'])->name('public.index');
    Route::get('/{product}', [\App\Http\Controllers\Api\ProductController::class, 'publicShow'])->name('public.show');
});

/*
|--------------------------------------------------------------------------
| Public Vendor Routes (for clients/users)
|--------------------------------------------------------------------------
*/
Route::prefix('vendors')->as('vendors.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\VendorController::class, 'index'])->name('public.index');
    Route::get('/{vendor}', [\App\Http\Controllers\Api\VendorController::class, 'show'])->name('public.show');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');
});
