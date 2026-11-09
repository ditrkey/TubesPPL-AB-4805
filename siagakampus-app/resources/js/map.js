import Map from 'ol/Map';
import View from 'ol/View';
import TileLayer from 'ol/layer/Tile';
import OSM from 'ol/source/OSM';
import { fromLonLat, transformExtent } from 'ol/proj';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import Feature from 'ol/Feature';
import Point from 'ol/geom/Point';
import { Style, Icon } from 'ol/style';
import Overlay from 'ol/Overlay';
import 'ol/ol.css';
import 'flowbite';

document.addEventListener('DOMContentLoaded', () => {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;

    const rektoratCoords = [107.6306, -6.9740]; // [Lng, Lat] OpenLayers

    const campusExtent = transformExtent(
        [107.623680, -6.980678, 107.637885, -6.967664], // EPSG:4326 (WGS84)
        'EPSG:4326',
        'EPSG:3857' // Konversi ke proyeksi tampilan OpenLayers
    );

    // 1. Layer Titik Banjir (Ikon "X" Berwarna Merah)
    const floodSource = new VectorSource();
    const floodVectorLayer = new VectorLayer({
        source: floodSource,
        zIndex: 10,
    });

    // 2. Inisialisasi Peta OpenLayers
    const map = new Map({
        target: 'map',
        layers: [
            new TileLayer({
                source: new OSM()
            }),
            floodVectorLayer
        ],
        view: new View({
            center: fromLonLat(rektoratCoords),
            zoom: 16,
            minZoom: 14,
            maxZoom: 19,
            extent: campusExtent,
            constrainOnlyCenter: false,
            smoothExtentConstraint: false
        })
    });

    // 3. Ikon "X" Merah Berformat SVG (High Resolution & Standar OpenLayers)
    const svgRedX = 'data:image/svg+xml;utf8,' + encodeURIComponent(`
        <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
            <circle cx="19" cy="19" r="16" fill="#dc2626" stroke="#ffffff" stroke-width="2.5" />
            <line x1="12" y1="12" x2="26" y2="26" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round" />
            <line x1="26" y1="12" x2="12" y2="26" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round" />
        </svg>
    `);

    const redXStyle = new Style({
        image: new Icon({
            src: svgRedX,
            anchor: [0.5, 0.5],
            scale: 1,
        })
    });

    // 4. Inisialisasi Overlay Popup OpenLayers
    const popupContainer = document.getElementById('popup');
    const popupContent = document.getElementById('popup-content');
    const popupCloser = document.getElementById('popup-closer');

    const overlay = new Overlay({
        element: popupContainer,
        autoPan: {
            animation: { duration: 250 },
        },
    });
    map.addOverlay(overlay);

    if (popupCloser) {
        popupCloser.onclick = () => {
            overlay.setPosition(undefined);
            popupCloser.blur();
            return false;
        };
    }

    // 5. Fungsi Render Titik "X" Merah ke Peta
    function addFloodPointToMap(report) {
        // Ingat: OpenLayers membutuhkan urutan [Longitude, Latitude]
        const coordinate = fromLonLat([parseFloat(report.lng), parseFloat(report.lat)]);
        
        const feature = new Feature({
            geometry: new Point(coordinate),
            data: report
        });

        feature.setStyle(redXStyle);
        floodSource.addFeature(feature);
    }

    // 6. Ambil Data Titik Banjir dari Backend Laravel
    function fetchFloodPoints() {
        fetch('/api/reports/points')
            .then(res => res.json())
            .then(res => {
                floodSource.clear();
                if (res.data && Array.isArray(res.data)) {
                    res.data.forEach(report => addFloodPointToMap(report));
                }
            })
            .catch(err => console.error('Gagal mengambil titik banjir:', err));
    }

    fetchFloodPoints();

    // 7. Event Klik pada Ikon "X" Merah di Peta
    map.on('singleclick', (evt) => {
        const feature = map.forEachFeatureAtPixel(evt.pixel, (f) => f);

        if (feature) {
            const data = feature.get('data');
            const coordinates = feature.getGeometry().getCoordinates();

            popupContent.innerHTML = `
                <div>
                    <span class="inline-block bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded-full mb-1">✕ TITIK BANJIR AKTIF</span>
                    <h3 class="font-extrabold text-sm text-slate-900">${data.lokasi}</h3>
                    <p class="text-[11px] text-slate-500 mb-2">Pelapor: <strong>${data.nama}</strong> (${data.nim} - ${data.jurusan})</p>
                    ${data.photo_url ? `<img src="${data.photo_url}" class="w-full h-28 object-cover rounded-xl mb-2 border border-slate-200" alt="Bukti Banjir" />` : ''}
                    <div class="bg-red-50 p-2 rounded-lg border border-red-100 text-[11px] text-slate-700 italic mb-1">
                        "${data.pengaduan}"
                    </div>
                    <span class="text-[10px] text-slate-400 block text-right">${data.created_at || 'Baru saja'}</span>
                </div>
            `;

            popupContainer.classList.remove('hidden');
            overlay.setPosition(coordinates);
        } else {
            overlay.setPosition(undefined);
        }
    });

    // Ubah kursor pointer saat mouse hover di atas ikon "X"
    map.on('pointermove', (e) => {
        const hit = map.hasFeatureAtPixel(e.pixel);
        map.getTargetElement().style.cursor = hit ? 'pointer' : '';
    });

    // 8. Logika Modal Form Lapor Banjir (PBI 4)
    const reportModal = document.getElementById('reportModal');
    const btnOpen = document.getElementById('btnOpenReportModal');
    const btnClose = document.getElementById('btnCloseModal');
    const btnCancel = document.getElementById('btnCancelModal');
    const landmarkSelect = document.getElementById('landmarkSelect');

    if (btnOpen) btnOpen.onclick = () => reportModal.classList.remove('hidden');
    if (btnClose) btnClose.onclick = () => reportModal.classList.add('hidden');
    if (btnCancel) btnCancel.onclick = () => reportModal.classList.add('hidden');

    if (landmarkSelect) {
        landmarkSelect.onchange = function () {
            const opt = this.options[this.selectedIndex];
            document.getElementById('lokasi').value = opt.value;
            document.getElementById('location_type').value = opt.dataset.type;
            document.getElementById('latitude').value = opt.dataset.lat;
            document.getElementById('longitude').value = opt.dataset.lng;
        };
    }

    // 9. Validasi Sisi Browser & Kirim Laporan via Fetch (AJAX)
    const form = document.getElementById('formLaporBanjir');
    const nimInput = document.getElementById('nim');
    const photoInput = document.getElementById('photo');
    const nimError = document.getElementById('nimError');
    const photoError = document.getElementById('photoError');
    const btnSubmit = document.getElementById('btnSubmitReport');

    if (form) {
        form.onsubmit = async function (e) {
            e.preventDefault();
            let valid = true;

            // Validasi NIM Regex: ^[0-9]{10}$
            const nimRegex = /^[0-9]{10}$/;
            if (!nimRegex.test(nimInput.value.trim())) {
                nimError.classList.remove('hidden');
                valid = false;
            } else {
                nimError.classList.add('hidden');
            }

            // Validasi Ukuran Foto Maksimal 10MB (10 * 1024 * 1024 bytes)
            if (photoInput.files.length > 0) {
                const fileSize = photoInput.files[0].size;
                if (fileSize > 10 * 1024 * 1024) {
                    photoError.classList.remove('hidden');
                    valid = false;
                } else {
                    photoError.classList.add('hidden');
                }
            }

            if (!valid) return;

            btnSubmit.disabled = true;
            btnSubmit.innerText = 'Menyimpan & Menandai...';

            const formData = new FormData(form);

            try {
                const response = await fetch('/lapor-banjir', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    alert('Laporan berhasil disubmit! Titik banjir dengan ikon "X" merah telah ditandai di peta.');
                    
                    // Tambahkan langsung titik "X" merah baru ke peta OpenLayers
                    const newReport = {
                        id: result.data.id,
                        nama: result.data.nama,
                        nim: result.data.nim,
                        jurusan: result.data.jurusan,
                        lokasi: result.data.lokasi,
                        pengaduan: result.data.pengaduan,
                        lat: result.data.latitude,
                        lng: result.data.longitude,
                        photo_url: `/storage/${result.data.photo_path}`,
                        created_at: 'Baru saja'
                    };
                    addFloodPointToMap(newReport);

                    // Animasi flyTo ke titik banjir yang baru dilaporkan
                    map.getView().animate({
                        center: fromLonLat([parseFloat(newReport.lng), parseFloat(newReport.lat)]),
                        zoom: 17,
                        duration: 800
                    });

                    // Reset form & tutup modal
                    form.reset();
                    reportModal.classList.add('hidden');
                } else {
                    alert('Gagal menyimpan laporan: ' + (result.message || 'Periksa input Anda.'));
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat mengirim laporan.');
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerText = 'Submit & Tandai di Peta';
            }
        };
    }
});