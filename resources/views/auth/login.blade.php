<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Prasta Solusi Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .text-corporate { color: #0f172a; }
        .bg-corporate { background-color: #0f172a; }
        .text-gold { color: #d4af37; }
        .bg-pattern {
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-emerald-500 selection:text-white relative min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-slate-200 via-slate-50 to-slate-50 z-0"></div>
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-[400px] h-[400px] bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
    
    <div class="relative sm:mx-auto sm:w-full sm:max-w-md z-10">
        <a href="/" class="flex justify-center items-center gap-3 mb-6 group cursor-pointer hover:scale-105 transition-transform">
            <div class="w-12 h-12 rounded-xl bg-corporate flex items-center justify-center text-white shadow-lg">
                <i data-lucide="shield-check" class="w-7 h-7 text-gold"></i>
            </div>
            <h1 class="font-extrabold text-2xl text-corporate tracking-tight">Prasta Solusi</h1>
        </a>
        <h2 class="mt-2 text-center text-3xl font-extrabold text-corporate">
            Masuk ke Portal
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600">
            Akses sistem manajemen perizinan dan kepatuhan
        </p>
    </div>

    <div class="relative mt-8 sm:mx-auto sm:w-full sm:max-w-md z-10">
        <div class="bg-white py-10 px-6 shadow-2xl rounded-3xl sm:px-10 border border-slate-100">
            <form class="space-y-6" action="#" method="POST" onsubmit="event.preventDefault(); alert('Ini halaman login sistem. Nanti role / hak akses dibedakan di tahap backend authentication.');">
                
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="h-5 w-5 text-slate-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required class="focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 sm:text-sm border-slate-300 rounded-xl py-3 bg-slate-50 border transition-colors" placeholder="email@perusahaan.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 text-slate-400"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 sm:text-sm border-slate-300 rounded-xl py-3 bg-slate-50 border transition-colors" placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded cursor-pointer">
                        <label for="remember-me" class="ml-2 block text-sm text-slate-700 cursor-pointer">
                            Ingat Saya
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-emerald-600 hover:text-emerald-500">
                            Lupa password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-corporate hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300">
                        Masuk Sistem
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center text-sm">
                <span class="text-slate-600">Klien baru dan belum punya akun?</span>
                <a href="/register" class="font-bold text-emerald-600 hover:text-emerald-500 ml-1">Daftar Sekarang</a>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-200">
                <p class="text-center text-xs text-slate-500">
                    Gunakan kredensial (Email & Password) yang telah diberikan oleh admin atau daftar akun baru untuk peran Klien.
                </p>
            </div>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
