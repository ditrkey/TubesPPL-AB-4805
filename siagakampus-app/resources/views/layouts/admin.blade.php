<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Verifikasi Laporan Banjir') - SiagaKampus Pusat Kendali</title>

    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN (Ensures immediate styling fallback alongside Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                            950: '#052e16',
                        },
                        sidebar: {
                            DEFAULT: '#0b1f1c',
                            card: '#122c28',
                            hover: '#183c36',
                            active: '#105244',
                            border: '#1b413a'
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Inter"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Leaflet Styles -->
    <style>
        .leaflet-container {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .custom-popup .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            padding: 4px;
        }
        .custom-popup .leaflet-popup-tip {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>

    @stack('styles')
</head>
<body class="h-full flex text-slate-800 antialiased overflow-hidden font-sans" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/60 z-40 lg:hidden" 
         style="display: none;"></div>

         {{ 
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-[#0a1f1b] border-r border-[#153a33] text-slate-300 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto">
        
        <!-- Logo & Header Brand -->
        <div class="h-20 px-6 flex items-center justify-between border-b border-[#153a33]">
            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl from-emerald-600 to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-950/40 font-bold text-lg">
                    <i class="fa-solid fa-water"></i>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-white flex items-center gap-1.5">
                        SiagaKampus
                        <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Tel-U</span>
                    </span>
                    <p class="text-[11px] text-emerald-300/70 font-medium tracking-wide">Pusat Kendali Bencana</p>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Main Navigation Links -->
        <nav class="flex-1 px-4 py-5 space-y-1.5 overflow-y-auto custom-scrollbar">
            
            <div class="px-3 pb-2 text-[11px] font-semibold tracking-wider text-emerald-400/80 uppercase">
                Operasional Bencana
            </div>

            <!-- Operational Analytics -->
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300/80 hover:bg-[#122e28] hover:text-white transition group">
                <i class="fa-solid fa-chart-pie text-emerald-400/70 group-hover:text-emerald-400 w-5 text-center"></i>
                <span>Operational Analytics</span>
            </a>

            <!-- Verifikasi Laporan (PBI-05 / KF-05) - ACTIVE -->
            <a href="{{ route('admin.reports.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.reports.*') ? 'bg-[#133e34] text-white shadow-sm shadow-emerald-950/40 border border-emerald-500/30' : 'text-slate-300/80 hover:bg-[#122e28] hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-400 w-5 text-center"></i>
                    <span>Verifikasi Laporan</span>
                </div>
                <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    KF-05
                </span>
            </a>

            <!-- Editor Zona Rawan -->
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300/80 hover:bg-[#122e28] hover:text-white transition group">
                <i class="fa-solid fa-map-location-dot text-emerald-400/70 group-hover:text-emerald-400 w-5 text-center"></i>
                <span>Editor Zona Rawan</span>
            </a>

            <!-- Kontrol Evakuasi -->
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300/80 hover:bg-[#122e28] hover:text-white transition group">
                <i class="fa-solid fa-person-walking-dashed-line-arrow-right text-emerald-400/70 group-hover:text-emerald-400 w-5 text-center"></i>
                <span>Kontrol Rute Evakuasi</span>
            </a>

            <!-- Kontrol Zona Parkir -->
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300/80 hover:bg-[#122e28] hover:text-white transition group">
                <i class="fa-solid fa-square-parking text-emerald-400/70 group-hover:text-emerald-400 w-5 text-center"></i>
                <span>Kontrol Zona Parkir</span>
            </a>

            <div class="pt-4 px-3 pb-2 text-[11px] font-semibold tracking-wider text-emerald-400/80 uppercase">
                Komunikasi & Dukungan
            </div>

            <!-- Broadcast Early Warning -->
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300/80 hover:bg-[#122e28] hover:text-white transition group">
                <i class="fa-solid fa-bullhorn text-emerald-400/70 group-hover:text-emerald-400 w-5 text-center"></i>
                <span>Broadcast Peringatan Dini</span>
            </a>

            <!-- Inventaris Alat Bencana -->
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300/80 hover:bg-[#122e28] hover:text-white transition group">
                <i class="fa-solid fa-boxes-stacked text-emerald-400/70 group-hover:text-emerald-400 w-5 text-center"></i>
                <span>Inventaris Alat Bencana</span>
            </a>

            <!-- System Audit Trail -->
            <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300/80 hover:bg-[#122e28] hover:text-white transition group">
                <i class="fa-solid fa-shield-halved text-emerald-400/70 group-hover:text-emerald-400 w-5 text-center"></i>
                <span>System Audit Trail</span>
            </a>

            <!-- Peta Interaktif Publik -->
            <a href="{{ route('map.index') }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300/80 hover:bg-[#122e28] hover:text-white transition group">
                <i class="fa-solid fa-globe text-teal-400/70 group-hover:text-teal-400 w-5 text-center"></i>
                <span>Peta Publik (Web)</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-auto text-slate-400"></i>
            </a>

        </nav>

        <div class="p-4 border-t border-[#153a33] bg-[#071714]">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-emerald-700 text-white font-bold text-xs flex items-center justify-center shrink-0 border border-emerald-400/40">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin SiagaKampus' }}</p>
                        <span class="inline-flex items-center text-[11px] text-emerald-300/80 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block mr-1.5 animate-pulse"></span>
                            {{ auth()->user()->role ?? 'Admin' }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="Keluar Sistem" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition">
                        <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>
            <div class="mt-2 pt-2 border-t border-[#153a33]/60 flex items-center justify-between text-[10px] text-slate-400">
                <span>SiagaKampus TEL-U</span>
                <span class="text-emerald-400/60 font-mono">v1.2-PBI05</span>
            </div>
        </div>
    </aside>
    }}

    <!-- MAIN CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
        
        <!-- TOPBAR -->
        <header class="h-16 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between z-30 shrink-0">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <!-- Breadcrumbs -->
                <nav class="flex items-center text-xs text-slate-500 font-medium">
                    <span class="text-slate-400">Pusat Kendali</span>
                    <i class="fa-solid fa-chevron-right text-[9px] mx-2 text-slate-300"></i>
                    <span class="text-emerald-700 font-semibold">@yield('page_category', 'Verifikasi & Laporan')</span>
                    <i class="fa-solid fa-chevron-right text-[9px] mx-2 text-slate-300"></i>
                    <span class="text-slate-700 font-semibold truncate max-w-xs">@yield('page_title', 'Daftar Laporan')</span>
                </nav>
            </div>

            <!-- Header Right Section -->
            <div class="flex items-center gap-4">
                <!-- Clock WIB -->
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200/60 text-emerald-800 text-xs font-medium">
                    <i class="fa-regular fa-clock text-emerald-600"></i>
                    <span id="liveWibClock">WIB</span>
                </div>

                <!-- Notif Icon -->
                <div class="relative">
                    <button class="w-9 h-9 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition">
                        <i class="fa-regular fa-bell text-sm"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                    </button>
                </div>

                <!-- Campus Tag -->
                <div class="hidden md:flex items-center gap-2 pl-3 border-l border-slate-200 text-xs text-slate-500">
                    <i class="fa-solid fa-location-dot text-rose-500"></i>
                    <span>Bandung Technoplex</span>
                </div>
            </div>
        </header>

        <!-- FLASH NOTIFICATION ALERTS -->
        <div class="px-6 pt-4 shrink-0">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-sm shrink-0">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-emerald-600 hover:text-emerald-900">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-3 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center text-sm shrink-0">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-rose-600 hover:text-rose-900">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div x-data="{ show: true }" x-show="show" class="mb-3 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 shadow-sm">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2 font-semibold text-sm">
                            <i class="fa-solid fa-circle-exclamation text-amber-600"></i>
                            <span>Terdapat kendala validasi data formulir:</span>
                        </div>
                        <button @click="show = false" class="text-amber-700 hover:text-amber-900">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-amber-800 pl-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- MAIN SCROLLABLE CONTENT BODY -->
        <main class="flex-1 overflow-y-auto px-6 py-4">
            @yield('content')
        </main>
    </div>

    <!-- Leaflet.js JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Update Live WIB Clock
        function updateClock() {
            const now = new Date();
            const options = {
                timeZone: 'Asia/Jakarta',
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };
            const clockEl = document.getElementById('liveWibClock');
            if (clockEl) {
                clockEl.textContent = new Intl.DateTimeFormat('id-ID', options).format(now) + ' WIB';
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    @stack('scripts')
</body>
</html>

