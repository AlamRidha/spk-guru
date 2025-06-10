<?php

use App\Http\Controllers\Kepsek\{
    DashboardController,
    PenilaianController
};
use Illuminate\Support\Facades\Route;

Route::prefix('kepsek')->name('kepsek.')->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Penilaian
    Route::resource('penilaians', PenilaianController::class)->only(['index', 'store']);

    // Hasil
    Route::get('hasil', [DashboardController::class, 'hasil'])->name('hasil');
});
