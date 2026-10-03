<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Prasta Solusi Indonesia</title>
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
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-200">
                <i data-lucide="mail-check" class="w-8 h-8"></i>
            </div>
        </div>
        <h2 class="mt-2 text-center text-3xl font-extrabold text-corporate">
            Verifikasi Alamat Email
        </h2>
        <p class="mt-3 text-center text-sm text-slate-600 leading-relaxed px-4">
            Kami telah mengirimkan 6 digit kode OTP ke email pendaftaran Anda. Silakan masukkan kode tersebut di bawah ini untuk mengaktifkan akun klien Anda.
        </p>
    </div>

    <div class="relative mt-8 sm:mx-auto sm:w-full sm:max-w-md z-10">
        <div class="bg-white py-10 px-6 shadow-2xl rounded-3xl sm:px-10 border border-slate-100">
            <!-- Simulated Form Submission using JS -->
            <form class="space-y-6" action="/login" method="GET" onsubmit="alert('Simulasi: Verifikasi Berhasil! Akun klien aktif dan diarahkan ke Login.'); return true;">
                
                <div>
                    <label for="otp" class="block text-sm font-semibold text-slate-700 text-center mb-4">Kode OTP 6 Digit</label>
                    
                    <!-- 6 Digit Input Group -->
                    <div class="flex justify-center gap-2 md:gap-3">
                        <input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-xl border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50 border shadow-sm transition-colors" required>
                        <input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-xl border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50 border shadow-sm transition-colors" required>
                        <input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-xl border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50 border shadow-sm transition-colors" required>
                        <input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-xl border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50 border shadow-sm transition-colors" required>
                        <input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-xl border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50 border shadow-sm transition-colors" required>
                        <input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-xl border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50 border shadow-sm transition-colors" required>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300">
                        Verifikasi & Aktifkan Akun
                    </button>
                </div>
            </form>
            
            <div class="mt-8 pt-6 border-t border-slate-200 text-center">
                <p class="text-xs text-slate-500 mb-2">Belum menerima email kode verifikasi?</p>
                <button type="button" onclick="alert('Kode verifikasi baru telah dikirimkan ulang ke email Anda.')" class="text-sm font-bold text-corporate hover:text-emerald-600 transition-colors">
                    Kirim Ulang Kode
                </button>
            </div>
            
            <div class="mt-6 text-center">
                <a href="/login" class="text-xs font-medium text-slate-400 hover:text-slate-600 flex items-center justify-center gap-1">
                    <i data-lucide="arrow-left" class="w-3 h-3"></i> Kembali ke halaman Login
                </a>
            </div>
        </div>
    </div>
    
    <script>
        lucide.createIcons();
        
        // Auto-focus logic for 6-digit OTP inputs
        const inputs = document.querySelectorAll('input[type="text"]');
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
</body>
</html>
