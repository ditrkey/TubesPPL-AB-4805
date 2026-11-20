<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SiagaKampus
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('admin.reports.index');
});

// Autentikasi Pengguna (UC-01 / Breeze Standard)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Peta Spasial Publik (UC-15)
Route::controller(MapController::class)->group(function () {
    Route::get('/map', 'index')->name('map.index');
    Route::get('/map/data', 'data')->name('map.data');
});

// Modul Web Dashboard Pengelola (Admin & Superadmin) - KF-05 / UC-05
Route::middleware(['auth', 'role:admin|superadmin'])->prefix('admin')->name('admin.')->group(function () {
    // Alur Verifikasi Khusus Status Laporan Banjir (UC-05)
    Route::patch('reports/{report}/status', [ReportController::class, 'updateStatus'])->name('reports.update-status');

    // Resource CRUD Manajemen Laporan Masuk
    Route::resource('reports', ReportController::class);
});
