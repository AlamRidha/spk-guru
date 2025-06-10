<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::view('/', 'welcome')->name('landing');
});

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Role-based Routing
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));
    Route::middleware('role:kepsek')->prefix('kepsek')->name('kepsek.')->group(base_path('routes/kepsek.php'));
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(base_path('routes/guru.php'));
});
