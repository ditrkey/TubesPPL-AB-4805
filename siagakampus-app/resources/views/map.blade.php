<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiagaKampus - Peta Interaktif</title>
    @vite(['resources/css/app.css'])

    <style>
        #map .ol-layer {
            filter: saturate(75%) brightness(104%) contrast(92%) hue-rotate(-8deg);
        }
    </style>

</head>
<body class="bg-gray-100 m-0 p-0">

    <div class="relative w-full h-screen">
        <!-- Container Peta -->
        <div id="map" class="w-full h-full z-0" style="width: 100%; height: 100vh; background-color: #e5e7eb; z-index: 0;"></div>

        <!-- Popup Container -->
        <div id="popup" class="bg-white p-3 rounded-xl shadow-xl border border-gray-100 hidden max-w-xs text-sm z-10">
            <div id="popup-content"></div>
        </div>
    </div>

    @vite(['resources/js/map.js'])
</body>
</html>
