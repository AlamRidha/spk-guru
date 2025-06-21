<?php

use App\Http\Controllers\Admin\{
    DashboardController,
    UserController,
    KriteriaController,
    GuruController,
    SubKriteriaController
};
use Illuminate\Support\Facades\Route;


// Dashboard
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

// User Management
Route::resource('users', UserController::class)->except(['show']);
Route::resource('gurus', GuruController::class);


// Data Master
Route::prefix('kriterias')->name('kriterias.')->group(function () {
    Route::get('/', [KriteriaController::class, 'index'])->name('index');
    Route::get('/data-kepsek', [KriteriaController::class, 'dataKepsek'])->name('dataKepsek');
    Route::get('/data-wakur', [KriteriaController::class, 'dataWakur'])->name('dataWakur');
    Route::post('/', [KriteriaController::class, 'store'])->name('store');
    Route::put('/{kriteria}', [KriteriaController::class, 'update'])->name('update');
    Route::delete('/{kriteria}', [KriteriaController::class, 'destroy'])->name('destroy');
    Route::get('/normalized-weights-by-penilai', [KriteriaController::class, 'getNormalizedWeightsByPenilai'])
        ->name('normalized-weights-by-penilai');
});


Route::resource('sub-kriterias', SubKriteriaController::class)->except(['show']);
Route::get('sub-kriterias/{kriteria}/by-kriteria', [SubKriteriaController::class, 'getByKriteria'])
    ->name('admin.sub-kriterias.by-kriteria');

// MOORA Calculation
Route::post('calculate-moora', [DashboardController::class, 'calculateMoora'])
    ->name('calculate.moora');
