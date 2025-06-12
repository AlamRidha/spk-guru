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

// Data Master
Route::get('kriterias/all', [KriteriaController::class, 'getAll'])->name('kriterias.all');
Route::resource('kriterias', KriteriaController::class);
Route::resource('sub-kriterias', SubKriteriaController::class);
Route::resource('gurus', GuruController::class);

// MOORA Calculation
Route::post('calculate-moora', [DashboardController::class, 'calculateMoora'])
    ->name('calculate.moora');
