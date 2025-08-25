<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AktualController;
use App\Http\Controllers\DashboardController;

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', [DashboardController::class, 'getDataDashboard2']);
Route::apiResource('dashboard', DashboardController::class);
// Route::prefix('aktual')->group(function () {
//     Route::get('/', [AktualController::class, 'apiIndex']);
//     Route::post('/', [AktualController::class, 'apiStore']);
//     Route::get('/{id}', [AktualController::class, 'apiShow']);
//     Route::put('/{id}', [AktualController::class, 'apiUpdate']);
//     Route::delete('/{id}', [AktualController::class, 'apiDestroy']);
// });
