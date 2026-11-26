<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk Sistem - SiagaKampus Tel-U</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-full bg-[#f7f6f2] text-slate-700" x-data="{ showHelp: false }">
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">
        {{-- Kolom Form (1 bagian -> 4 dari 12 kolom) --}}
        <div class="lg:col-span-4 flex flex-col justify-between p-6 sm:p-8 lg:p-10 bg-[#fbfbfa]">
            <div class="w-full max-w-sm mx-auto my-8 space-y-6">
              <a href="/" class="inline-flex items-center gap-3">
               <svg fill="none" height="36" viewBox="0 0 40 48" width="36" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                        <g clip-rule="evenodd" fill-rule="evenodd">
                            <path d="m4.29111 28.6025c.90712.9019.9071 2.3641-.00003 3.266l-.04646.0462c-.90713.9019-2.37788.9018-3.284994-.0001-.9071177-.9019-.9071038-2.3641.000031-3.266l.046453-.0462c.90714-.9019 2.37788-.9018 3.285.0001z" fill="#a5b4fc"/>
                            <path d="m15.2112 29.298c.9055.9035.9028 2.3658-.006 3.266l-6.43424 6.3741c-.90876.9002-2.3795.8976-3.28499-.0059-.90548-.9035-.90282-2.3658.00594-3.266l6.43429-6.3741c.9088-.9002 2.3795-.8976 3.285.0059z" fill="#6366f1"/>
                            <path d="m23.9431 40.2706c.7116-1.0613 2.1538-1.348 3.2212-.6405l.1394.0923c1.0674.7075 1.3558 2.1414.6442 3.2026-.7116 1.0613-2.1538 1.3481-3.2212.6406l-.1394-.0924c-1.0674-.7075-1.3558-2.1414-.6442-3.2026z" fill="#a5b4fc"/>
                            <path d="m39.2728 28.4638c.9072.9019.9072 2.3641 0 3.266l-3.159 3.1408c-.9072.9019-2.3779.9019-3.285 0s-.9071-2.3641 0-3.266l3.1591-3.1408c.9071-.9019 2.3778-.9019 3.2849 0z" fill="#6366f1"/>
                            <g fill="#4f46e5">
                                <path d="m39.3197 16.8473c.9071.9019.9071 2.3642 0 3.266l-23.3446 23.2098c-.9071.9019-2.3779.9019-3.285 0s-.9071-2.3641 0-3.266l23.3446-23.2098c.9071-.9019 2.3778-.9019 3.285 0z"/>
                                <path d="m34.905 9.71133c.9072.90187.9072 2.36417 0 3.26607l-13.426 13.3485c-.9071.9019-2.3779.9019-3.285 0s-.9071-2.3642 0-3.2661l13.4261-13.34847c.9071-.90189 2.3778-.90189 3.2849 0z"/>
                                <path d="m27.8187 5.18362c.9077.90128.9087 2.36353.0022 3.26603l-17.2588 17.18215c-.90647.9025-2.37721.9035-3.28495.0022-.90773-.9013-.90872-2.3635-.00221-3.266l17.25876-17.18218c.9065-.9025 2.3772-.90348 3.285-.0022z"/>
                            </g>
                            <path d="m10.7276 10.6841c.9056.9035.9031 2.3657-.0056 3.266l-6.75949 6.6974c-.90869.9003-2.37943.8978-3.28499-.0056-.905563-.9035-.903032-2.3657.005654-3.2661l6.759476-6.6973c.90868-.90034 2.37942-.89783 3.28495.0056z" fill="#6366f1"/>
                            <path d="m14.8678 4.00024c1.2828 0 2.3228 1.03397 2.3228 2.30944v.23094c0 1.27546-1.04 2.30943-2.3228 2.30943-1.2829 0-2.3229-1.03397-2.3229-2.30943v-.23094c0-1.27547 1.04-2.30944 2.3229-2.30944z" fill="#a5b4fc"/>
                        </g>
                    </svg>
                    <div>
                        <span class="block text-base font-bold tracking-tight text-slate-800">SiagaKampus</span>
                        <span class="block text-[11px] font-medium text-slate-500">Telkom University</span>
                    </div>
                    </a>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-800">Masuk ke Sistem</h1>
                    <p class="mt-1.5 text-sm text-slate-500">
                        Gunakan akun resmi untuk mengakses dashboard pemantauan dan manajemen.
                    </p>
                </div>

                {{-- Error Alerts --}}
                @if($errors->any())
                    <div class="p-3.5 rounded-xl bg-rose-50/80 border border-rose-200 text-rose-700 text-xs">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-3.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="emailInput" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                            Alamat Email SSO / Kampus
                        </label>
                        <div class="relative">
                            <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input
                                type="email"
                                name="email"
                                id="emailInput"
                                value="{{ old('email', 'admin@siagakampus.telkomuniversity.ac.id') }}"
                                required
                                placeholder="nama@telkomuniversity.ac.id"
                                class="w-full pl-9 pr-4 py-2.5 text-sm bg-white border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-slate-400/25 focus:border-slate-400 transition"
                            >
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="passwordInput" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input
                                type="password"
                                name="password"
                                id="passwordInput"
                                value="password"
                                required
                                placeholder="••••••••"
                                class="w-full pl-9 pr-4 py-2.5 text-sm bg-white border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-slate-400/25 focus:border-slate-400 transition"
                            >
                        </div>
                    </div>

                    {{-- Remember Me & Bantuan --}}
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded bg-white border-slate-300 text-slate-700 focus:ring-slate-400/25">
                            <span>Ingat sesi saya</span>
                        </label>
                        <button
                            type="button"
                            @click="showHelp = !showHelp"
                            class="text-slate-600 hover:text-slate-900 hover:underline font-medium"
                        >
                            Bantuan Akses
                        </button>
                    </div>

                    <div
                        x-show="showHelp"
                        x-cloak
                        class="p-3 rounded-xl bg-stone-100/90 border border-stone-200 text-xs text-slate-600"
                    >
                        Hubungi admin pengelola sistem apabila mengalami kendala masuk atau reset kredensial akun.
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        class="w-full py-3 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-stone-50 font-semibold text-sm shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                        <span>Masuk ke Dashboard</span>
                    </button>
                </form>

                {{-- Quick Demo Credentials Hint --}}
                <div class="pt-4 border-t border-slate-200/80 text-center">
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Akun Pengelola: <code class="text-slate-700 font-mono bg-stone-100 border border-stone-200/80 px-1.5 py-0.5 rounded">admin@siagakampus.telkomuniversity.ac.id</code><br>
                        Kata sandi: <code class="text-slate-700 font-mono bg-stone-100 border border-stone-200/80 px-1.5 py-0.5 rounded">password</code>
                    </p>
                </div>
            </div>

            <div class="text-center lg:text-left text-[11px] text-slate-400">
                &copy; 2026 Tim Pengembang SiagaKampus TEL-U. Hak Cipta Dilindungi.
            </div>
        </div>

        {{-- Kolom Gambar (2 bagian -> 8 dari 12 kolom) --}}
        <div class="min-h-70 lg:min-h-screen lg:col-span-8 relative overflow-hidden bg-stone-100 border-t lg:border-t-0 lg:border-l border-slate-200/80">
            <img
                src="https://images.pexels.com/photos/35066820/pexels-photo-35066820.jpeg"
                alt="Koordinasi tim SiagaKampus"
                class="h-full w-full object-cover opacity-30"
                loading="lazy"
            >
            <!-- <div class="absolute inset-0 bg-linear-to-t from-[#eceae4]/95 via-[#eceae4]/65 to-stone-100/35 flex flex-col justify-end p-6 sm:p-10 lg:p-12">
                <span class="inline-flex w-fit items-center gap-2 rounded-full bg-white/90 border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 shadow-2xs mb-4">
                    <i class="fa-solid fa-circle-dot text-[10px] text-slate-500"></i>
                    Pusat Koordinasi &amp; Pemantauan
                </span>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-800 max-w-xl">
                    Pemantauan Kondisi, Peta Interaktif, dan Manajemen Logistik dalam Satu Portal
                </h2>
                <p class="mt-2 text-sm text-slate-600 max-w-xl">
                    Akses terpadu untuk mengelola laporan, titik lokasi, serta ketersediaan inventaris secara cepat dan terstruktur.
                </p> -->
            </div>
        </div>
    </div>
</body>
</html>