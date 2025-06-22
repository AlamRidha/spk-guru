<?php

use App\Http\Controllers\Guru\{
    DashboardController,
    ProfileController
};
use Illuminate\Support\Facades\Route;

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Profile
Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');

// Lihat Hasil
Route::get('hasil', [DashboardController::class, 'hasil'])->name('hasil');
