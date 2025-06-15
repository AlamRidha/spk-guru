<?php

use App\Http\Controllers\Kepsek\{
    DashboardController,
    GuruController,
    PenilaianController
};
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Halaman guru
Route::resource('gurus', GuruController::class);

// Penilaian
Route::resource('penilaians', PenilaianController::class)->only(['index', 'store']);

// Hasil
Route::get('hasil', [DashboardController::class, 'hasil'])->name('hasil');
