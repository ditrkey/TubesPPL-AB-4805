<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
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
<body class="h-full flex items-center justify-center p-4 bg-[#081815] text-slate-100">

    <div class="w-full max-w-md space-y-6" x-data="{ showDemoNotice: true }">
        
        <!-- BRAND LOGO -->
        <div class="text-center space-y-2">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 items-center justify-center text-white shadow-xl shadow-emerald-950/60 font-bold text-2xl mx-auto">
                <i class="fa-solid fa-water"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white">SiagaKampus</h1>
            <p class="text-xs text-emerald-400/80 font-medium">Sistem Mitigasi &amp; Penanggulangan Banjir Kampus Tel-U</p>
        </div>

        <!-- LOGIN CARD -->
        <div class="bg-[#0e2722] border border-[#1b433a] p-6 sm:p-8 rounded-3xl shadow-2xl space-y-5">
            <div>
                <h2 class="text-lg font-bold text-white">Masuk ke Pusat Kendali</h2>
                <p class="text-xs text-slate-400 mt-0.5">Khusus sivitas akademika dan tim operasional kampus (UC-01)</p>
            </div>

            <!-- Error Alerts -->
            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Alamat Email SSO / Kampus
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="email" 
                               name="email" 
                               id="emailInput"
                               value="{{ old('email', 'admin@siagakampus.telkomuniversity.ac.id') }}" 
                               required 
                               placeholder="nama@telkomuniversity.ac.id"
                               class="w-full pl-9 pr-4 py-2.5 text-sm bg-[#091a17] border border-[#1b433a] rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="password" 
                               name="password" 
                               id="passwordInput"
                               value="password"
                               required 
                               placeholder="••••••••"
                               class="w-full pl-9 pr-4 py-2.5 text-sm bg-[#091a17] border border-[#1b433a] rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-[#091a17] border-[#1b433a] text-emerald-600 focus:ring-0">
                        <span>Ingat sesi saya</span>
                    </label>
                    <span class="text-emerald-400 hover:underline cursor-pointer">Bantuan Akses</span>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-sm shadow-lg shadow-emerald-950/60 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <!-- Quick Demo Credentials Hint -->
            <div class="pt-3 border-t border-[#1b433a]/60 text-center">
                <p class="text-[11px] text-slate-400">
                    Akun Pengelola: <code class="text-emerald-300 font-mono">admin@siagakampus.telkomuniversity.ac.id</code><br>
                    Kata sandi: <code class="text-emerald-300 font-mono">password</code>
                </p>
            </div>
        </div>

        <div class="text-center text-[11px] text-slate-400">
            &copy; 2026 Tim Pengembang SiagaKampus TEL-U. Hak Cipta Dilindungi.
        </div>

    </div>

</body>
</html>

