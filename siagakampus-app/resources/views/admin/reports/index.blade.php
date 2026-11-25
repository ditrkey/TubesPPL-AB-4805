@extends('layouts.admintemp')

@section('title', 'Verifikasi Laporan Masuk (PBI-05)')
@section('page_category', 'Verifikasi & Laporan')
@section('page_title', 'Daftar Laporan Banjir')

@section('content')
<div class="space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-2.5">
                <span>Verifikasi Laporan Masuk</span>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                    Modul KF-05 / UC-05
                </span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Validasi dan alokasikan tindak lanjut laporan genangan air dari sivitas akademika secara real-time.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-sm shadow-emerald-700/20 transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Input Laporan Manual</span>
            </a>
        </div>
    </div>

    <!-- STATISTIK RINGKASAN (6 STATUS METRICS) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- Total Laporan -->
        <a href="{{ route('admin.reports.index') }}" 
           class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-slate-300 transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</span>
                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs group-hover:bg-slate-200 transition">
                    <i class="fa-solid fa-list-check"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900 mt-2">{{ $stats['total'] }}</p>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Semua laporan tercatat</span>
        </a>

        <!-- Pending (Amber) -->
        <a href="{{ route('admin.reports.index', ['status' => 'Pending']) }}" 
           class="p-4 rounded-2xl bg-white border border-amber-200/80 shadow-xs hover:border-amber-300 transition group {{ request('status') === 'Pending' ? 'ring-2 ring-amber-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Pending</span>
                <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs group-hover:bg-amber-100 transition">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600 mt-2">{{ $stats['pending'] }}</p>
            <span class="text-[11px] text-amber-700/70 mt-0.5 block">Menunggu verifikasi</span>
        </a>

        <!-- Verified (Blue / Sky) -->
        <a href="{{ route('admin.reports.index', ['status' => 'Verified']) }}" 
           class="p-4 rounded-2xl bg-white border border-sky-200/80 shadow-xs hover:border-sky-300 transition group {{ request('status') === 'Verified' ? 'ring-2 ring-sky-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-sky-700 uppercase tracking-wider">Verified</span>
                <div class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-xs group-hover:bg-sky-100 transition">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-sky-600 mt-2">{{ $stats['verified'] }}</p>
            <span class="text-[11px] text-sky-700/70 mt-0.5 block">Valid & siap diproses</span>
        </a>

        <!-- Handling (Indigo) -->
        <a href="{{ route('admin.reports.index', ['status' => 'Handling']) }}" 
           class="p-4 rounded-2xl bg-white border border-indigo-200/80 shadow-xs hover:border-indigo-300 transition group {{ request('status') === 'Handling' ? 'ring-2 ring-indigo-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-indigo-700 uppercase tracking-wider">Handling</span>
                <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs group-hover:bg-indigo-100 transition">
                    <i class="fa-solid fa-person-running"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-indigo-600 mt-2">{{ $stats['handling'] }}</p>
            <span class="text-[11px] text-indigo-700/70 mt-0.5 block">Sedang ditangani</span>
        </a>

        <!-- Resolved (Emerald) -->
        <a href="{{ route('admin.reports.index', ['status' => 'Resolved']) }}" 
           class="p-4 rounded-2xl bg-white border border-emerald-200/80 shadow-xs hover:border-emerald-300 transition group {{ request('status') === 'Resolved' ? 'ring-2 ring-emerald-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Resolved</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs group-hover:bg-emerald-100 transition">
                    <i class="fa-solid fa-check-double"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600 mt-2">{{ $stats['resolved'] }}</p>
            <span class="text-[11px] text-emerald-700/70 mt-0.5 block">Penanganan selesai</span>
        </a>

        <!-- Rejected (Rose) -->
        <a href="{{ route('admin.reports.index', ['status' => 'Rejected']) }}" 
           class="p-4 rounded-2xl bg-white border border-rose-200/80 shadow-xs hover:border-rose-300 transition group {{ request('status') === 'Rejected' ? 'ring-2 ring-rose-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Rejected</span>
                <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs group-hover:bg-rose-100 transition">
                    <i class="fa-solid fa-ban"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-rose-600 mt-2">{{ $stats['rejected'] }}</p>
            <span class="text-[11px] text-rose-700/70 mt-0.5 block">Ditolak / tidak valid</span>
        </a>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
        
        <!-- Status Pills Quick Filter -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
            <span class="text-slate-400 font-semibold uppercase tracking-wider text-[11px] pr-2 shrink-0">Filter Status:</span>
            
            <a href="{{ route('admin.reports.index', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}"
               class="px-3 py-1.5 rounded-full font-medium transition shrink-0 {{ !request('status') || request('status') === 'all' ? 'bg-[#0f2e28] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua ({{ $stats['total'] }})
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->except(['status', 'page']), ['status' => 'Pending'])) }}"
               class="px-3 py-1.5 rounded-full font-medium transition shrink-0 {{ request('status') === 'Pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                Pending ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->except(['status', 'page']), ['status' => 'Verified'])) }}"
               class="px-3 py-1.5 rounded-full font-medium transition shrink-0 {{ request('status') === 'Verified' ? 'bg-sky-600 text-white shadow-xs' : 'bg-sky-50 text-sky-700 hover:bg-sky-100' }}">
                Verified ({{ $stats['verified'] }})
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->except(['status', 'page']), ['status' => 'Handling'])) }}"
               class="px-3 py-1.5 rounded-full font-medium transition shrink-0 {{ request('status') === 'Handling' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}">
                Handling ({{ $stats['handling'] }})
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->except(['status', 'page']), ['status' => 'Resolved'])) }}"
               class="px-3 py-1.5 rounded-full font-medium transition shrink-0 {{ request('status') === 'Resolved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                Resolved ({{ $stats['resolved'] }})
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->except(['status', 'page']), ['status' => 'Rejected'])) }}"
               class="px-3 py-1.5 rounded-full font-medium transition shrink-0 {{ request('status') === 'Rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                Rejected ({{ $stats['rejected'] }})
            </a>
        </div>

        <!-- Form Pencarian & Parameter Filter -->
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <!-- Keyword Search -->
            <div class="md:col-span-6 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}"
                       placeholder="Cari ID laporan (LP-XXXX), nama pelapor, lokasi, atau deskripsi..."
                       class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
            </div>

            <!-- Kategori Dropdown -->
            <div class="md:col-span-3">
                <select name="category" 
                        class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-700">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Kejadian -->
            <div class="md:col-span-2">
                <input type="date" 
                       name="date" 
                       value="{{ request('date') }}"
                       class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-700">
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-1 flex items-center gap-1.5">
                <button type="submit" 
                        title="Terapkan Filter"
                        class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold transition flex items-center justify-center">
                    <i class="fa-solid fa-filter text-xs"></i>
                </button>
                @if(request()->hasAny(['q', 'category', 'date', 'status']))
                    <a href="{{ route('admin.reports.index') }}" 
                       title="Reset Filter"
                       class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm transition flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABEL DAFTAR LAPORAN -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">ID Laporan</th>
                        <th class="py-3.5 px-4">Pelapor</th>
                        <th class="py-3.5 px-4">Titik & Zona Lokasi</th>
                        <th class="py-3.5 px-4 text-center">Bukti Foto</th>
                        <th class="py-3.5 px-4">Kategori & Air</th>
                        <th class="py-3.5 px-4">Waktu Kirim</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50/80 transition group">
                            <!-- ID Laporan -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-mono font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 text-xs">
                                    {{ $report->code }}
                                </span>
                            </td>

                            <!-- Pelapor & Role -->
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-900 leading-snug">
                                    {{ $report->user->name ?? 'Anonim' }}
                                </div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <span class="inline-block px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 font-medium">
                                        {{ $report->user->role ?? 'Warga Tel-U' }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $report->user->phone ?? '-' }}</span>
                                </div>
                            </td>

                            <!-- Titik & Zona Lokasi -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="font-medium text-slate-900 truncate" title="{{ $report->title }}">
                                    {{ $report->title }}
                                </div>
                                <div class="text-xs text-slate-500 flex items-center gap-1.5 mt-0.5">
                                    @if($report->zone)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                                            {{ $report->zone->name }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">Luar Zona Terpetakan</span>
                                    @endif

                                    @if($report->latitude && $report->longitude)
                                        <span class="font-mono text-[10px] text-slate-400" title="Koordinat GPS">
                                            [{{ number_format($report->latitude, 4) }}, {{ number_format($report->longitude, 4) }}]
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Thumbnail Bukti Foto -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-block relative group/thumb">
                                    <img src="{{ $report->photo_url }}" 
                                         alt="Foto {{ $report->code }}" 
                                         class="w-12 h-12 object-cover rounded-xl border border-slate-200 shadow-xs cursor-pointer group-hover/thumb:scale-105 transition">
                                </div>
                            </td>

                            <!-- Kategori & Ketinggian Air -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-block text-xs font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">
                                    {{ $report->category }}
                                </span>
                                <div class="mt-1">
                                    @php $water = $report->water_level_info; @endphp
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $water['badge_class'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $water['dot_class'] }}"></span>
                                        {{ $water['short'] }}
                                    </span>
                                </div>
                            </td>

                            <!-- Waktu Kirim -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-slate-700">
                                    {{ $report->created_at->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $report->created_at->format('H:i') }} WIB
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @php $badge = $report->status_badge; @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge['badge_class'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot_class'] }}"></span>
                                    {{ $badge['pill'] }}
                                </span>
                            </td>

                            <!-- Tombol Aksi -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Tinjau / Detail -->
                                    <a href="{{ route('admin.reports.show', $report) }}" 
                                       title="Tinjau Detail Laporan"
                                       class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white font-semibold text-xs transition border border-emerald-200/80 flex items-center gap-1">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>Tinjau</span>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('admin.reports.edit', $report) }}" 
                                       title="Koreksi Data Laporan"
                                       class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>

                                    <!-- Hapus (Soft Delete) -->
                                    <form action="{{ route('admin.reports.destroy', $report) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan {{ $report->code }}? Aksi ini akan dicatat ke audit log.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus Laporan" 
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="max-w-sm mx-auto text-center space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                                        <i class="fa-solid fa-inbox"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">Tidak ada laporan yang sesuai kriteria</p>
                                    <p class="text-xs text-slate-400">Silakan sesuaikan kata kunci pencarian atau ubah filter status di bagian atas.</p>
                                    <a href="{{ route('admin.reports.index') }}" class="inline-block text-xs text-emerald-600 font-semibold hover:underline">
                                        Reset Semua Filter
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($reports->hasPages())
            <div class="p-4 border-t border-slate-200/80 bg-slate-50/50">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

