<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris Antisipasi Bencana - Telkom University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v9.2.4/ol.css">
    <style>
        #picker-map { width: 100%; height: 240px; border-radius: 8px; border: 1px solid #dee2e6; }
        .badge-gedung { background-color: #0d6efd; }
        .badge-taman { background-color: #198754; }
        .badge-lapangan { background-color: #fd7e14; }
    </style>
</head>
<body class="bg-light">

@php
    $isAdminOrDosen = auth()->check() && in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'dosen']);
@endphp

<nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('inventories.index') }}">🎒 Inventaris Bencana Tel-U</a>
        <div class="d-flex gap-2">
            <a href="{{ route('map.index') }}" class="btn btn-light btn-sm fw-semibold">🗺️ Buka view.map</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    {{-- Notifikasi Sukses / Error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM UPLOAD INVENTORY (HANYA ADMIN & DOSEN) --}}
    @if ($isAdminOrDosen)
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header bg-primary text-white fw-bold">
                ➕ Upload / Tambah Stok Inventaris (Admin & Dosen)
            </div>
            <div class="card-body">
                <form action="{{ route('inventories.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Nama Lokasi (Gedung / Taman / Lapangan)</label>
                                <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Gedung Bangkit / Taman GKU" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Tipe Lokasi</label>
                                <select name="location_type" class="form-select" required>
                                    <option value="gedung">Gedung</option>
                                    <option value="taman">Taman</option>
                                    <option value="lapangan">Lapangan</option>
                                </select>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Atribut</label>
                                    <select name="atribut" class="form-select" required>
                                        @foreach ($atributList as $attr)
                                            <option value="{{ $attr }}">{{ $attr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Jumlah</label>
                                    <input type="number" name="jumlah" min="1" value="1" class="form-control" required>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small text-muted">Latitude</label>
                                    <input type="text" id="lat-input" name="latitude" value="-6.9733000" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted">Longitude</label>
                                    <input type="text" id="lng-input" name="longitude" value="107.6305000" class="form-control form-control-sm" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Klik Peta Kampus untuk Set Titik Ikon BACKPACK</label>
                            <div id="picker-map"></div>
                            <small class="text-muted">Titik koordinat akan otomatis terisi dan langsung tampil di <code>view.map</code>.</small>
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary fw-semibold">Simpan & Sinkronkan ke Peta</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- FILTER TIPE LOKASI --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="mb-0 fw-bold">Daftar Lokasi Inventaris</h4>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('inventories.index') }}" class="btn {{ !request('location_type') ? 'btn-dark' : 'btn-outline-dark' }}">Semua</a>
            <a href="{{ route('inventories.index', ['location_type' => 'gedung']) }}" class="btn {{ request('location_type') === 'gedung' ? 'btn-dark' : 'btn-outline-dark' }}">Gedung</a>
            <a href="{{ route('inventories.index', ['location_type' => 'taman']) }}" class="btn {{ request('location_type') === 'taman' ? 'btn-dark' : 'btn-outline-dark' }}">Taman</a>
            <a href="{{ route('inventories.index', ['location_type' => 'lapangan']) }}" class="btn {{ request('location_type') === 'lapangan' ? 'btn-dark' : 'btn-outline-dark' }}">Lapangan</a>
        </div>
    </div>

    {{-- LIST LOKASI & STOK --}}
    @forelse ($inventories as $inventory)
        <div class="card shadow-sm mb-4" id="lokasi-{{ $inventory->id }}">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <span class="badge badge-{{ $inventory->location_type }} text-uppercase me-1">{{ $inventory->location_type }}</span>
                    <strong class="fs-5">🎒 {{ $inventory->lokasi }}</strong>
                    <small class="text-muted ms-2">({{ $inventory->latitude }}, {{ $inventory->longitude }})</small>
                </div>
                <button class="btn btn-warning btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalPakai{{ $inventory->id }}">
                    📝 Isi Form Pemakaian
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Atribut</th>
                                <th width="140">Stok Tersedia</th>
                                @if ($isAdminOrDosen)
                                    <th width="200">Update Stok (Admin/Dosen)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inventory->items as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->atribut }}</td>
                                    <td>
                                        <span class="badge {{ $item->jumlah > 0 ? 'bg-success' : 'bg-secondary' }} fs-6">
                                            {{ $item->jumlah }}
                                        </span>
                                    </td>
                                    @if ($isAdminOrDosen)
                                        <td>
                                            <form action="{{ route('inventories.items.update', $item->id) }}" method="POST" class="d-flex gap-1">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="jumlah" value="{{ $item->jumlah }}" min="0" class="form-control form-control-sm">
                                                <button type="submit" class="btn btn-outline-primary btn-sm">Simpan</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Riwayat Pemakaian di Lokasi Ini --}}
                @if ($inventory->usages->count() > 0)
                    <div class="mt-3">
                        <small class="fw-bold text-muted d-block mb-2">Riwayat Pengambilan / Pemakaian Terbaru:</small>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped small mb-0">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Nama (NIM)</th>
                                        <th>Atribut & Jumlah</th>
                                        <th>Alasan</th>
                                        <th>Foto Bukti</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventory->usages as $usage)
                                        <tr>
                                            <td>{{ $usage->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $usage->nama }} ({{ $usage->nim }})</td>
                                            <td>{{ $usage->item->atribut ?? '-' }} (<strong>{{ $usage->jumlah_diambil }}</strong>)</td>
                                            <td>{{ $usage->alasan }}</td>
                                            <td>
                                                <a href="{{ asset('storage/' . $usage->photo_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm py-0">Lihat Foto</a>
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
        <div class="modal fade" id="modalPakai{{ $inventory->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('inventories.usage.store', $inventory->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title fw-bold">Form Pemakaian — {{ $inventory->lokasi }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Atribut yang Diambil</label>
                                <select name="inventory_item_id" class="form-select" required>
                                    @foreach ($inventory->items as $item)
                                        <option value="{{ $item->id }}" {{ $item->jumlah == 0 ? 'disabled' : '' }}>
                                            {{ $item->atribut }} (Stok: {{ $item->jumlah }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Jumlah Diambil</label>
                                <input type="number" name="jumlah_diambil" min="1" value="1" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Nama</label>
                                <input type="text" name="nama" class="form-control" value="{{ auth()->user()->name ?? '' }}" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold">NIM (10 Digit Tel-U)</label>
                                <input type="text" name="nim" pattern="[0-9]{10}" maxlength="10" class="form-control" placeholder="1301213088" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Alasan Pemakaian</label>
                                <textarea name="alasan" rows="2" class="form-control" placeholder="Contoh: Evakuasi banjir di sekitar gedung..." required></textarea>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Foto Atribut (Max 10MB)</label>
                                <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning fw-bold">Kirim & Ambil Inventaris</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Belum ada data inventaris yang diunggah di lokasi mana pun.</div>
    @endforelse
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@if ($isAdminOrDosen)
<script src="https://cdn.jsdelivr.net/npm/ol@v9.2.4/dist/ol.js"></script>
<script>
    const teluCenter = ol.proj.fromLonLat([107.6305, -6.9733]);
    const markerSource = new ol.source.Vector();
    const markerFeature = new ol.Feature({ geometry: new ol.geom.Point(teluCenter) });
    markerSource.addFeature(markerFeature);

    const pickerMap = new ol.Map({
        target: 'picker-map',
        layers: [
            new ol.layer.Tile({ source: new ol.source.OSM() }),
            new ol.layer.Vector({
                source: markerSource,
                style: new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 7,
                        fill: new ol.style.Fill({ color: '#0d6efd' }),
                        stroke: new ol.style.Stroke({ color: '#fff', width: 2 })
                    })
                })
            })
        ],
        view: new ol.View({ center: teluCenter, zoom: 16 })
    });

    pickerMap.on('singleclick', function (evt) {
        const lonLat = ol.proj.toLonLat(evt.coordinate);
        markerFeature.getGeometry().setCoordinates(evt.coordinate);
        document.getElementById('lng-input').value = lonLat[0].toFixed(7);
        document.getElementById('lat-input').value = lonLat[1].toFixed(7);
    });
</script>
@endif

</body>
</html>