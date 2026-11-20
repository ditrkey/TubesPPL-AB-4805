<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\ReportController as AdminReportController;

/**
 * ReportController
 * 
 * Sesuai Diagram 4.4 (Class Diagram) & Arsitektur Sistem SiagaKampus:
 * Menangani penerimaan laporan warga, pencarian berbasis lokasi, 
 * hingga pembaruan status verifikasi laporan banjir (PBI-05 / KF-05 / UC-05).
 */
class ReportController extends AdminReportController
{
    // Mewarisi seluruh method operasional:
    // - index(): Daftar laporan & statistik ringkasan
    // - create(): Form input laporan manual dengan peta Leaflet.js
    // - store(): Simpan laporan baru & koordinat MySQL Spatial POINT
    // - show(): Tinjau detail bukti foto & peta spasial
    // - edit(): Koreksi rincian data laporan
    // - update(): Simpan pembaruan data laporan
    // - updateStatus(): Alur verifikasi status (Pending -> Verified/Rejected -> Handling -> Resolved)
    // - destroy(): Hapus laporan (Soft Delete) & rekam Audit Trail
}

