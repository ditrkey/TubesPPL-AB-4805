@extends('layouts.admin')

@section('title', 'Input Laporan Banjir Manual')
@section('page_category', 'Verifikasi & Laporan')
@section('page_title', 'Input Laporan Manual')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- PAGE HEADER -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Input Laporan Banjir Manual</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1 pl-6">
                Catat insiden luapan air / genangan dari laporan posko, panggilan darurat, atau pantauan visual petugas di lapangan.
            </p>
        </div>

        <a href="{{ route('admin.reports.index') }}" 
           class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-sm transition">
            Batal
        </a>
    </div>

    <!-- MAIN FORM -->
    <form action="{{ route('admin.reports.store') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="space-y-6"
          x-data="{
              photoPreview: null,
              waterLevel: '{{ old('water_level', 'Low') }}',
              handlePhotoChange(event) {
                  const file = event.target.files[0];
                  if (file) {
                      this.photoPreview = URL.createObjectURL(file);
                  }
              }
          }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- KOLOM KIRI: DETAIL LAPORAN & FOTO -->
            <div class="lg:col-span-7 space-y-5">
                
                <!-- Kartu Informasi Dasar -->
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-file-lines text-emerald-600"></i>
                        <span>Informasi Insiden Genangan</span>
                    </h2>

                    <!-- Judul Laporan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Judul / Ringkasan Kejadian <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               value="{{ old('title') }}" 
                               placeholder="Contoh: Genangan Air 30 cm di Gerbang Depan Tel-U"
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
                                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>
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
                                    <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
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

                    <!-- Status Awal -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Status Penetapan Awal
                        </label>
                        <select name="status" 
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition text-slate-800">
                            <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>Pending (Menunggu Verifikasi Tambahan)</option>
                            <option value="Verified" {{ old('status', 'Verified') === 'Verified' ? 'selected' : '' }}>Verified (Langsung Diverifikasi & Valid)</option>
                        </select>
                    </div>

                    <!-- Deskripsi Detail -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Deskripsi Lengkap Kondisi Genangan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="description" 
                                  rows="4" 
                                  placeholder="Jelaskan kondisi air, akses jalan yang tertutup, potensi bahaya, atau bantuan logistik yang diperlukan..."
                                  class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition @error('description') border-rose-500 @enderror"
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kartu Upload Foto Bukti -->
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-emerald-600"></i>
                        <span>Unggah Foto Bukti Kejadian <span class="text-rose-500">*</span></span>
                    </h2>

                    <div class="border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-6 text-center transition cursor-pointer relative bg-slate-50/50">
                        <input type="file" 
                               name="photo" 
                               id="photo" 
                               accept="image/jpeg,image/png,image/jpg" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               @change="handlePhotoChange($event)"
                               required>
                        
                        <div x-show="!photoPreview" class="space-y-2">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-xl">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Tarik foto bukti ke sini atau klik untuk memilih file</p>
                            <p class="text-xs text-slate-400">Mendukung format JPG, JPEG, PNG (Maksimal 5 MB)</p>
                        </div>

                        <!-- Live Photo Preview -->
                        <div x-show="photoPreview" class="space-y-3" style="display: none;">
                            <img :src="photoPreview" alt="Pratinjau Foto" class="max-h-60 mx-auto rounded-xl shadow-md border border-slate-200 object-contain">
                            <p class="text-xs text-emerald-700 font-semibold flex items-center justify-center gap-1">
                                <i class="fa-solid fa-circle-check"></i>
                                Foto siap diunggah. Klik area ini jika ingin mengganti.
                            </p>
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
                            <span>Penetapan Koordinat Spasial</span>
                        </h2>
                        <span class="text-[11px] font-semibold text-slate-400">SRID 4326</span>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Klik pada peta atau geser marker untuk menentukan titik koordinat presisi genangan air di kawasan kampus.
                    </p>

                    <!-- Peta Container -->
                    <div class="rounded-xl overflow-hidden border border-slate-200 shadow-inner relative">
                        <div id="pickerMap" class="w-full h-80 z-0 bg-slate-100"></div>
                        <div class="absolute top-2 right-2 z-10">
                            <button type="button" 
                                    id="btnResetCenter"
                                    class="px-2.5 py-1 bg-white/95 hover:bg-white text-slate-700 text-xs font-medium rounded-lg shadow-sm border border-slate-200 transition">
                                <i class="fa-solid fa-location-crosshairs mr-1 text-emerald-600"></i> Kampus Utama
                            </button>
                        </div>
                    </div>

                    <!-- Input Koordinat Spasial -->
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                                Latitude (Garis Lintang)
                            </label>
                            <input type="text" 
                                   name="latitude" 
                                   id="latitude" 
                                   value="{{ old('latitude', $defaultLat) }}" 
                                   class="w-full px-3 py-2 text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-800 font-semibold"
                                   readonly
                                   required>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                                Longitude (Garis Bujur)
                            </label>
                            <input type="text" 
                                   name="longitude" 
                                   id="longitude" 
                                   value="{{ old('longitude', $defaultLng) }}" 
                                   class="w-full px-3 py-2 text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-800 font-semibold"
                                   readonly
                                   required>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 flex items-start gap-2">
                        <i class="fa-solid fa-circle-info text-emerald-600 mt-0.5"></i>
                        <span>Koordinat otomatis disimpan ke kolom <code>location</code> dengan tipe data <strong>MySQL Spatial POINT</strong>.</span>
                    </div>

                </div>

                <!-- Submit Button Card -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                    <a href="{{ route('admin.reports.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-700/20 transition flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                        <span>Simpan Laporan</span>
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
        const defaultLat = parseFloat(document.getElementById('latitude').value) || -6.9732;
        const defaultLng = parseFloat(document.getElementById('longitude').value) || 107.6314;

        // Inisialisasi Peta Leaflet
        const map = L.map('pickerMap', {
            zoomControl: true,
            scrollWheelZoom: false
        }).setView([defaultLat, defaultLng], 16);

        // Tile layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Marker kustom yang bisa digeser (draggable)
        let marker = L.marker([defaultLat, defaultLng], {
            draggable: true
        }).addTo(map);

        marker.bindPopup('<b class="text-xs">Titik Genangan Banjir</b><br><span class="text-[11px] text-slate-500">Geser atau klik peta untuk mengubah lokasi.</span>').openPopup();

        // Update input ketika marker digeser
        function updateCoordinates(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
        }

        marker.on('dragend', function (e) {
            const position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
        });

        // Pindah marker ketika peta diklik
        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
            marker.openPopup();
        });

        // Tombol Reset ke Tel-U Landmark
        const btnReset = document.getElementById('btnResetCenter');
        if (btnReset) {
            btnReset.addEventListener('click', function () {
                const landmark = [-6.9732, 107.6314];
                map.setView(landmark, 16);
                marker.setLatLng(landmark);
                updateCoordinates(landmark[0], landmark[1]);
                marker.openPopup();
            });
        }
    });
</script>
@endpush

