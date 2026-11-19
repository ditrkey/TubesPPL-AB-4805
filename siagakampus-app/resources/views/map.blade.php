<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiagaKampus - Peta Interaktif</title>
    @vite(['resources/css/app.css','resources/js/InteractiveMap.js'])

    <style>
        #map .ol-layer {
            filter: saturate(75%) brightness(104%) contrast(92%) hue-rotate(-8deg);
        }
    </style>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('css/style.css')}}">
    <title>Peta Interaktif</title>
</head>
<body class="flex min-h-screen">
    <aside class="flex min-h-screen flex-col justify-between border-e border-gray-100 bg-white w-64 flex-shrink:0">
<div class="p-4 flex-1 overflow-y-auto">
    <span
      class="grid h-12 w-32 place-content-center rounded-lg bg-gray-100 text-sm text-gray-500"
    >
      Logo
    </span>

    <ul class="mt-4 space-y-1">

    <li>
        <a
          href=""
          class="block rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900"
        >
          Beranda
        </a>
    </li>

      <li>
        <a
          href=""
            class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900"
        >
          Inventaris
        </a>
      </li>

      <li>
        <a
          href=""
          class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900"
        >
          Form
        </a>
      </li>

      <li>
        <a
          href="#"
          class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900"
        >
          Invoices
        </a>
      </li>


      <li>
        <details class="group [&_summary::-webkit-details-marker]:hidden">
          <summary
            class="flex items-center justify-between rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900"
          >
            Teams

            <span class="shrink-0 transition duration-300 group-open:-rotate-180">
              <svg
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                class="size-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </span>
          </summary>

          <ul class="mt-2 space-y-1 px-4">
            <li>
              <a
                href="#"
                class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900"
              >
                Banned Users
              </a>
            </li>

            <li>
              <a
                href="#"
                class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900"
              >
                Calendar
              </a>
            </li>
          </ul>
        </details>
      </li>

      <li>
        <details class="group [&_summary::-webkit-details-marker]:hidden">
          <summary
            class="flex items-center justify-between rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900"
          >
            Account

            <span class="shrink-0 transition duration-300 group-open:-rotate-180">
              <svg
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                class="size-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </span>
          </summary>

          <ul class="mt-2 space-y-1 px-4">
            <li>
              <a
                href="#"
                class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900"
              >
                Details
              </a>
            </li>

            <li>
              <a
                href="#"
                class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900"
              >
                Security
              </a>
            </li>

            <li>
              <a
                href="#"
                class="w-full rounded-lg px-4 py-2 [text-align:inherit] text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700"
              >
                Logout
              </a>
            </li>
          </ul>
        </details>
      </li>
    </ul>
  </div>

  <div class="sticky inset-x-0 bottom-0 border-t border-gray-100">
    <a
      href="#"
      class="flex items-center gap-2 bg-white p-4 hover:bg-gray-50 hover:transition-colors"
    >
      <img
        alt=""
        src="https://images.unsplash.com/photo-1600486913747-55e5470d6f40?auto=format&fit=crop&q=80&w=1160"
        class="size-10 rounded-full object-cover"
      />

      <p class="text-xs text-gray-900">
        <strong class="block font-medium">Eric Frusciante</strong>

        <span> eric@frusciante.com </span>
      </p>
    </a>
</div>
</aside>

<main class="flex-1 p-8 overflow-y-auto">
<div class="max-w-7xl mx-auto">
      
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Interactive Map</h1>
        <p class="text-sm text-gray-500">Peta Regional Telkom University</p>
      </div>

<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <div class="overflow-x-auto">
          

         <div id="hs-basic-leaflet" class="container relative w-full h-100 hs-leaflet z-10">
        <!-- Container Peta -->
        <div id="map" class="w-full h-[500px] min-h-[400px] z-0" style="width: 100%; height: 75vh; background-color: #e5e7eb; z-index: 0;"></div>

        <!-- Popup Container -->
        <div id="popup" class="bg-white p-3 rounded-xl shadow-xl border border-gray-100 hidden max-w-xs text-sm z-10">
            <div id="popup-content">

            <button></button>

            </div>
        </div>
    </div>

    

</div>
</div>

</div>
  </main>
</div>

</body>
</html>