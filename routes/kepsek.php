<?php

use App\Http\Controllers\Kepsek\{
    DashboardController,
    GuruController,
    HasilController,
    PenilaianController,
    RankingController
};
use App\Http\Controllers\MooraController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Halaman guru
Route::resource('gurus', GuruController::class);

// Penilaian
Route::prefix('penilaians')->name('penilaians.')->group(function () {
    Route::get('/', [PenilaianController::class, 'index'])->name('index');
    Route::get('/data', [PenilaianController::class, 'getData'])->name('data');
    Route::get('/get', [PenilaianController::class, 'getPenilaian'])->name('get');
    Route::post('/', [PenilaianController::class, 'store'])->name('store');
    Route::delete('/{penilaian}', [PenilaianController::class, 'destroy'])->name('destroy');
    Route::post('/destroy-by-guru', [PenilaianController::class, 'destroyByGuru'])->name('destroy-by-guru');

    Route::get('/matriks', [PenilaianController::class, 'matrikKeputusan'])->name('matriks');
    Route::get('/normalisasi-matriks', [PenilaianController::class, 'normalisasiMatriks'])->name('normalisasimatrik');
});


// Ranking
Route::prefix('ranking')->name('ranking.')->group(function () {
    Route::get('/', [HasilController::class, 'index'])->name('index');
    Route::get('/export', [HasilController::class, 'export'])->name('export');
});
