<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pemesanan {{ $order->order_number }} - IzinYou</title>
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
                    <h2 class="text-lg sm:text-xl font-bold text-corporate">Konfirmasi Pemesanan Berhasil</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pesanan Anda telah tercatat di sistem dan invoice tagihan telah diterbitkan.</p>
                </div>
            </div>
        </header>

        @include('klien.partials.alerts')

        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/80 shadow-sm text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="check-circle-2" class="w-9 h-9"></i>
                    </div>

                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Pesanan Terkonfirmasi
                    </span>

                    <h3 class="text-2xl font-black text-corporate mt-3">Terima Kasih, {{ $user->name }}!</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-lg mx-auto">
                        Pengajuan layanan perizinan Anda telah kami terima. Silakan selesaikan pembayaran dan lengkapi dokumen persyaratan agar tim kami dapat segera memproses pengajuan Anda.
                    </p>

                    <!-- Identitas Pesanan & Invoice -->
                    <div class="mt-7 p-5 rounded-2xl bg-slate-50 border border-slate-200/70 text-left space-y-3 text-xs">
                        <div class="flex flex-wrap justify-between gap-2 pb-3 border-b border-slate-200">
                            <div>
                                <span class="text-slate-400 block">Nomor Pesanan</span>
                                <strong class="font-mono text-sm text-corporate">{{ $order->order_number }}</strong>
                            </div>
                            <div class="sm:text-right">
                                <span class="text-slate-400 block">Nomor Invoice</span>
                                <strong class="font-mono text-sm text-sky-700">{{ $invoice->invoice_number }}</strong>
                            </div>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Layanan Dipilih</span>
                            <strong class="text-corporate text-right">{{ $invoice->service_name }}</strong>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Harga Layanan Normal</span>
                            <span class="font-semibold text-corporate">Rp {{ number_format((float) $invoice->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Potongan Diskon {{ $order->promo_code ? '(' . $order->promo_code . ')' : '' }}</span>
                            <span class="font-semibold text-emerald-600">- Rp {{ number_format((float) $invoice->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-3 border-t border-slate-200 flex justify-between items-center text-sm">
                            <strong class="text-corporate">Total Tagihan</strong>
                            <strong class="text-lg font-black text-sky-600">Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <!-- Tombol Aksi Lanjutan -->
                    <div class="mt-7 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <a href="{{ route('klien.orders.invoice', $order->id) }}" class="py-3 px-4 bg-slate-100 hover:bg-slate-200 text-corporate rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition">
                            <i data-lucide="file-text" class="w-4 h-4 text-sky-600"></i>
                            <span>Lihat Invoice</span>
                        </a>
                        <a href="{{ route('klien.orders.invoice.pdf', $order->id) }}" class="py-3 px-4 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            <span>Unduh Invoice PDF</span>
                        </a>
                        <a href="{{ route('klien.orders.show', $order->id) }}" class="py-3 px-4 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-md shadow-sky-600/20 transition">
                            <span>Bayar &amp; Unggah Dokumen</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>

