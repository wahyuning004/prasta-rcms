<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout Pemesanan Layanan - IzinYou</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .text-corporate { color: #0f172a; }
        .bg-corporate { background-color: #0f172a; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-sky-500 selection:text-white flex h-screen overflow-hidden">

    @include('klien.partials.sidebar')

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-8 shrink-0 relative z-10">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleKlienSidebar()" class="lg:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-corporate">Checkout &amp; Konfirmasi Pemesanan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Periksa rincian layanan, gunakan voucher promo, dan konfirmasi pengajuan layanan Anda.</p>
                </div>
            </div>
            <a href="{{ route('klien.services.show', $service->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Kembali ke Detail Layanan</span>
            </a>
        </header>

        @include('klien.partials.alerts')

        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Kiri: Detail Layanan & Identitas Pemesan -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                {{ $service->category?->name ?? 'Perizinan & Sertifikasi' }}
                            </span>
                            <span class="text-xs text-slate-500 flex items-center gap-1">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-sky-600"></i>
                                Estimasi: <strong class="text-corporate">{{ $service->timeline_estimation ?: ($service->estimation_time ?: '1-3 Bulan') }}</strong>
                            </span>
                        </div>

                        <h3 class="text-xl font-extrabold text-corporate mb-2">{{ $service->name }}</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-5">{{ $service->description }}</p>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="text-xs font-bold text-corporate uppercase tracking-wider mb-3">Ringkasan Dokumen Persyaratan ({{ $requirements->count() }} Item):</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-slate-600">
                                @foreach($requirements->take(6) as $req)
                                    <div class="flex items-start gap-2">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold shrink-0 {{ $req->is_mandatory ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $req->is_mandatory ? 'Wajib' : 'Opsional' }}
                                        </span>
                                        <span class="leading-snug">{{ $req->document_name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Identitas Pemohon -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-bold text-corporate flex items-center gap-2">
                                <i data-lucide="building-2" class="w-4 h-4 text-sky-600"></i>
                                Identitas Pelanggan &amp; Perusahaan (Untuk Invoice)
                            </h4>
                            <a href="{{ route('klien.profile.edit') }}" class="text-xs font-bold text-sky-600 hover:underline">Ubah Profil</a>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50">
                                <span class="text-slate-400 block">Nama PIC</span>
                                <strong class="text-corporate">{{ $user->name }}</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50">
                                <span class="text-slate-400 block">Perusahaan</span>
                                <strong class="text-corporate">{{ $user->company_name ?: '-' }}</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50">
                                <span class="text-slate-400 block">Email / WhatsApp</span>
                                <strong class="text-corporate">{{ $user->email }} &bull; {{ $user->phone ?: '-' }}</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50">
                                <span class="text-slate-400 block">Alamat</span>
                                <strong class="text-corporate">{{ $user->address ?: '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kanan: Voucher Promosi & Konfirmasi Checkout -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm sticky top-6">
                        <h3 class="text-base font-extrabold text-corporate mb-4 flex items-center gap-2">
                            <i data-lucide="receipt" class="w-5 h-5 text-sky-600"></i>
                            Rincian Pembayaran &amp; Voucher
                        </h3>

                        <form action="{{ route('klien.order.checkout') }}" method="POST" id="checkoutPageForm" class="space-y-4">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $service->id }}">
                            <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">

                            <!-- Rincian Biaya Sebelum & Sesudah Diskon -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-2.5 text-xs">
                                <div class="flex justify-between text-slate-600">
                                    <span>Biaya Layanan Normal</span>
                                    <strong class="text-corporate">Rp {{ number_format($basePrice, 0, ',', '.') }}</strong>
                                </div>
                                <div class="flex justify-between text-emerald-600 font-semibold {{ old('promo_code') ? '' : 'hidden' }}" id="voucherDiscountRow">
                                    <span>Potongan Voucher (<span id="appliedVoucherCode">{{ strtoupper((string) old('promo_code')) }}</span>)</span>
                                    <span id="voucherDiscountAmount">- Rp 0</span>
                                </div>
                                <div class="pt-2.5 border-t border-slate-200 flex justify-between items-center text-sm font-extrabold text-corporate">
                                    <span>Total Pembayaran</span>
                                    <span class="text-lg text-sky-600" id="checkoutTotalDisplay">Rp {{ number_format($basePrice, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Input Voucher -->
                            <div>
                                <label for="promoCodeInput" class="block text-xs font-bold text-slate-700 mb-1.5">Kode Voucher Promosi (Opsional)</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="promo_code" id="promoCodeInput" value="{{ old('promo_code') }}"
                                           placeholder="Masukkan kode voucher"
                                           class="flex-1 px-3.5 py-2.5 text-xs font-mono uppercase bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white">
                                    <button type="button" onclick="verifyVoucherCode()" id="applyVoucherBtn"
                                            class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition shrink-0">
                                        Terapkan
                                    </button>
                                </div>
                                <p id="voucherFeedbackMsg" class="text-[11px] mt-1.5 text-slate-500">
                                    Sistem akan memverifikasi keaktifan, kuota, dan minimal transaksi voucher secara otomatis.
                                </p>
                            </div>

                            <!-- Daftar Promo Tersedia -->
                            @if($promos->isNotEmpty())
                                <div>
                                    <span class="text-[11px] font-bold text-slate-500 block mb-1.5">Voucher Promo Aktif Tersedia:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($promos as $promo)
                                            <button type="button" onclick="pickVoucher('{{ $promo->code }}')"
                                                    class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-900 text-[11px] font-mono font-bold transition text-left">
                                                {{ $promo->code }}
                                                <span class="font-sans font-semibold text-amber-700">
                                                    ({{ $promo->discount_amount > 0 ? '-Rp ' . number_format($promo->discount_amount, 0, ',', '.') : '-' . (float) $promo->discount_percentage . '%' }})
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Catatan Pesanan -->
                            <div>
                                <label for="orderNotes" class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Kebutuhan / Produk (Opsional)</label>
                                <textarea name="notes" id="orderNotes" rows="3" maxlength="1000"
                                          placeholder="Contoh: Pengurusan izin edar untuk 3 varian produk alat kesehatan..."
                                          class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white">{{ old('notes') }}</textarea>
                            </div>

                            <button type="submit" id="confirmCheckoutBtn"
                                    class="w-full py-3.5 px-4 bg-sky-600 hover:bg-sky-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-sky-600/25 flex items-center justify-center gap-2 transition">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Konfirmasi &amp; Buat Pesanan</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();

        const basePrice = {{ (float) $basePrice }};
        const serviceId = {{ (int) $service->id }};

        function formatRupiah(amount) {
            return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
        }

        function pickVoucher(code) {
            document.getElementById('promoCodeInput').value = code;
            verifyVoucherCode();
        }

        async function verifyVoucherCode() {
            const inputEl = document.getElementById('promoCodeInput');
            const feedbackEl = document.getElementById('voucherFeedbackMsg');
            const discountRow = document.getElementById('voucherDiscountRow');
            const discountAmtEl = document.getElementById('voucherDiscountAmount');
            const appliedCodeEl = document.getElementById('appliedVoucherCode');
            const totalEl = document.getElementById('checkoutTotalDisplay');
            const code = inputEl.value.trim();

            if (!code) {
                discountRow.classList.add('hidden');
                totalEl.textContent = formatRupiah(basePrice);
                feedbackEl.textContent = 'Masukkan kode voucher untuk mendapatkan potongan biaya.';
                feedbackEl.className = 'text-[11px] mt-1.5 text-slate-500';
                return;
            }

            try {
                const response = await fetch("{{ route('klien.checkout.validate-voucher') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        service_id: serviceId,
                        promo_code: code
                    })
                });

                const data = await response.json();

                if (response.ok && data.valid) {
                    discountRow.classList.remove('hidden');
                    appliedCodeEl.textContent = data.promo_code;
                    discountAmtEl.textContent = '- ' + data.discount_formatted;
                    totalEl.textContent = data.final_total_formatted;
                    feedbackEl.textContent = data.message;
                    feedbackEl.className = 'text-[11px] mt-1.5 text-emerald-600 font-semibold';
                } else {
                    discountRow.classList.add('hidden');
                    totalEl.textContent = formatRupiah(basePrice);
                    feedbackEl.textContent = data.message || 'Kode voucher tidak dapat digunakan.';
                    feedbackEl.className = 'text-[11px] mt-1.5 text-rose-600 font-semibold';
                }
            } catch (err) {
                feedbackEl.textContent = 'Gagal memeriksa voucher. Silakan coba lagi.';
                feedbackEl.className = 'text-[11px] mt-1.5 text-rose-600 font-semibold';
            }
        }

        // Pencegahan klik ganda tombol checkout
        const checkoutForm = document.getElementById('checkoutPageForm');
        const confirmBtn = document.getElementById('confirmCheckoutBtn');
        if (checkoutForm && confirmBtn) {
            checkoutForm.addEventListener('submit', () => {
                confirmBtn.disabled = true;
                confirmBtn.classList.add('opacity-75', 'cursor-not-allowed');
                confirmBtn.innerHTML = '<span>Memproses Pesanan...</span>';
            });
        }
    </script>
</body>
</html>

