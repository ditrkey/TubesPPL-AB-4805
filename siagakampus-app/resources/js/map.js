import Map from 'ol/Map';
import View from 'ol/View';
import TileLayer from 'ol/layer/Tile';
import OSM from 'ol/source/OSM';
import { fromLonLat, transformExtent } from 'ol/proj';
import 'ol/ol.css';
import 'flowbite';

document.addEventListener('DOMContentLoaded', () => {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;
    const rektoratCoords = [107.6306, -6.9740];

    const campusExtent = transformExtent(
        [107.623680,-6.980678,107.637885,-6.967664], // EPSG:4326 (WGS84)
        'EPSG:4326',
        'EPSG:3857' // Konversi ke proyeksi tampilan OpenLayers
    );

    // Inisialisasi Peta OpenLayers Dasar
    const map = new Map({
        target: 'map',
        layers: [
            new TileLayer({
                source: new OSM()
            })
        ],
view: new View({
            center: fromLonLat(rektoratCoords),
            zoom: 16,
            minZoom: 1,             // Cegah pengguna zoom-out terlalu jauh sampai luar Bandung
            maxZoom: 19,             // Batas maksimal detail zoom-in
            extent: campusExtent,    // Pengguna tidak bisa menggeser peta keluar dari kotak radius ini
            constrainOnlyCenter: false, // Membuat geseran peta terasa mulus saat membentur batas extent
            smoothExtentConstraint: false
        })
    });
});