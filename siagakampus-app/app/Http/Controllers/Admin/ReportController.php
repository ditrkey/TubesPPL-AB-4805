<?php

namespace App\Http\Controllers\Admin;

use App\Events\ReportStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Report;
use App\Models\Zone;
use App\Services\FcmNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * READ (Index - /admin/reports)
     * Menampilkan daftar laporan masuk, statistik ringkasan, pencarian query, dan filter status.
     */
    public function index(Request $request): View
    {
        // 1. Kartu statistik ringkasan
        $stats = [
            'total' => Report::count(),
            'pending' => Report::where('status', 'Pending')->count(),
            'verified' => Report::where('status', 'Verified')->count(),
            'handling' => Report::where('status', 'Handling')->count(),
            'resolved' => Report::where('status', 'Resolved')->count(),
            'rejected' => Report::where('status', 'Rejected')->count(),
        ];

        // 2. Query builder dengan relasi dan koordinat spasial
        $reports = Report::withCoordinates()
            ->with(['user', 'zone', 'fieldAssignment.user'])
            ->filter($request->only(['q', 'status', 'category', 'date']))
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $categories = ['Banjir', 'Drainase', 'Akses Tertutup', 'Lainnya'];

        return view('admin.reports.index', compact('reports', 'stats', 'categories'));
    }

    /**
     * CREATE (Create - /admin/reports/create)
     * Form penginputan laporan banjir manual oleh Admin dengan peta Leaflet.js interaktif.
     */
    public function create(): View
    {
        $zones = Zone::orderBy('name')->get();
        $categories = ['Banjir', 'Drainase', 'Akses Tertutup', 'Lainnya'];
        $waterLevels = [
            'Low' => 'Rendah (< 15 cm)',
            'Med' => 'Sedang (15 - 40 cm)',
            'High' => 'Tinggi (> 40 cm)',
        ];

        // Default coordinate: Telkom University Landmark (-6.9732, 107.6314)
        $defaultLat = -6.9732;
        $defaultLng = 107.6314;

        return view('admin.reports.create', compact('zones', 'categories', 'waterLevels', 'defaultLat', 'defaultLng'));
    }

    /**
     * STORE (Store - POST /admin/reports)
     * Menyimpan data laporan manual baru ke database spasial MySQL dan mencatat audit log.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:Banjir,Drainase,Akses Tertutup,Lainnya',
            'water_level' => 'required|string|in:Low,Med,High',
            'zone_id' => 'nullable|exists:zones,id',
            'description' => 'required|string|min:10',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'status' => 'nullable|in:Pending,Verified',
        ], [
            'title.required' => 'Judul laporan wajib diisi.',
            'category.required' => 'Kategori kejadian wajib dipilih.',
            'water_level.required' => 'Tingkat ketinggian air wajib dipilih.',
            'description.required' => 'Deskripsi detail kondisi genangan wajib diisi.',
            'description.min' => 'Deskripsi minimal 10 karakter.',
            'latitude.required' => 'Titik koordinat latitude wajib ditentukan pada peta.',
            'longitude.required' => 'Titik koordinat longitude wajib ditentukan pada peta.',
            'photo.required' => 'Foto bukti kejadian wajib diunggah.',
            'photo.image' => 'Berkas bukti harus berupa gambar.',
            'photo.mimes' => 'Format foto harus berupa JPG, JPEG, atau PNG.',
            'photo.max' => 'Ukuran berkas foto maksimal 5 MB.',
        ]);

        // Simpan file foto ke public storage
        $photoPath = $request->file('photo')->store('reports', 'public');

        // Koordinat MySQL Spatial POINT (SRID 4326)
        $lat = (float) $validated['latitude'];
        $lng = (float) $validated['longitude'];
        $location = DB::raw("ST_SRID(Point({$lng}, {$lat}), 4326)");

        $report = Report::create([
            'user_id' => auth()->id() ?? 1,
            'zone_id' => $validated['zone_id'] ?? null,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'water_level' => $validated['water_level'],
            'description' => $validated['description'],
            'photo_path' => $photoPath,
            'location' => $location,
            'status' => $validated['status'] ?? 'Pending',
        ]);

        // Catat Audit Trail (KNF-06)
        AuditLog::record("Admin membuat laporan insiden banjir manual (#{$report->code} - {$report->title})");

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', "Laporan banjir #{$report->code} berhasil dibuat dan dicatat ke sistem.");
    }

    /**
     * READ (Show - /admin/reports/{report})
     * Halaman detail laporan komprehensif, bukti foto penuh, pin Leaflet.js, dan panel verifikasi status.
     */
    public function show(Report $report): View
    {
        $report->load(['user', 'zone', 'fieldAssignment.user']);

        // Ambil riwayat audit log khusus untuk laporan ini
        $auditLogs = AuditLog::with('user')
            ->where('action', 'like', "%{$report->code}%")
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('admin.reports.show', compact('report', 'auditLogs'));
    }

    /**
     * UPDATE DATA (Edit - /admin/reports/{report}/edit)
     * Form untuk mengoreksi rincian data laporan banjir.
     */
    public function edit(Report $report): View
    {
        $report->load(['user', 'zone']);
        $zones = Zone::orderBy('name')->get();
        $categories = ['Banjir', 'Drainase', 'Akses Tertutup', 'Lainnya'];
        $waterLevels = [
            'Low' => 'Rendah (< 15 cm)',
            'Med' => 'Sedang (15 - 40 cm)',
            'High' => 'Tinggi (> 40 cm)',
        ];

        return view('admin.reports.edit', compact('report', 'zones', 'categories', 'waterLevels'));
    }

    /**
     * UPDATE DATA (Update - PUT/PATCH /admin/reports/{report})
     * Menyimpan koreksi data laporan.
     */
    public function update(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:Banjir,Drainase,Akses Tertutup,Lainnya',
            'water_level' => 'required|string|in:Low,Med,High',
            'zone_id' => 'nullable|exists:zones,id',
            'description' => 'required|string|min:10',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ], [
            'title.required' => 'Judul laporan wajib diisi.',
            'category.required' => 'Kategori kejadian wajib dipilih.',
            'water_level.required' => 'Tingkat ketinggian air wajib dipilih.',
            'description.required' => 'Deskripsi detail kondisi genangan wajib diisi.',
            'description.min' => 'Deskripsi minimal 10 karakter.',
            'latitude.required' => 'Titik koordinat latitude wajib ditentukan.',
            'longitude.required' => 'Titik koordinat longitude wajib ditentukan.',
            'photo.image' => 'Berkas bukti harus berupa gambar.',
            'photo.mimes' => 'Format foto harus berupa JPG, JPEG, atau PNG.',
            'photo.max' => 'Ukuran berkas foto maksimal 5 MB.',
        ]);

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika tersimpan di disk public
            if ($report->photo_path && Storage::disk('public')->exists($report->photo_path)) {
                Storage::disk('public')->delete($report->photo_path);
            }
            $report->photo_path = $request->file('photo')->store('reports', 'public');
        }

        $lat = (float) $validated['latitude'];
        $lng = (float) $validated['longitude'];

        $report->title = $validated['title'];
        $report->category = $validated['category'];
        $report->water_level = $validated['water_level'];
        $report->zone_id = $validated['zone_id'] ?? null;
        $report->description = $validated['description'];
        $report->location = DB::raw("ST_SRID(Point({$lng}, {$lat}), 4326)");
        $report->save();

        // Catat Audit Trail (KNF-06)
        AuditLog::record("Admin memperbarui data rincian laporan (#{$report->code} - {$report->title})");

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', "Rincian laporan #{$report->code} berhasil diperbarui.");
    }

    /**
     * UPDATE STATUS VERIFIKASI (Patch - /admin/reports/{report}/status)
     * Alur verifikasi laporan (UC-05):
     * - Pending -> Verified / Rejected
     * - Verified -> Handling
     * - Handling -> Resolved
     * - Validasi wajib: Jika status 'Rejected', rejection_reason wajib diisi (min 5 karakter).
     * - Push notification (FCM) dispatch ke pelapor jika Rejected atau Resolved.
     */
    public function updateStatus(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Verified,Handling,Resolved,Rejected',
            'rejection_reason' => 'required_if:status,Rejected|nullable|string|min:5',
        ], [
            'status.required' => 'Status laporan wajib dipilih.',
            'status.in' => 'Status verifikasi yang dipilih tidak valid.',
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi ketika menolak laporan.',
            'rejection_reason.min' => 'Alasan penolakan minimal 5 karakter agar pelapor mendapat kejelasan.',
        ]);

        $previousStatus = $report->status;
        $newStatus = $validated['status'];

        // Atur rejection_reason
        if ($newStatus === 'Rejected') {
            $report->rejection_reason = $validated['rejection_reason'];
        }

        $report->status = $newStatus;
        $report->save();

        // 1. Kirim Push Notification (FCM) ke pelapor jika status Rejected atau Resolved
        if (in_array($newStatus, ['Rejected', 'Resolved']) && $report->user) {
            FcmNotificationService::sendReportStatusNotification(
                user: $report->user,
                report: $report,
                newStatus: $newStatus,
                reason: $report->rejection_reason
            );
        }

        // 2. Dispatch Event ReportStatusUpdated
        ReportStatusUpdated::dispatch($report, $previousStatus, $newStatus);

        // 3. Catat Audit Trail (KNF-06)
        $auditAction = "Admin memverifikasi status laporan #{$report->code} dari [{$previousStatus}] menjadi [{$newStatus}]";
        if ($newStatus === 'Rejected') {
            $auditAction .= " (Alasan penolakan: {$report->rejection_reason})";
        }
        AuditLog::record($auditAction);

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', "Status laporan #{$report->code} berhasil diubah menjadi [{$newStatus}].");
    }

    /**
     * DELETE (Destroy - /admin/reports/{report})
     * Soft delete laporan banjir dan mencatat penghapusan ke audit trail (KNF-06).
     */
    public function destroy(Report $report): RedirectResponse
    {
        $code = $report->code;
        $title = $report->title;

        $report->delete();

        // Catat Audit Trail (KNF-06)
        AuditLog::record("Admin menghapus laporan #{$code} ({$title}) via Soft Delete");

        return redirect()
            ->route('admin.reports.index')
            ->with('success', "Laporan #{$code} berhasil dihapus dari sistem.");
    }
}

