@extends('layouts.admin')

@section('title', 'Koreksi Data Laporan ' . $report->code)
@section('page_category', 'Verifikasi & Laporan')
@section('page_title', 'Edit Laporan ' . $report->code)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.show', $report) }}" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Koreksi Data Laporan <span class="font-mono text-emerald-700">#{{ $report->code }}</span>
                </h1>
            </div>
            <p class="text-sm text-slate-500 mt-1 pl-6">
                Perbaiki rincian judul, kategori, elevasi genangan air, zona kampus, atau penempatan titik koordinat spasial.
            </p>
        </div>

        <a href="{{ route('admin.reports.show', $report) }}" 
           class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-sm transition">
            Batal
        </a>
    </div>

    <!-- MAIN FORM -->
    <form action="{{ route('admin.reports.update', $report) }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="space-y-6"
          x-data="{
              photoPreview: null,
              waterLevel: '{{ old('water_level', $report->water_level) }}',
              handlePhotoChange(event) {
                  const file = event.target.files[0];
                  if (file) {
                      this.photoPreview = URL.createObjectURL(file);
                  }
              }
          }">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- KOLOM KIRI: DETAIL LAPORAN & FOTO -->
            <div class="lg:col-span-7 space-y-5">
                
                <!-- Kartu Informasi Dasar -->
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-file-pen text-emerald-600"></i>
                        <span>Rincian Laporan Genangan</span>
                    </h2>

                    <!-- Judul Laporan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Judul / Ringkasan Kejadian <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               value="{{ old('title', $report->title) }}" 
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition @error('title') border-rose-500 @enderror"
                               required>
                        @error('title')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori & Zona Rawan Kampus -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Kategori -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kategori Kejadian <span class="text-rose-500">*</span>
                            </label>
                            <select name="category" 
                                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-800"
                                    required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $report->category) === $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Zona Terkait -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Zona Rawan Kampus
                            </label>
                            <select name="zone_id" 
                                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-800">
                                <option value="">-- Pilih Zona (Opsional) --</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ old('zone_id', $report->zone_id) == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }} (Risiko {{ ucfirst($zone->risk_level) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('zone_id')
                                <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Tingkat Ketinggian Air (Water Level) -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                            Estimasi Ketinggian Air <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            <!-- Low -->
                            <label class="cursor-pointer border rounded-xl p-3 flex flex-col items-center text-center transition"
                                   :class="waterLevel === 'Low' ? 'border-emerald-500 bg-emerald-50/50 ring-2 ring-emerald-500/20' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="water_level" value="Low" class="sr-only" x-model="waterLevel">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 mb-1.5"></span>
                                <span class="text-xs font-bold text-slate-800">Rendah</span>
                                <span class="text-[11px] text-slate-500">&lt; 15 cm</span>
                            </label>

                            <!-- Med -->
                            <label class="cursor-pointer border rounded-xl p-3 flex flex-col items-center text-center transition"
                                   :class="waterLevel === 'Med' ? 'border-amber-500 bg-amber-50/50 ring-2 ring-amber-500/20' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="water_level" value="Med" class="sr-only" x-model="waterLevel">
                                <span class="w-3 h-3 rounded-full bg-amber-500 mb-1.5"></span>
                                <span class="text-xs font-bold text-slate-800">Sedang</span>
                                <span class="text-[11px] text-slate-500">15 - 40 cm</span>
                            </label>

                            <!-- High -->
                            <label class="cursor-pointer border rounded-xl p-3 flex flex-col items-center text-center transition"
                                   :class="waterLevel === 'High' ? 'border-rose-500 bg-rose-50/50 ring-2 ring-rose-500/20' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="water_level" value="High" class="sr-only" x-model="waterLevel">
                                <span class="w-3 h-3 rounded-full bg-rose-500 mb-1.5"></span>
                                <span class="text-xs font-bold text-slate-800">Tinggi</span>
                                <span class="text-[11px] text-slate-500">&gt; 40 cm</span>
                            </label>
                        </div>
                        @error('water_level')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi Detail -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Deskripsi Lengkap Kondisi Genangan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="description" 
                                  rows="4" 
                                  class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition @error('description') border-rose-500 @enderror"
                                  required>{{ old('description', $report->description) }}</textarea>
                        @error('description')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kartu Perbarui Foto Bukti (Opsional) -->
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-emerald-600"></i>
                        <span>Foto Bukti Kejadian</span>
                    </h2>

                    <!-- Tampilan Foto Saat Ini -->
                    <div class="flex items-center gap-4 p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <img src="{{ $report->photo_url }}" 
                             alt="Foto Saat Ini" 
                             class="w-16 h-16 object-cover rounded-lg border border-slate-300 shrink-0">
                        <div class="text-xs text-slate-600">
                            <span class="font-bold block text-slate-800">Foto Tersimpan Saat Ini</span>
                            <span class="text-[11px] text-slate-400">Jika tidak ingin mengganti foto, kosongkan input di bawah.</span>
                        </div>
                    </div>

                    <!-- File input -->
                    <div class="border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-5 text-center transition cursor-pointer relative bg-slate-50/50">
                        <input type="file" 
                               name="photo" 
                               id="photo" 
                               accept="image/jpeg,image/png,image/jpg" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               @change="handlePhotoChange($event)">
                        
                        <div x-show="!photoPreview" class="space-y-1">
                            <p class="text-xs font-semibold text-slate-700">Pilih foto baru untuk mengganti foto saat ini</p>
                            <p class="text-[11px] text-slate-400">Maksimal 5 MB (JPG, PNG)</p>
                        </div>

                        <div x-show="photoPreview" class="space-y-2" style="display: none;">
                            <img :src="photoPreview" alt="Pratinjau Foto Baru" class="max-h-48 mx-auto rounded-xl shadow-md border border-slate-200 object-contain">
                            <p class="text-xs text-emerald-700 font-semibold">Foto baru dipilih dan siap disimpan.</p>
                        </div>
                    </div>
                    @error('photo')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- KOLOM KANAN: PETA INTERAKTIF KOORDINAT (Leaflet.js) -->
            <div class="lg:col-span-5 space-y-5">
                
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-map-pin text-rose-500"></i>
                            <span>Koreksi Titik Koordinat</span>
                        </h2>
                        <span class="text-[11px] font-semibold text-slate-400">SRID 4326</span>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Geser marker merah atau klik di area peta untuk menyesuaikan lokasi genangan air.
                    </p>

                    <!-- Peta Container -->
                    <div class="rounded-xl overflow-hidden border border-slate-200 shadow-inner relative">
                        <div id="pickerMap" class="w-full h-80 z-0 bg-slate-100"></div>
                    </div>

                    <!-- Input Koordinat Spasial -->
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                                Latitude
                            </label>
                            <input type="text" 
                                   name="latitude" 
                                   id="latitude" 
                                   value="{{ old('latitude', $report->latitude ?? -6.9732) }}" 
                                   class="w-full px-3 py-2 text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-800 font-semibold"
                                   readonly
                                   required>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                                Longitude
                            </label>
                            <input type="text" 
                                   name="longitude" 
                                   id="longitude" 
                                   value="{{ old('longitude', $report->longitude ?? 107.6314) }}" 
                                   class="w-full px-3 py-2 text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-800 font-semibold"
                                   readonly
                                   required>
                        </div>
                    </div>

                </div>

                <!-- Submit Button Card -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                    <a href="{{ route('admin.reports.show', $report) }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-700/20 transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const curLat = parseFloat(document.getElementById('latitude').value) || -6.9732;
        const curLng = parseFloat(document.getElementById('longitude').value) || 107.6314;

        // Inisialisasi Peta Leaflet dengan koordinat laporan saat ini
        const map = L.map('pickerMap', {
            zoomControl: true,
            scrollWheelZoom: false
        }).setView([curLat, curLng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([curLat, curLng], {
            draggable: true
        }).addTo(map);

        marker.bindPopup('<b class="text-xs">Lokasi Laporan #{{ $report->code }}</b><br><span class="text-[11px] text-slate-500">Geser atau klik peta untuk mengoreksi posisi.</span>').openPopup();

        function updateCoordinates(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
        }

        marker.on('dragend', function (e) {
            const pos = marker.getLatLng();
            updateCoordinates(pos.lat, pos.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
            marker.openPopup();
        });
    });
</script>
@endpush

