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
| Public Category Routes
|--------------------------------------------------------------------------
*/
Route::prefix('categories')->as('categories.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\Admin\CategoryController::class, 'index'])->name('public.index');
    Route::get('/{category}', [\App\Http\Controllers\Api\Admin\CategoryController::class, 'show'])->name('public.show');
    Route::get('/{category}/subcategories', function (\App\Models\Category $category) {
        return response()->json([
            'data' => $category->subcategories()->select('id', 'name', 'image', 'category_id')->get(),
        ]);
    })->name('public.subcategories');
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
