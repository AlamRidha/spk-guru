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
Route::resource('penilaians', PenilaianController::class)->except(['create', 'show', 'edit']);
Route::get('penilaians/data', [PenilaianController::class, 'getData'])->name('penilaians.data');
Route::get('penilaians/get', [PenilaianController::class, 'getPenilaian'])->name('penilaians.get');

// Custom delete route since we're deleting by guru_id
Route::post('penilaians/delete-by-guru', [PenilaianController::class, 'destroyByGuru'])->name('penilaians.destroy-by-guru');

// Hasil
Route::get('hasil', [DashboardController::class, 'hasil'])->name('hasil');
