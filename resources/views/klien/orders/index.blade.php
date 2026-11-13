<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan &amp; Proyek Saya - IzinYou</title>
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
                    <h2 class="text-lg sm:text-xl font-bold text-corporate">Daftar Pesanan &amp; Proyek Saya</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola invoice, unggah bukti pembayaran, dokumen persyaratan, tracking progres, dan ulasan.</p>
                </div>
            </div>
            <a href="{{ route('klien.services.index') }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Pesan Layanan Baru</span>
            </a>
        </header>

        @include('klien.partials.alerts')

        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            <div class="max-w-6xl mx-auto space-y-5">
                @forelse($orders as $order)
                    @php
                        $paymentMeta = $order->paymentStatusMeta();
                        $service = $order->resolvedService();
                    @endphp
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-sm hover:border-sky-300 transition">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="font-mono text-xs font-bold bg-slate-900 text-white px-3 py-1 rounded-lg">{{ $order->order_number }}</span>
                                    @if($order->invoice)
                                        <span class="font-mono text-[11px] font-semibold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg">{{ $order->invoice->invoice_number }}</span>
                                    @endif
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                                        @if($paymentMeta['badge'] === 'emerald') bg-emerald-50 text-emerald-700 border border-emerald-200
                                        @elseif($paymentMeta['badge'] === 'rose') bg-rose-50 text-rose-700 border border-rose-200
                                        @elseif($paymentMeta['badge'] === 'sky') bg-sky-50 text-sky-700 border border-sky-200
                                        @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                        Pembayaran: {{ $paymentMeta['label'] }}
                                    </span>
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                                        @if($order->status === 'completed') bg-emerald-100 text-emerald-800
                                        @elseif($order->isCancelled()) bg-rose-100 text-rose-800
                                        @else bg-blue-50 text-blue-700 @endif">
                                        Status Proyek: {{ $order->statusLabel() }}
                                    </span>
                                </div>
                                <h3 class="text-base sm:text-lg font-extrabold text-corporate">{{ $service?->name ?? 'Layanan Perizinan & Sertifikasi' }}</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Dipesan pada {{ $order->created_at->format('d F Y, H:i') }} WIB</p>
                                @if($order->isCancelled() && $order->cancellationReason())
                                    <p class="text-[11px] text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-2.5 py-1 mt-1.5 inline-block">
                                        {{ $order->cancellationReason() }}
                                    </p>
                                @endif
                            </div>

                            <div class="text-left lg:text-right">
                                <span class="text-[11px] text-slate-400 block">Total Tagihan</span>
                                <p class="text-lg font-black text-corporate">Rp {{ number_format((float) $order->total_cost, 0, ',', '.') }}</p>
                                @if((float) $order->discount_amount > 0)
                                    <p class="text-[11px] text-emerald-600 font-semibold">Hemat Rp {{ number_format((float) $order->discount_amount, 0, ',', '.') }} ({{ $order->promo_code }})</p>
                                @endif
                            </div>
                        </div>

                        <!-- Progress & Actions -->
                        <div class="pt-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1 max-w-md">
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="font-bold text-slate-700">Tahap Saat Ini: <span class="text-sky-700">{{ $order->current_stage ?? 'Pesanan Diterima' }}</span></span>
                                    <span class="font-extrabold text-corporate">{{ (int) ($order->progress_percentage ?? 15) }}%</span>
                                </div>
                                <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-sky-600 rounded-full transition-all" style="width: {{ min(100, (int) ($order->progress_percentage ?? 15)) }}%"></div>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                @if($order->isCancelled())
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i data-lucide="slash" class="w-3.5 h-3.5"></i>
                                        <span>Pesanan Dibatalkan</span>
                                    </span>
                                @else
                                    <a href="{{ route('klien.orders.invoice', $order->id) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-corporate rounded-xl text-xs font-bold flex items-center gap-1.5 transition">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-sky-600"></i>
                                        <span>Invoice</span>
                                    </a>
                                    <a href="{{ route('klien.orders.invoice.pdf', $order->id) }}" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition">
                                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                        <span>Unduh PDF</span>
                                    </a>
                                    @if($order->canBeCancelled())
                                        <button type="button"
                                                data-action="{{ route('klien.orders.cancel', $order->id) }}"
                                                data-order-number="{{ $order->order_number }}"
                                                data-service-name="{{ $service?->name ?? 'Layanan Perizinan & Sertifikasi' }}"
                                                onclick="openCancelOrderModal(this)"
                                                class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition">
                                            <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                            <span>Batalkan Pesanan</span>
                                        </button>
                                    @endif
                                    <a href="{{ route('klien.orders.show', $order->id) }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition">
                                        <span>Kelola Proyek &amp; Dokumen</span>
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-3xl p-12 border border-dashed border-slate-200 text-center">
                        <i data-lucide="inbox" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                        <h3 class="text-base font-bold text-corporate">Belum Ada Pesanan Layanan</h3>
                        <p class="text-xs text-slate-500 mt-1 mb-5">Silakan pilih layanan perizinan atau sertifikasi dari katalog kami untuk memulai pengajuan.</p>
                        <a href="{{ route('klien.services.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-md transition">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                            <span>Jelajahi Katalog Layanan</span>
                        </a>
                    </div>
                @endforelse

                @if($orders->hasPages())
                    <div class="pt-2">{{ $orders->links() }}</div>
                @endif
            </div>
        </div>
    </main>

    <!-- Modal Konfirmasi Pembatalan Pesanan -->
    <div id="cancelOrderModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-start justify-between pb-3 mb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-corporate">Konfirmasi Pembatalan Pesanan</h3>
                        <p id="cancelOrderSubtitle" class="text-xs text-slate-500">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeCancelOrderModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                Apakah Anda yakin ingin membatalkan pesanan ini? Pesanan yang dibatalkan tidak dapat dilanjutkan ke pembayaran namun tetap tersimpan sebagai riwayat transaksi.
            </p>

            <form id="cancelOrderForm" method="POST" action="#" class="space-y-4">
                @csrf
                <div>
                    <label for="cancelReasonInput" class="block text-xs font-bold text-slate-700 mb-1">Alasan Pembatalan (Opsional)</label>
                    <textarea id="cancelReasonInput" name="cancel_reason" rows="3" maxlength="500"
                              placeholder="Misal: Salah memilih jenis layanan atau ingin mengubah paket..."
                              class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 focus:bg-white"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeCancelOrderModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                        Tidak, Kembali
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-sm">
                        Ya, Batalkan Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function openCancelOrderModal(button) {
            const modal = document.getElementById('cancelOrderModal');
            const form = document.getElementById('cancelOrderForm');
            const subtitle = document.getElementById('cancelOrderSubtitle');
            const reason = document.getElementById('cancelReasonInput');

            form.action = button.dataset.action || '#';
            subtitle.textContent = (button.dataset.orderNumber || '') + ' — ' + (button.dataset.serviceName || '');
            if (reason) reason.value = '';
            modal.classList.remove('hidden');
        }

        function closeCancelOrderModal() {
            document.getElementById('cancelOrderModal').classList.add('hidden');
        }
    </script>
</body>
</html>

