<?php

use App\Http\Controllers\Kepsek\{
    DashboardController,
    GuruController,
    HasilController,
    PenilaianController
};
use App\Http\Controllers\MooraController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Halaman guru
Route::resource('gurus', GuruController::class);

// Penilaian
Route::prefix('penilaians')->group(function () {
    Route::get('/', [PenilaianController::class, 'index'])->name('penilaians.index');
    Route::post('/', [PenilaianController::class, 'store'])->name('penilaians.store');
    Route::get('/data', [PenilaianController::class, 'getData'])->name('penilaians.data');
    Route::get('/get', [PenilaianController::class, 'getPenilaian'])->name('penilaians.get');
    Route::post('/delete-by-guru', [PenilaianController::class, 'destroyByGuru'])->name('penilaians.destroy-by-guru');
});

// Hasil MOORA
Route::prefix('hasils')->group(function () {
    Route::get('/', [HasilController::class, 'index'])->name('hasils.index');
    Route::post('/calculate', [HasilController::class, 'calculate'])->name('hasils.calculate');
    Route::get('/detail', [HasilController::class, 'detail'])->name('hasils.detail');
});

// MOORA Calculation API
Route::prefix('moora')->group(function () {
    Route::get('/weights', [HasilController::class, 'getNormalizedWeights'])->name('moora.weights');
    Route::get('/ranking', [HasilController::class, 'calculateRanking'])->name('moora.ranking');
    Route::get('/full-calculation', [HasilController::class, 'fullCalculation'])->name('moora.full-calculation');
    Route::get('/hasil', [HasilController::class, 'getHasil'])->name('moora.hasil');
});
