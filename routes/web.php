<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->as('admin.')->group(function () {
    // Public: Admin Login
    Route::get('/login', function () {
        return view('admin.login');
    })->name('login');

    // Redirect /admin to /admin/login
    Route::get('/', function () {
        return redirect()->route('admin.login');
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

    Route::get('/vendors/{id}/edit', function (string $id) {
        return view('admin.vendors.edit', ['vendorId' => $id]);
    })->name('vendors.edit');

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
