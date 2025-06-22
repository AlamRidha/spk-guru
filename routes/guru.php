<?php

use App\Http\Controllers\Guru\{
    DashboardController,
    ProfileController
};
use Illuminate\Support\Facades\Route;

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');


// Lihat Hasil
Route::get('hasil', [DashboardController::class, 'hasil'])->name('hasil');
