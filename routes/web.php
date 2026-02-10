<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    // If logout parameter is present, don't redirect even if authenticated
    if (request()->has('logout')) {
        return view('auth.login');
    }

    if (auth()->check()) {
        return match (auth()->user()->type) {
            \App\Models\User::TYPE_ADMIN => redirect()->route('admin.dashboard'),
            \App\Models\User::TYPE_VENDOR => redirect()->route('vendor.dashboard'),
            default => redirect()->route('home'),
        };
    }

    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }

    return view('auth.register');
})->name('register');

/*
|--------------------------------------------------------------------------
| Public Product Routes (for clients/users)
|--------------------------------------------------------------------------
*/
Route::get('/products/{id}', function (string $id) {
    return view('products.show', ['productId' => $id]);
})->name('products.show');

/*
|--------------------------------------------------------------------------
| Public Vendor Routes (for clients/users)
|--------------------------------------------------------------------------
*/
Route::get('/vendors/{id}', function (string $id) {
    return view('vendors.show', ['vendorId' => $id]);
})->name('vendors.show');

/*
|--------------------------------------------------------------------------
| Vendor Web Routes
|--------------------------------------------------------------------------
|
| All vendor routes are protected by the 'auth' (session) and 'vendor'
| middleware so that only authenticated vendors can access these pages.
|
*/
Route::prefix('vendor')->as('vendor.')->middleware(['auth', 'vendor'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('vendor.dashboard');
    });

    Route::get('/dashboard', function () {
        return view('vendor.dashboard');
    })->name('dashboard');

    // Product Management
    Route::get('/products', function () {
        return view('vendor.products.index');
    })->name('products.index');

    Route::get('/products/create', function () {
        return view('vendor.products.create');
    })->name('products.create');

    Route::get('/products/{id}/edit', function (string $id) {
        return view('vendor.products.edit', ['productId' => $id]);
    })->name('products.edit');

    Route::get('/products/{id}', function (string $id) {
        return view('vendor.products.show', ['productId' => $id]);
    })->name('products.show');
});

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
|
| All admin routes are protected by the 'auth' (session) and 'admin'
| middleware so that only authenticated admins can access these pages.
|
*/
Route::prefix('admin')->as('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Redirect /admin to /admin/dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Vendor Management
    Route::get('/vendors', function () {
        return view('admin.vendors.index');
    })->name('vendors.index');

    Route::get('/vendors/create', function () {
        return view('admin.vendors.create');
    })->name('vendors.create');

    Route::get('/vendors/{id}', function (string $id) {
        return view('admin.vendors.show', ['vendorId' => $id]);
    })->name('vendors.show');

    Route::get('/vendors/{id}/edit', function (string $id) {
        return view('admin.vendors.edit', ['vendorId' => $id]);
    })->name('vendors.edit');

    // Product Management
    Route::get('/products', function () {
        return view('admin.products.index');
    })->name('products.index');

    Route::get('/products/create', function () {
        return view('admin.products.create');
    })->name('products.create');

    Route::get('/products/{id}/edit', function (string $id) {
        return view('admin.products.edit', ['productId' => $id]);
    })->name('products.edit');

    Route::get('/products/{id}', function (string $id) {
        return view('admin.products.show', ['productId' => $id]);
    })->name('products.show');

    // User Management
    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');

    Route::get('/users/create', function () {
        return view('admin.users.create');
    })->name('users.create');

    Route::get('/users/{id}/edit', function (string $id) {
        return view('admin.users.edit', ['userId' => $id]);
    })->name('users.edit');
});
