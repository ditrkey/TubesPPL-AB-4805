@extends('layouts.admintemp')

@section('title', 'Manajemen Inventaris')

@php
    $role = strtolower(auth()->user()->role ?? '');
    $canManageInventory = auth()->check() && in_array($role, ['admin', 'dosen', 'superadmin']);
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v9.2.4/ol.css">
    <style>
        #picker-map {
            width: 100%;
            height: 320px;
            border-radius: 0.75rem;
            border: 1px solid #e4e4e7;
        }
    </style>
@endpush

@section('content')
<div
    x-data="{ activeModalId: null }"
    @keydown.escape.window="activeModalId = null"
    class="max-w-7xl mx-auto pb-10 space-y-6"
>
    {{-- Notifikasi Sukses / Error --}}
    @if (session('success'))
        <div
            x-data="{ show: true }"
            x-show="show"
            class="flex items-center justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
            role="alert"
        >
            <span>{{ session('success') }}</span>
            <button
                type="button"
                @click="show = false"
                aria-label="Tutup"
                class="rounded-lg px-2 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-100"
            >
                ✕
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <strong class="font-semibold">Terjadi kesalahan:</strong>
            <ul class="mt-1 list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM UPLOAD INVENTORY (ADMIN, DOSEN, SUPERADMIN) --}}
    @if ($canManageInventory)
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-zinc-200 bg-blue-600 px-5 py-3.5 text-sm font-bold text-white">
                Upload / Tambah Stok Inventaris
            </div>

            <div class="p-5">
                <form action="{{ route('inventories.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                        <div class="md:col-span-5 space-y-3">
                            <div>
                                <label for="lokasi-input" class="block text-sm font-semibold text-zinc-700 mb-1">
                                    Nama Lokasi (Gedung / Taman / Lapangan)
                                </label>
                                <input
                                    id="lokasi-input"
                                    type="text"
                                    name="lokasi"
                                    value="{{ old('lokasi') }}"
                                    class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                    placeholder="Contoh: Gedung Bangkit / Taman GKU"
                                    required
                                >
                            </div>

                            <div>
                                <label for="location-type-select" class="block text-sm font-semibold text-zinc-700 mb-1">
                                    Tipe Lokasi
                                </label>
                                <select
                                    id="location-type-select"
                                    name="location_type"
                                    class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                    required
                                >
                                    <option value="gedung" @selected(old('location_type') === 'gedung')>Gedung</option>
                                    <option value="taman" @selected(old('location_type') === 'taman')>Taman</option>
                                    <option value="lapangan" @selected(old('location_type') === 'lapangan')>Lapangan</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="atribut-select" class="block text-sm font-semibold text-zinc-700 mb-1">
                                        Atribut
                                    </label>
                                    <select
                                        id="atribut-select"
                                        name="atribut"
                                        class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                        required
                                    >
                                        @foreach ($atributList as $attr)
                                            <option value="{{ $attr }}" @selected(old('atribut') === $attr)>{{ $attr }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="jumlah-input" class="block text-sm font-semibold text-zinc-700 mb-1">
                                        Jumlah
                                    </label>
                                    <input
                                        id="jumlah-input"
                                        type="number"
                                        name="jumlah"
                                        min="1"
                                        value="{{ old('jumlah', 1) }}"
                                        class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="lat-input" class="block text-xs font-medium text-zinc-500 mb-1">
                                        Latitude
                                    </label>
                                    <input
                                        id="lat-input"
                                        type="text"
                                        name="latitude"
                                        value="{{ old('latitude', '-6.9733000') }}"
                                        class="block w-full rounded-lg border border-zinc-300 bg-white px-2.5 py-1.5 text-xs text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                        required
                                    >
                                </div>

                                <div>
                                    <label for="lng-input" class="block text-xs font-medium text-zinc-500 mb-1">
                                        Longitude
                                    </label>
                                    <input
                                        id="lng-input"
                                        type="text"
                                        name="longitude"
                                        value="{{ old('longitude', '107.6305000') }}"
                                        class="block w-full rounded-lg border border-zinc-300 bg-white px-2.5 py-1.5 text-xs text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-7 flex flex-col">
                            <label class="block text-sm font-semibold text-zinc-700 mb-1">
                                Klik Peta Kampus untuk Set Titik Ikon BACKPACK
                            </label>
                            <div id="picker-map"></div>
                            <span class="mt-1.5 text-xs text-zinc-500">
                                Titik koordinat akan otomatis terisi dan langsung tampil di peta.
                            </span>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        >
                            Simpan &amp; Sinkronkan ke Peta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- FILTER TIPE LOKASI --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h4 class="text-lg font-bold text-zinc-900">Daftar Lokasi Inventaris</h4>

        <div class="inline-flex rounded-lg border border-zinc-200 bg-white p-1 shadow-2xs">
            <a
                href="{{ route('inventories.index') }}"
                class="rounded-md px-3 py-1.5 text-xs font-semibold transition-colors {{ !request('location_type') ? 'bg-zinc-900 text-white' : 'text-zinc-700 hover:bg-zinc-100' }}"
            >
                Semua
            </a>
            <a
                href="{{ route('inventories.index', ['location_type' => 'gedung']) }}"
                class="rounded-md px-3 py-1.5 text-xs font-semibold transition-colors {{ request('location_type') === 'gedung' ? 'bg-zinc-900 text-white' : 'text-zinc-700 hover:bg-zinc-100' }}"
            >
                Gedung
            </a>
            <a
                href="{{ route('inventories.index', ['location_type' => 'taman']) }}"
                class="rounded-md px-3 py-1.5 text-xs font-semibold transition-colors {{ request('location_type') === 'taman' ? 'bg-zinc-900 text-white' : 'text-zinc-700 hover:bg-zinc-100' }}"
            >
                Taman
            </a>
            <a
                href="{{ route('inventories.index', ['location_type' => 'lapangan']) }}"
                class="rounded-md px-3 py-1.5 text-xs font-semibold transition-colors {{ request('location_type') === 'lapangan' ? 'bg-zinc-900 text-white' : 'text-zinc-700 hover:bg-zinc-100' }}"
            >
                Lapangan
            </a>
        </div>
    </div>

    {{-- LIST LOKASI & STOK --}}
    @forelse ($inventories as $inventory)
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm overflow-hidden" id="lokasi-{{ $inventory->id }}">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-200 bg-white px-5 py-3.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-zinc-700 border border-zinc-200">
                        {{ $inventory->location_type }}
                    </span>
                    <strong class="text-base font-bold text-zinc-900">{{ $inventory->lokasi }}</strong>
                    <span class="text-xs text-zinc-500">({{ $inventory->latitude }}, {{ $inventory->longitude }})</span>
                </div>

                <button
                    type="button"
                    @click="activeModalId = {{ $inventory->id }}"
                    class="inline-flex items-center rounded-lg bg-amber-400 px-3 py-1.5 text-xs font-semibold text-zinc-950 hover:bg-amber-500 transition-colors"
                >
                    Isi Form Pemakaian
                </button>
            </div>

            <div class="p-5 space-y-4">
                <div class="overflow-x-auto rounded-lg border border-zinc-200">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-wider text-zinc-600">
                            <tr>
                                <th class="px-4 py-2.5">Jenis Atribut</th>
                                <th class="px-4 py-2.5 w-40">Stok Tersedia</th>
                                @if ($canManageInventory)
                                    <th class="px-4 py-2.5 w-56">Update Stok</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 bg-white">
                            @foreach ($inventory->items as $item)
                                <tr>
                                    <td class="px-4 py-2.5 font-semibold text-zinc-800">{{ $item->atribut }}</td>
                                    <td class="px-4 py-2.5">
                                        <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-bold {{ $item->jumlah > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-100 text-zinc-600' }}">
                                            {{ $item->jumlah }}
                                        </span>
                                    </td>
                                    @if ($canManageInventory)
                                        <td class="px-4 py-2.5">
                                            <form action="{{ route('inventories.items.update', $item->id) }}" method="POST" class="flex items-center gap-1.5">
                                                @csrf
                                                @method('PATCH')
                                                <input
                                                    type="number"
                                                    name="jumlah"
                                                    value="{{ $item->jumlah }}"
                                                    min="0"
                                                    class="w-24 rounded-lg border border-zinc-300 bg-white px-2 py-1 text-xs text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                                    required
                                                >
                                                <button
                                                    type="submit"
                                                    class="rounded-lg border border-blue-600 px-2.5 py-1 text-xs font-semibold text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                                >
                                                    Simpan
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Riwayat Pemakaian di Lokasi Ini --}}
                @if ($inventory->usages->isNotEmpty())
                    <div class="pt-1">
                        <span class="block text-xs font-bold text-zinc-500 mb-2">
                            Riwayat Pengambilan / Pemakaian Terbaru:
                        </span>
                        <div class="overflow-x-auto rounded-lg border border-zinc-200">
                            <table class="min-w-full divide-y divide-zinc-200 text-xs">
                                <thead class="bg-zinc-50 text-left font-semibold text-zinc-600">
                                    <tr>
                                        <th class="px-3 py-2">Waktu</th>
                                        <th class="px-3 py-2">Nama (NIM)</th>
                                        <th class="px-3 py-2">Atribut &amp; Jumlah</th>
                                        <th class="px-3 py-2">Alasan</th>
                                        <th class="px-3 py-2">Foto Bukti</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 bg-white">
                                    @foreach ($inventory->usages as $usage)
                                        <tr class="odd:bg-white even:bg-zinc-50/50">
                                            <td class="px-3 py-2 text-zinc-600 whitespace-nowrap">
                                                {{ optional($usage->created_at)->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-3 py-2 text-zinc-800">{{ $usage->nama }} ({{ $usage->nim }})</td>
                                            <td class="px-3 py-2 text-zinc-800">
                                                {{ $usage->item->atribut ?? '-' }} (<strong class="font-bold">{{ $usage->jumlah_diambil }}</strong>)
                                            </td>
                                            <td class="px-3 py-2 text-zinc-700">{{ $usage->alasan }}</td>
                                            <td class="px-3 py-2">
                                                @if ($usage->photo_path)
                                                    <a
                                                        href="{{ asset('storage/' . $usage->photo_path) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="inline-flex items-center rounded-md border border-zinc-300 bg-white px-2 py-0.5 text-xs font-medium text-zinc-700 hover:bg-zinc-100"
                                                    >
                                                        Lihat Foto
                                                    </a>
                                                @else
                                                    <span class="text-zinc-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL FORM PEMAKAIAN --}}
        <div
            x-show="activeModalId === {{ $inventory->id }}"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            <div
                @click.outside="activeModalId = null"
                class="w-full max-w-lg rounded-xl border border-zinc-200 bg-white shadow-2xl overflow-hidden"
            >
                <form action="{{ route('inventories.usage.store', $inventory->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex items-center justify-between bg-amber-400 px-5 py-3.5">
                        <h5 class="text-sm font-bold text-zinc-950">Form Pemakaian — {{ $inventory->lokasi }}</h5>
                        <button
                            type="button"
                            @click="activeModalId = null"
                            aria-label="Tutup"
                            class="rounded-lg px-2 py-1 text-xs font-bold text-zinc-900 hover:bg-amber-500/50"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="p-5 space-y-3 text-sm">
                        <div>
                            <label class="block font-semibold text-zinc-700 mb-1">Atribut yang Diambil</label>
                            <select
                                name="inventory_item_id"
                                class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                required
                            >
                                @foreach ($inventory->items as $item)
                                    <option value="{{ $item->id }}" @disabled($item->jumlah <= 0)>
                                        {{ $item->atribut }} (Stok: {{ $item->jumlah }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 mb-1">Jumlah Diambil</label>
                            <input
                                type="number"
                                name="jumlah_diambil"
                                min="1"
                                value="1"
                                class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                required
                            >
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 mb-1">Nama</label>
                            <input
                                type="text"
                                name="nama"
                                class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                value="{{ old('nama', auth()->user()->name ?? '') }}"
                                required
                            >
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 mb-1">NIM (10 Digit Tel-U)</label>
                            <input
                                type="text"
                                name="nim"
                                pattern="[0-9]{10}"
                                maxlength="10"
                                class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                value="{{ old('nim') }}"
                                placeholder="1301213088"
                                required
                            >
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 mb-1">Alasan Pemakaian</label>
                            <textarea
                                name="alasan"
                                rows="2"
                                class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                placeholder="Contoh: Evakuasi banjir di sekitar gedung..."
                                required
                            >{{ old('alasan') }}</textarea>
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 mb-1">Foto Atribut (Max 10MB)</label>
                            <input
                                type="file"
                                name="photo"
                                accept="image/jpeg,image/png,image/webp"
                                class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-sm text-zinc-700 file:mr-3 file:rounded-md file:border-0 file:bg-zinc-100 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-zinc-700 hover:file:bg-zinc-200"
                                required
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-zinc-200 bg-zinc-50 px-5 py-3">
                        <button
                            type="button"
                            @click="activeModalId = null"
                            class="rounded-lg border border-zinc-300 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="rounded-lg bg-amber-400 px-4 py-2 text-xs font-bold text-zinc-950 hover:bg-amber-500 transition-colors"
                        >
                            Kirim &amp; Ambil Inventaris
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
            Belum ada data inventaris yang diunggah di lokasi mana pun.
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
    @if ($canManageInventory)
        @vite('resources/js/map.js')
    @endif
@endpush