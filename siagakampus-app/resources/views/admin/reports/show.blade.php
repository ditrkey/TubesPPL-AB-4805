@extends('layouts.admin')

@section('title', 'Tinjau Laporan ' . $report->code)
@section('page_category', 'Verifikasi & Laporan')
@section('page_title', 'Tinjau Laporan ' . $report->code)

@section('content')
<div class="space-y-6" 
     x-data="{ 
         rejectModalOpen: false, 
         imageModalOpen: false, 
         rejectionReason: '',
         get isReasonValid() {
             return this.rejectionReason.trim().length >= 5;
         }
     }">

    <!-- TOP BAR / BREADCRUMB & QUICK ACTIONS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.index') }}" 
               class="w-9 h-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 flex items-center justify-center text-slate-600 transition shadow-xs">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
                        {{ $report->code }}
                    </span>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">{{ $report->title }}</h1>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Dilaporkan pada {{ $report->created_at->translatedFormat('l, d F Y - H:i') }} WIB oleh {{ $report->user->name ?? 'Anonim' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Edit Button -->
            <a href="{{ route('admin.reports.edit', $report) }}" 
               class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-pen-to-square text-xs text-slate-400"></i>
                <span>Edit Laporan</span>
            </a>

            <!-- Delete Button (Soft Delete) -->
            <form action="{{ route('admin.reports.destroy', $report) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan {{ $report->code }}? Aksi ini akan dicatat ke audit log.');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-3 py-2 rounded-xl border border-rose-200 bg-rose-50/50 hover:bg-rose-100 text-rose-700 font-semibold text-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>

    <!-- ALUR VERIFIKASI UTAMA (WORKFLOW CARD - UC-05) -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status Verifikasi Saat Ini</span>
                <div class="flex items-center gap-3">
                    @php $badge = $report->status_badge; @endphp
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold {{ $badge['badge_class'] }}">
                        <span class="w-2 h-2 rounded-full {{ $badge['dot_class'] }}"></span>
                        {{ $badge['label'] }} ({{ $report->status }})
                    </span>
                    <span class="text-xs text-slate-500">
                        Pembaruan Terakhir: {{ $report->updated_at->diffForHumans() }}
                    </span>
                </div>
            </div>

            <!-- Tombol Transisi Status Dinamis -->
            <div class="flex items-center flex-wrap gap-2.5">
                
                @if($report->status === 'Pending')
                    <!-- Aksi Verifikasi (Pending -> Verified) -->
                    <form action="{{ route('admin.reports.update-status', $report) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Verified">
                        <button type="submit" 
                                class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs shadow-sm transition flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-xs"></i>
                            <span>Verifikasi Laporan (Valid)</span>
                        </button>
                    </form>

                    <!-- Aksi Tolak Laporan (Pending -> Rejected) via Alpine Modal -->
                    <button type="button" 
                            @click="rejectModalOpen = true"
                            class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-ban text-xs"></i>
                        <span>Tolak Laporan</span>
                    </button>

                @elseif($report->status === 'Verified')
                    <!-- Aksi Alokasi ke Lapangan (Verified -> Handling) -->
                    <form action="{{ route('admin.reports.update-status', $report) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Handling">
                        <button type="submit" 
                                class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm transition flex items-center gap-1.5">
                            <i class="fa-solid fa-person-running text-xs"></i>
                            <span>Mulai Penanganan Lapangan (Handling)</span>
                        </button>
                    </form>

                    <button type="button" 
                            @click="rejectModalOpen = true"
                            class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-xs transition">
                        Tolak
                    </button>

                @elseif($report->status === 'Handling')
                    <!-- Aksi Selesai (Handling -> Resolved) -->
                    <form action="{{ route('admin.reports.update-status', $report) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Resolved">
                        <button type="submit" 
                                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm transition flex items-center gap-1.5">
                            <i class="fa-solid fa-check-double text-xs"></i>
                            <span>Tandai Penanganan Selesai (Resolved)</span>
                        </button>
                    </form>

                @elseif($report->status === 'Rejected')
                    <!-- Opsi Buka Kembali / Verifikasi Ulang -->
                    <form action="{{ route('admin.reports.update-status', $report) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Verified">
                        <button type="submit" 
                                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-rotate-left text-xs"></i>
                            <span>Buka Kembali & Verifikasi Ulang</span>
                        </button>
                    </form>

                @elseif($report->status === 'Resolved')
                    <div class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-semibold flex items-center gap-1.5 border border-emerald-200">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Insiden ini telah tuntas ditangani oleh tim lapangan.</span>
                    </div>
                @endif

            </div>
        </div>

        <!-- Banner Alasan Penolakan Jika Rejected -->
        @if($report->status === 'Rejected' && $report->rejection_reason)
            <div class="p-4 rounded-xl bg-rose-50/70 border border-rose-200 text-rose-900 text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5 text-rose-800">
                    <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                    <span>Catatan Alasan Penolakan Laporan:</span>
                </div>
                <p class="leading-relaxed pl-5 font-medium">{{ $report->rejection_reason }}</p>
                <p class="text-[10px] text-rose-600/80 pl-5">Alasan ini telah dikirimkan via Push Notification (FCM) kepada pelapor.</p>
            </div>
        @endif
    </div>

    <!-- 3-KOLOM GRID: FOTO, PETA, & RINCIAN DETAIL -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- KOLOM 1: FOTO BUKTI KEJADIAN (4 COLS) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-emerald-600"></i>
                        <span>Bukti Visual Kejadian</span>
                    </h2>
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Foto Asli</span>
                </div>

                <div class="relative group cursor-pointer overflow-hidden rounded-xl border border-slate-200"
                     @click="imageModalOpen = true">
                    <img src="{{ $report->photo_url }}" 
                         alt="Foto Bukti {{ $report->code }}" 
                         class="w-full h-64 object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1.5">
                        <i class="fa-solid fa-magnifying-glass-plus text-base"></i>
                        <span>Klik untuk Memperbesar</span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 space-y-1">
                    <div class="flex justify-between">
                        <span>Penyimpanan:</span>
                        <span class="font-mono text-slate-700">storage/app/public</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Status File:</span>
                        <span class="text-emerald-700 font-semibold">Tersimpan Valid</span>
                    </div>
                </div>
            </div>

            <!-- Penugasan Tim Lapangan (Field Assignment) jika ada -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-user-shield text-indigo-600"></i>
                        <span>Penugasan Tim Lapangan</span>
                    </h2>
                    <span class="text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200">
                        KF-07
                    </span>
                </div>

                @if($report->fieldAssignment)
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Petugas Ditunjuk:</span>
                            <span class="font-semibold text-slate-800">{{ $report->fieldAssignment->user->name ?? 'Tim K3L' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Status Tugas:</span>
                            <span class="font-semibold text-indigo-700 uppercase">{{ $report->fieldAssignment->status }}</span>
                        </div>
                        <div class="pt-1">
                            <span class="text-slate-500 block mb-0.5">Instruksi:</span>
                            <p class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-slate-700 text-xs">
                                {{ $report->fieldAssignment->instruction }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="py-4 text-center space-y-2">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xs">
                            <i class="fa-solid fa-clipboard-question"></i>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Belum ada tim lapangan yang ditugaskan untuk laporan ini.</p>
                        @if($report->status === 'Verified')
                            <span class="text-[11px] text-sky-700 font-semibold block">Laporan siap diteruskan ke modul Dispatching.</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- KOLOM 2: PETA INTERAKTIF LOKASI (4 COLS) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-emerald-600"></i>
                        <span>Titik Lokasi Geospasial</span>
                    </h2>
                    <span class="font-mono text-[10px] text-slate-400">Leaflet &amp; OSM</span>
                </div>

                <div class="rounded-xl overflow-hidden border border-slate-200 relative">
                    <div id="detailMap" class="w-full h-64 z-0 bg-slate-100"></div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">Latitude</span>
                        <span class="font-mono font-bold text-slate-800">{{ $report->latitude ?? -6.9732 }}</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">Longitude</span>
                        <span class="font-mono font-bold text-slate-800">{{ $report->longitude ?? 107.6314 }}</span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-emerald-50/60 border border-emerald-200 text-xs text-emerald-800 space-y-1">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Zona Rawan Terkait:</span>
                        <span class="font-bold">
                            {{ $report->zone->name ?? 'Di Luar Area Polygon' }}
                        </span>
                    </div>
                    @if($report->zone)
                        <div class="flex justify-between items-center text-[11px] text-emerald-700">
                            <span>Tingkat Risiko Zona:</span>
                            <span class="font-semibold uppercase">{{ $report->zone->risk_level }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- KOLOM 3: DETAIL LENGKAP & PELAPOR (4 COLS) -->
        <div class="lg:col-span-4 space-y-4">
            
            <!-- Rincian Parameter Laporan -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3.5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-emerald-600"></i>
                        <span>Parameter Insiden</span>
                    </h2>
                </div>

                <div class="space-y-3 text-xs">
                    <!-- Kategori -->
                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                        <span class="text-slate-500">Kategori Insiden:</span>
                        <span class="font-bold text-slate-800 px-2 py-0.5 rounded bg-slate-100">
                            {{ $report->category }}
                        </span>
                    </div>

                    <!-- Ketinggian Air Gauge -->
                    <div class="py-1 border-b border-slate-100 space-y-1.5">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Tingkat Genangan:</span>
                            @php $water = $report->water_level_info; @endphp
                            <span class="inline-flex items-center gap-1 font-bold px-2 py-0.5 rounded-full text-[11px] {{ $water['badge_class'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $water['dot_class'] }}"></span>
                                {{ $water['label'] }}
                            </span>
                        </div>
                        
                        <!-- Water Level Progress Bar Visual -->
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $report->water_level === 'High' ? 'w-full bg-rose-500' : ($report->water_level === 'Med' ? 'w-2/3 bg-amber-500' : 'w-1/3 bg-emerald-500') }}"></div>
                        </div>
                    </div>

                    <!-- Pelapor -->
                    <div class="pt-1 space-y-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Identitas Pelapor</span>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                            <div class="font-bold text-slate-800 text-sm">{{ $report->user->name ?? 'Anonim' }}</div>
                            <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                <i class="fa-regular fa-envelope text-slate-400"></i>
                                <span>{{ $report->user->email ?? '-' }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                <i class="fa-solid fa-phone text-slate-400"></i>
                                <span>{{ $report->user->phone ?? '-' }}</span>
                            </div>
                            <div class="pt-1">
                                <span class="inline-block text-[10px] font-semibold px-2 py-0.5 rounded bg-white text-slate-600 border border-slate-200">
                                    {{ $report->user->role ?? 'Warga Tel-U' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi Lengkap -->
                    <div class="pt-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Deskripsi Kondisi</span>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-700 text-xs leading-relaxed">
                            {{ $report->description }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Trail Laporan (KNF-06) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                        <span>Audit Trail Kejadian</span>
                    </h2>
                    <span class="text-[10px] font-semibold text-slate-400">KNF-06</span>
                </div>

                <div class="space-y-2 text-xs">
                    @forelse($auditLogs as $log)
                        <div class="p-2 rounded-lg bg-slate-50/70 border border-slate-100 space-y-0.5">
                            <div class="text-[11px] font-medium text-slate-700 leading-snug">
                                {{ $log->action }}
                            </div>
                            <div class="text-[10px] text-slate-400 flex items-center justify-between">
                                <span>{{ $log->user->name ?? 'System' }}</span>
                                <span>{{ $log->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-2 text-center">Belum ada catatan log aktivitas.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- MODAL PENOLAKAN LAPORAN (ALPINE.JS) DENGAN TEXTAREA WAJIB -->
    <div x-show="rejectModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 transition-opacity" @click="rejectModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-slate-100 space-y-4">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5 text-rose-600">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Konfirmasi Penolakan Laporan</h3>
                    </div>
                    <button type="button" @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed">
                    Anda akan menolak laporan <strong>{{ $report->code }}</strong>. Silakan masukkan alasan penolakan secara spesifik (minimal 5 karakter). Alasan ini akan dikirimkan langsung ke pelapor melalui Push Notification.
                </p>

                <!-- Form Submit Patch Status Rejected -->
                <form action="{{ route('admin.reports.update-status', $report) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Rejected">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alasan Penolakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="rejection_reason" 
                                  rows="3" 
                                  x-model="rejectionReason"
                                  placeholder="Contoh: Foto bukti tidak menunjukkan genangan air di lokasi Tel-U, koordinat tidak valid..."
                                  class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition"
                                  required></textarea>
                        
                        <div class="flex items-center justify-between text-[11px] mt-1 text-slate-400">
                            <span>Minimal 5 karakter</span>
                            <span :class="isReasonValid ? 'text-emerald-600 font-semibold' : 'text-slate-400'" 
                                  x-text="rejectionReason.length + ' karakter'"></span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                        <button type="button" 
                                @click="rejectModalOpen = false" 
                                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                :disabled="!isReasonValid"
                                class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-ban text-xs"></i>
                            <span>Tolak Laporan Ini</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- LIGHTBOX MODAL PRATINJAU FOTO PENUH (ALPINE.JS) -->
    <div x-show="imageModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="fixed inset-0 bg-black/85 backdrop-blur-xs transition-opacity" @click="imageModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative max-w-4xl max-h-[90vh] bg-transparent">
                <button type="button" 
                        @click="imageModalOpen = false" 
                        class="absolute -top-10 right-0 text-white/80 hover:text-white text-xl">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="{{ $report->photo_url }}" 
                     alt="Foto Penuh {{ $report->code }}" 
                     class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl object-contain border border-white/20">
                <p class="text-white/80 text-center text-xs mt-3 font-medium">
                    Foto Bukti #{{ $report->code }} - {{ $report->title }}
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ $report->latitude ?? -6.9732 }};
        const lng = {{ $report->longitude ?? 107.6314 }};

        // Render Peta Leaflet Lokasi Laporan
        const map = L.map('detailMap', {
            zoomControl: true,
            scrollWheelZoom: false
        }).setView([lat, lng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Custom Icon / Marker
        const marker = L.marker([lat, lng]).addTo(map);

        marker.bindPopup(`
            <div class="text-xs font-sans">
                <b class="text-emerald-700 font-bold block">{{ $report->code }}</b>
                <span class="font-semibold text-slate-800">{{ $report->title }}</span><br>
                <span class="text-[10px] text-slate-500">Air: {{ $report->water_level_info['label'] }}</span>
            </div>
        `).openPopup();
    });
</script>
@endpush

