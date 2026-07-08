<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\LapanganApiController;
use App\Http\Controllers\Api\ReservasiApiController;

// Public API routes
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);

Route::get('/lapangan', [LapanganApiController::class, 'index']);
Route::get('/lapangan/{lapangan}', [LapanganApiController::class, 'show']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user', [AuthApiController::class, 'user']);

    Route::get('/reservasi', [ReservasiApiController::class, 'index']);
    Route::post('/reservasi', [ReservasiApiController::class, 'store']);
    Route::get('/reservasi/{reservasi}', [ReservasiApiController::class, 'show']);
});
