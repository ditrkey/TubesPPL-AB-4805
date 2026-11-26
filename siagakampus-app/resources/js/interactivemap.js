import Map from 'ol/Map';
import View from 'ol/View';
import TileLayer from 'ol/layer/Tile';
import OSM from 'ol/source/OSM';
import { fromLonLat, transformExtent } from 'ol/proj';
import 'ol/ol.css';
import 'flowbite';

import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import Feature from 'ol/Feature';
import Point from 'ol/geom/Point';
import { circular } from 'ol/geom/Polygon';
import { Style, Circle as CircleStyle, Fill, Stroke } from 'ol/style';

document.addEventListener('DOMContentLoaded', () => {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;
    const rektoratCoords = [107.6306, -6.9740];

    const campusExtent = transformExtent(
        [107.623680,-6.980678,107.637885,-6.967664], // EPSG:4326 (WGS84)
        'EPSG:4326',
        'EPSG:3857' // Konversi ke proyeksi tampilan OpenLayers
    );

    const userLocationSource = new VectorSource();
    const userLocationLayer = new VectorLayer({
        source: userLocationSource
    });

    // Inisialisasi Peta OpenLayers Dasar
    const map = new Map({
        target: 'map',
        layers: [
            new TileLayer({
                source: new OSM()
            }),
            userLocationLayer
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

    const zoomContainer = document.querySelector('.ol-zoom');
    if (zoomContainer) {
        const locBtn = document.createElement('button');
        locBtn.type = 'button';
        locBtn.className = 'ol-location-btn';
        locBtn.title = 'Lokasi Saya';
        locBtn.innerHTML = `
            <svg style=" width: 14px; height: 14px;" fill="none" stroke="currentcolor" viewBox="0 0 24 24">
            </svg>
        `;
        locBtn.addEventListener('click', () => {
            if (!navigator.geolocation){
                alert('Browser tidak mendukung GPS.')
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;
                    console.log("lat:", lat, "Lng:", lng, "Acc:", accuracy)
                    const userCoords = fromLonLat([lng,lat]);

                    userLocationSource.clear();
                   const accuracyFeature = new Feature({
                        geometry: circular([lng, lat], accuracy, 64)
                            .transform('EPSG:4326', map.getView().getProjection())
                    });
                    accuracyFeature.setStyle(new Style({
                        fill: new Fill({ color: 'rgba(59, 130, 246, 0.15)' }),
                        stroke: new Stroke({ color: 'rgba(59, 130, 246, 0.5)', width: 1.5 })
                    }));

                    // Marker Titik User
                    const userMarkerFeature = new Feature({
                        geometry: new Point(userCoords)
                    });
                    userMarkerFeature.setStyle(new Style({
                        image: new CircleStyle({
                            radius: 6,
                            fill: new Fill({ color: '#2563EB' }),
                            stroke: new Stroke({ color: '#FFFFFF', width: 2 })
                        })
                    }));

                    userLocationSource.addFeatures([accuracyFeature, userMarkerFeature]);

                    // Animasi Geser Peta ke Posisi Pengguna
                    map.getView().animate({
                        center: userCoords,
                        zoom: 17,
                        duration: 800
                    });
                },
                (error) => {
                    alert('Gagal mendeteksi lokasi. Pastikan GPS aktif.');
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        });

        // Masukkan tombol ke bagian bawah container zoom OpenLayers
        zoomContainer.appendChild(locBtn);
         setTimeout(() => {
            map.updateSize();},
    200);
    }
}      
);