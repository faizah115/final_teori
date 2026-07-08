<?php

use App\Http\Controllers\Admin\AdminReservasiController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LapanganController as AdminLapanganController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ────────────────────────────────────────────────────────────

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/lapangan', [LapanganController::class, 'index'])->name('lapangan.index');
Route::get('/lapangan/{lapangan}', [LapanganController::class, 'show'])->name('lapangan.show');

// ─── User Routes (hanya USER, bukan ADMIN) ───────────────────────────────────

Route::middleware(['auth', 'user'])->group(function () {
    // Dashboard User
    Route::get('/dashboard', [UserDashboardController::class, 'index'])
        ->name('dashboard');

    // Booking Reservasi (hanya user biasa)
    Route::post('/reservasi', [ReservasiController::class, 'store'])->name('reservasi.store');
});

// ─── Authenticated Routes (Admin & User bisa akses) ──────────────────────────

Route::middleware('auth')->group(function () {
    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Kelola Lapangan
    Route::resource('lapangan', AdminLapanganController::class)->names('admin.lapangan');
    Route::patch('lapangan/{lapangan}/toggle-status', [AdminLapanganController::class, 'toggleStatus'])
        ->name('admin.lapangan.toggleStatus');

    // Kelola Reservasi
    Route::get('reservasi', [AdminReservasiController::class, 'index'])->name('admin.reservasi.index');
    Route::patch('reservasi/{reservasi}/status', [AdminReservasiController::class, 'updateStatus'])
        ->name('admin.reservasi.updateStatus');
    Route::delete('reservasi/{reservasi}', [AdminReservasiController::class, 'destroy'])
        ->name('admin.reservasi.destroy');

    // Kelola User
    Route::resource('users', AdminUserController::class)->except(['show'])->names('admin.users');
});

// ─── Auth Routes (Breeze) ─────────────────────────────────────────────────────

require __DIR__ . '/auth.php';
