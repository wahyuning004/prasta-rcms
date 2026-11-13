<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan {{ $order->order_number }} - IzinYou</title>
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
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="toggleKlienSidebar()" class="lg:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 shrink-0">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base sm:text-xl font-bold text-corporate truncate">{{ $service?->name ?? 'Detail Pesanan' }}</h2>
                        <span class="font-mono text-xs font-bold bg-slate-900 text-white px-2.5 py-0.5 rounded-lg shrink-0">{{ $order->order_number }}</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 truncate">Invoice: {{ $invoice->invoice_number }} &bull; Progres: {{ (int) ($order->progress_percentage ?? 15) }}% ({{ $order->current_stage ?? 'Pesanan Diterima' }})</p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if($order->isCancelled())
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold">
                        <i data-lucide="slash" class="w-4 h-4"></i>
                        <span>Akses Pesanan Dibatalkan</span>
                    </span>
                @else
                    @if($order->canBeCancelled())
                        <button type="button" onclick="document.getElementById('cancelOrderModal').classList.remove('hidden')"
                                class="flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Batalkan Pesanan</span>
                        </button>
                    @endif
                    <a href="{{ route('klien.orders.invoice', $order->id) }}" class="hidden sm:flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-corporate rounded-xl text-xs font-bold transition">
                        <i data-lucide="file-text" class="w-4 h-4 text-sky-600"></i>
                        <span>Lihat Invoice</span>
                    </a>
                    <a href="{{ route('klien.orders.invoice.pdf', $order->id) }}" class="flex items-center gap-1.5 px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-sm transition">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Unduh PDF</span>
                    </a>
                @endif
            </div>
        </header>

        @include('klien.partials.alerts')

        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            <div class="max-w-7xl mx-auto space-y-8">

                <!-- 1. Ringkasan Pesanan, Invoice & Status Pembayaran -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="font-bold text-corporate text-base flex items-center gap-2">
                                <i data-lucide="receipt" class="w-5 h-5 text-sky-600"></i>
                                Ringkasan Tagihan &amp; Invoice
                            </h3>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                                @if($paymentMeta['badge'] === 'emerald') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($paymentMeta['badge'] === 'rose') bg-rose-50 text-rose-700 border border-rose-200
                                @elseif($paymentMeta['badge'] === 'sky') bg-sky-50 text-sky-700 border border-sky-200
                                @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                {{ $paymentMeta['label'] }}
                            </span>
                        </div>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nomor Invoice</span>
                                <strong class="font-mono text-corporate">{{ $invoice->invoice_number }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Tanggal Terbit</span>
                                <span class="font-semibold text-corporate">{{ $invoice->issued_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Harga Normal</span>
                                <span class="font-semibold text-corporate">Rp {{ number_format((float) $invoice->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-emerald-600 font-semibold">
                                <span>Diskon {{ $order->promo_code ? '(' . $order->promo_code . ')' : '' }}</span>
                                <span>- Rp {{ number_format((float) $invoice->discount_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="pt-2.5 border-t border-slate-100 flex justify-between items-center text-sm font-black text-corporate">
                                <span>Total Tagihan</span>
                                <span class="text-lg text-sky-600">Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @if(! $order->isCancelled())
                            <div class="grid grid-cols-2 gap-2.5 pt-2">
                                <a href="{{ route('klien.orders.invoice', $order->id) }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-corporate rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition">
                                    <i data-lucide="eye" class="w-4 h-4 text-sky-600"></i>
                                    <span>Lihat Invoice</span>
                                </a>
                                <a href="{{ route('klien.orders.invoice.pdf', $order->id) }}" class="py-2.5 px-3 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                    <span>Unduh PDF</span>
                                </a>
                            </div>
                        @else
                            <div class="pt-2 border-t border-slate-100">
                                <span class="text-xs text-rose-600 font-semibold block text-center py-2 bg-rose-50 rounded-xl border border-rose-100">
                                    Akses Invoice Dinonaktifkan
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Unggah / Status Bukti Pembayaran -->
                    <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm" id="pembayaran">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                            <h3 class="font-bold text-corporate text-base flex items-center gap-2">
                                <i data-lucide="credit-card" class="w-5 h-5 text-sky-600"></i>
                                Pembayaran &amp; Unggah Bukti Pembayaran
                            </h3>
                            <span class="text-xs font-bold {{ $order->isCancelled() ? 'text-rose-600' : 'text-slate-500' }}">
                                Status Pesanan: {{ $order->statusLabel() }}
                            </span>
                        </div>

                        @php
                            $latestPayment = $order->latestPayment;
                            $selectedMethod = old('payment_method', 'Transfer Bank Mandiri');
                            if (! array_key_exists($selectedMethod, $paymentMethods)) {
                                $selectedMethod = 'Transfer Bank Mandiri';
                            }
                        @endphp

                        @if($order->isCancelled())
                            <div class="mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-900 space-y-1.5">
                                <div class="flex items-center gap-2 font-bold text-rose-900">
                                    <i data-lucide="ban" class="w-4 h-4 text-rose-600"></i>
                                    <span>Pesanan Telah Dibatalkan</span>
                                </div>
                                <p>{{ $order->cancellationReason() ?: 'Pesanan ini telah dibatalkan oleh pelanggan dan tetap tersimpan sebagai riwayat transaksi.' }}</p>
                            </div>
                        @elseif($latestPayment && $latestPayment->status === 'failed')
                            <div class="mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                                <div class="flex items-center gap-2 font-bold text-rose-900">
                                    <i data-lucide="x-circle" class="w-4 h-4 text-rose-600"></i>
                                    <span>Bukti Pembayaran Ditolak</span>
                                </div>
                                <p><strong>Alasan Penolakan:</strong> {{ $latestPayment->rejection_reason ?: 'Bukti transfer tidak sesuai. Silakan unggah ulang bukti yang valid.' }}</p>
                                <p class="text-[11px] text-rose-700">Anda dapat mengunggah ulang bukti pembayaran yang benar menggunakan formulir di bawah ini.</p>
                            </div>
                        @elseif($latestPayment && $latestPayment->status === 'waiting_confirmation')
                            <div class="mb-4 p-4 rounded-2xl bg-sky-50 border border-sky-200 text-xs text-sky-900 space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-bold flex items-center gap-1.5">
                                        <i data-lucide="clock" class="w-4 h-4 text-sky-600"></i>
                                        Menunggu Verifikasi Petugas Finance
                                    </span>
                                    <a href="{{ route('payments.proof', $latestPayment->id) }}" target="_blank" class="px-3 py-1 bg-white border border-sky-300 rounded-lg text-sky-700 font-bold hover:bg-sky-100 transition">
                                        Lihat Bukti Diunggah
                                    </a>
                                </div>
                                <p>Metode: <strong>{{ $latestPayment->payment_method }}</strong> (a.n. {{ $latestPayment->sender_name }}) &bull; Nominal: <strong>Rp {{ number_format((float) $latestPayment->amount, 0, ',', '.') }}</strong></p>
                            </div>
                        @elseif($latestPayment && $latestPayment->status === 'success')
                            <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-bold flex items-center gap-1.5">
                                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                                        Pembayaran Berhasil Diverifikasi (Lunas)
                                    </span>
                                    @if($latestPayment->payment_proof)
                                        <a href="{{ route('payments.proof', $latestPayment->id) }}" target="_blank" class="px-3 py-1 bg-white border border-emerald-300 rounded-lg text-emerald-700 font-bold hover:bg-emerald-100 transition">
                                            Lihat Bukti
                                        </a>
                                    @endif
                                </div>
                                <p>Diverifikasi pada {{ $latestPayment->verified_at?->format('d M Y, H:i') ?? $latestPayment->updated_at->format('d M Y, H:i') }} WIB &bull; Rp {{ number_format((float) $latestPayment->amount, 0, ',', '.') }}</p>
                            </div>
                        @endif

                        @if(! $order->isCancelled() && (! $latestPayment || $latestPayment->status === 'failed'))
                            <form action="{{ route('klien.orders.payment.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="paymentMethodSelect" class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran *</label>
                                    <select id="paymentMethodSelect" name="payment_method" required onchange="updatePaymentMethodDetails()"
                                            class="w-full px-3.5 py-2.5 text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500">
                                        @foreach($paymentMethods as $methodKey => $methodInfo)
                                            <option value="{{ $methodKey }}" @selected($selectedMethod === $methodKey)>{{ $methodKey }}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                                </div>

                                <!-- Detail Tujuan Pembayaran Dinamis sesuai Metode Terpilih -->
                                <div id="paymentMethodDetails" class="space-y-3">
                                    @foreach($paymentMethods as $methodKey => $methodInfo)
                                        @if($methodInfo['type'] === 'bank')
                                            <div data-payment-panel="{{ $methodKey }}"
                                                 class="p-4 rounded-2xl bg-sky-50/70 border border-sky-200 text-xs space-y-2 {{ $selectedMethod === $methodKey ? '' : 'hidden' }}">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="font-bold text-sky-900">{{ $methodInfo['bank_name'] }} ({{ $methodInfo['branch'] }})</span>
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-sky-700 border border-sky-200">Rekening Resmi</span>
                                                </div>
                                                <div class="flex flex-wrap items-center justify-between gap-2 bg-white px-3.5 py-2.5 rounded-xl border border-sky-100">
                                                    <div>
                                                        <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">Nomor Rekening Tujuan</span>
                                                        <strong class="font-mono text-sm sm:text-base font-black text-corporate">{{ $methodInfo['account_number'] }}</strong>
                                                    </div>
                                                    <button type="button" onclick="copyPaymentAccount('{{ $methodInfo['account_number'] }}')"
                                                            class="px-3 py-1.5 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold text-[11px] transition">
                                                        Salin Rekening
                                                    </button>
                                                </div>
                                                <p class="text-slate-600">Atas Nama Pemilik Rekening: <strong class="text-corporate">{{ $methodInfo['account_holder'] }}</strong></p>
                                            </div>
                                        @else
                                            <div data-payment-panel="{{ $methodKey }}"
                                                 class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs {{ $selectedMethod === $methodKey ? '' : 'hidden' }}">
                                                <div class="flex flex-col sm:flex-row items-center gap-4">
                                                    <div class="w-36 shrink-0 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm text-center">
                                                        <img src="{{ asset($methodInfo['qr_image']) }}" alt="QRIS {{ $methodInfo['merchant_name'] }}" class="w-full h-auto rounded-lg">
                                                    </div>
                                                    <div class="flex-1 space-y-2 text-center sm:text-left">
                                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">QRIS Nasional (M-Banking &amp; E-Wallet)</span>
                                                        <div>
                                                            <p class="text-[11px] text-slate-500">Nama Merchant</p>
                                                            <p class="font-extrabold text-corporate text-sm">{{ $methodInfo['merchant_name'] }}</p>
                                                        </div>
                                                        <div class="flex flex-wrap justify-center sm:justify-start gap-x-4 gap-y-1 text-[11px] text-slate-600">
                                                            <span>NMID: <strong class="font-mono text-corporate">{{ $methodInfo['nmid'] }}</strong></span>
                                                            <span>Nominal: <strong class="text-sky-700">Rp {{ number_format((float) $order->total_cost, 0, ',', '.') }}</strong></span>
                                                        </div>
                                                        <p class="text-[11px] text-slate-500 leading-relaxed">
                                                            Pindai QRIS menggunakan BCA Mobile, Livin' Mandiri, BNI Mobile, GoPay, OVO, atau DANA, lalu unggah bukti transaksi berhasil di bawah ini.
                                                        </p>
                                                        <button type="button" onclick="document.getElementById('qrisPreviewModal').classList.remove('hidden')"
                                                                class="px-3.5 py-2 bg-corporate hover:bg-slate-800 text-white rounded-xl text-[11px] font-bold inline-flex items-center gap-1.5 transition shadow-sm">
                                                            <i data-lucide="zoom-in" class="w-3.5 h-3.5"></i>
                                                            <span>Lihat QRIS Lebih Jelas</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label id="senderNameLabel" class="block text-xs font-bold text-slate-700 mb-1">Nama Pemilik Rekening / Pengirim *</label>
                                        <input id="senderNameInput" type="text" name="sender_name" value="{{ old('sender_name', $user->name) }}" required placeholder="Nama sesuai bukti pembayaran" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Pembayaran (Rp) *</label>
                                        <input type="number" name="amount" value="{{ old('amount', (int) $order->total_cost) }}" min="{{ (int) $order->total_cost }}" required class="w-full px-3.5 py-2.5 text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">File Bukti Pembayaran (JPG, PNG, PDF, Maks. 5 MB) *</label>
                                        <input type="file" name="payment_proof" required accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 border border-slate-200 rounded-xl bg-slate-50 p-1">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan / Referensi Transaksi (Opsional)</label>
                                        <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Nomor referensi / jam pembayaran" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500">
                                    </div>
                                </div>

                                <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs rounded-xl shadow-md flex items-center gap-2 transition">
                                    <i data-lucide="upload" class="w-4 h-4"></i>
                                    <span>{{ $latestPayment && $latestPayment->status === 'failed' ? 'Unggah Ulang Bukti Pembayaran' : 'Kirim Bukti Pembayaran' }}</span>
                                </button>
                            </form>
                        @endif

                        @if($order->paymentLogs->isNotEmpty())
                            <div class="mt-5 pt-4 border-t border-slate-100">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Riwayat Status Pembayaran:</p>
                                <div class="space-y-1.5 text-xs">
                                    @foreach($order->paymentLogs as $log)
                                        <div class="flex items-center justify-between gap-2 text-slate-600 bg-slate-50 px-3 py-2 rounded-xl">
                                            <span><strong>{{ strtoupper($log->status) }}</strong> — {{ $log->notes }}</span>
                                            <span class="text-[11px] text-slate-400 shrink-0">{{ $log->created_at->format('d M Y H:i') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. Pelacakan Progres Proyek (Tracking) -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm" id="tracking">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-6 border-b border-slate-100">
                        <div>
                            <h3 class="text-lg font-bold text-corporate flex items-center gap-2">
                                <i data-lucide="git-merge" class="w-5 h-5 text-sky-600"></i>
                                Pelacakan Progres Pengerjaan Proyek
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Tahapan terkini: <strong class="text-sky-700">{{ $order->current_stage ?? 'Pesanan Diterima' }}</strong></p>
                        </div>
                        <div class="w-full sm:w-64">
                            <div class="flex items-center justify-between text-xs font-bold mb-1">
                                <span class="text-slate-500">Total Progres</span>
                                <span class="text-sky-600">{{ (int) ($order->progress_percentage ?? 15) }}%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-sky-600 rounded-full transition-all" style="width: {{ min(100, (int) ($order->progress_percentage ?? 15)) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 6-Stage Timeline -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
                        @foreach($timeline as $step)
                            <div class="p-4 rounded-2xl border transition
                                @if($step['status'] === 'completed') bg-emerald-50/60 border-emerald-200
                                @elseif($step['status'] === 'in_progress') bg-sky-50 border-sky-300 ring-1 ring-sky-200
                                @else bg-slate-50 border-slate-200/80 @endif">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                        @if($step['status'] === 'completed') bg-emerald-600 text-white
                                        @elseif($step['status'] === 'in_progress') bg-sky-600 text-white
                                        @else bg-slate-200 text-slate-500 @endif">
                                        @if($step['status'] === 'completed')
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        @elseif($step['status'] === 'in_progress')
                                            <i data-lucide="loader" class="w-4 h-4"></i>
                                        @else
                                            {{ $step['order'] }}
                                        @endif
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                                        @if($step['status'] === 'completed') bg-emerald-100 text-emerald-800
                                        @elseif($step['status'] === 'in_progress') bg-sky-100 text-sky-800
                                        @else bg-slate-200 text-slate-600 @endif">
                                        {{ $step['status'] === 'completed' ? 'Selesai' : ($step['status'] === 'in_progress' ? 'Berjalan' : 'Menunggu') }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-corporate text-xs mb-1">{{ $step['name'] }}</h4>
                                <p class="text-[11px] text-slate-600 leading-snug mb-2">{{ $step['description'] }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">{{ $step['updated_at'] ?? 'Belum dimulai' }}</p>
                            </div>
                        @endforeach
                    </div>

                    <!-- Riwayat Log Perubahan Progres -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Riwayat Perubahan Status Proyek:</h4>
                        <div class="space-y-2.5">
                            @forelse($order->trackings->sortByDesc('id') as $track)
                                <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                                    <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 mt-0.5">
                                        <i data-lucide="activity" class="w-4 h-4"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <span class="font-bold text-corporate">{{ $track->stage_name }}
                                                <span class="ml-1.5 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-white border border-slate-200 text-slate-600">
                                                    {{ $track->progress_percentage ?? $order->progress_percentage }}%
                                                </span>
                                            </span>
                                            <span class="text-[11px] text-slate-400">{{ $track->date_recorded?->format('d M Y, H:i') }} WIB</span>
                                        </div>
                                        <p class="text-slate-600 mt-1">{{ $track->description }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">Belum ada riwayat perubahan progres.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- 3. Unggah & Manajemen Dokumen + Riwayat Revisi (Versioning) -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm" id="dokumen">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-6 border-b border-slate-100">
                        <div>
                            <h3 class="text-lg font-bold text-corporate flex items-center gap-2">
                                <i data-lucide="folder-check" class="w-5 h-5 text-sky-600"></i>
                                Unggah &amp; Manajemen Dokumen Persyaratan
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Unggah dokumen legalitas &amp; teknis. Setiap pembaruan dokumen tersimpan dalam riwayat revisi tanpa menghapus versi lama.</p>
                        </div>
                    </div>

                    @foreach([
                        ['title' => 'Dokumen Wajib', 'badge' => 'Wajib', 'items' => $mandatoryRequirements],
                        ['title' => 'Dokumen Opsional / Pendukung', 'badge' => 'Opsional', 'items' => $optionalRequirements],
                    ] as $group)
                        @if($group['items']->isNotEmpty())
                            <div class="mb-8 last:mb-0">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] {{ $group['badge'] === 'Wajib' ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $group['badge'] }}
                                    </span>
                                    <span>{{ $group['title'] }} ({{ $group['items']->count() }})</span>
                                </h4>

                                <div class="space-y-4">
                                    @foreach($group['items'] as $req)
                                        @php $doc = $docsByRequirement->get($req->id); @endphp
                                        <div class="p-4 sm:p-5 rounded-2xl border {{ $doc && $doc->status === 'rejected' ? 'border-rose-300 bg-rose-50/30' : ($doc && $doc->status === 'approved' ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200 bg-slate-50/60') }}">
                                            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                                                <div class="space-y-1.5 flex-1">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="font-bold text-corporate text-sm">{{ $req->document_name }}</span>
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                                            {{ $req->document_type ?? 'Persyaratan Teknis' }}
                                                        </span>
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $req->is_mandatory ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-600' }}">
                                                            {{ $req->is_mandatory ? 'Wajib' : 'Opsional' }}
                                                        </span>
                                                    </div>
                                                    @if($req->description)
                                                        <p class="text-xs text-slate-500">{{ $req->description }}</p>
                                                    @endif

                                                    @if($doc)
                                                        <div class="pt-2 flex flex-wrap items-center gap-3 text-xs">
                                                            <span class="px-2.5 py-1 rounded-full font-bold text-[11px]
                                                                @if($doc->status === 'approved') bg-emerald-100 text-emerald-800
                                                                @elseif($doc->status === 'rejected') bg-rose-100 text-rose-800
                                                                @else bg-amber-100 text-amber-800 @endif">
                                                                {{ $doc->statusLabel() }}
                                                            </span>
                                                            <span class="font-mono font-bold text-sky-700">Versi v{{ $doc->version }}</span>
                                                            <span class="text-slate-600 font-medium">{{ $doc->original_filename ?: basename($doc->file_path) }} ({{ $doc->file_size ?: '-' }})</span>
                                                            <span class="text-slate-400">Diunggah: {{ $doc->updated_at->format('d M Y, H:i') }}</span>
                                                            <a href="{{ route('documents.current.download', $doc->id) }}" class="text-sky-600 font-bold hover:underline flex items-center gap-1">
                                                                <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh
                                                            </a>
                                                        </div>

                                                        @if($doc->notes)
                                                            <div class="mt-2 p-3 rounded-xl text-xs {{ $doc->status === 'rejected' ? 'bg-rose-100/70 text-rose-900 border border-rose-200' : 'bg-white text-slate-700 border border-slate-200' }}">
                                                                <strong>{{ $doc->status === 'rejected' ? 'Alasan / Catatan Revisi:' : 'Catatan Pemeriksaan:' }}</strong>
                                                                {{ $doc->notes }}
                                                            </div>
                                                        @endif
                                                    @else
                                                        <p class="text-xs text-amber-700 font-semibold pt-1">Belum ada dokumen yang diunggah.</p>
                                                    @endif
                                                </div>

                                                @if(! $order->isCancelled())
                                                    <!-- Form Upload / Upload Versi Baru -->
                                                    <form action="{{ route('klien.orders.documents.upload', $order->id) }}" method="POST" enctype="multipart/form-data" class="w-full lg:w-80 bg-white p-3.5 rounded-xl border border-slate-200 space-y-2.5 shrink-0">
                                                        @csrf
                                                        <input type="hidden" name="service_requirement_id" value="{{ $req->id }}">

                                                        <div>
                                                            <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                                                {{ $doc ? 'Unggah Versi Terbaru (v' . ($doc->version + 1) . ')' : 'Pilih Berkas Dokumen' }}
                                                            </label>
                                                            <input type="file" name="document_file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                                                                   class="w-full text-[11px] text-slate-600 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-sky-50 file:text-sky-700">
                                                        </div>

                                                        <div>
                                                            <input type="text" name="revision_note"
                                                                   {{ $doc && $doc->status === 'rejected' ? 'required' : '' }}
                                                                   placeholder="{{ $doc ? 'Keterangan revisi (wajib jika revisi)' : 'Keterangan dokumen (opsional)' }}"
                                                                   class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-sky-500">
                                                        </div>

                                                        <button type="submit" class="w-full py-2 px-3 bg-corporate hover:bg-slate-800 text-white rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition">
                                                            <i data-lucide="upload-cloud" class="w-3.5 h-3.5 text-sky-400"></i>
                                                            <span>{{ $doc ? 'Unggah Versi Baru' : 'Unggah Dokumen' }}</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <div class="w-full lg:w-80 bg-slate-50 p-4 rounded-xl border border-dashed border-slate-200 text-center shrink-0">
                                                        <span class="text-xs text-slate-400 italic">Unggah berkas dinonaktifkan (Pesanan Dibatalkan)</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Riwayat Revisi Dokumen (File Versioning) -->
                                            @if($doc && $doc->versions->isNotEmpty())
                                                <div class="mt-4 pt-3 border-t border-slate-200/80">
                                                    <button type="button" onclick="document.getElementById('versions-{{ $doc->id }}').classList.toggle('hidden')"
                                                            class="text-xs font-bold text-sky-700 hover:text-sky-800 flex items-center gap-1.5">
                                                        <i data-lucide="history" class="w-3.5 h-3.5"></i>
                                                        <span>Riwayat Revisi Dokumen ({{ $doc->versions->count() }} Versi Tersimpan)</span>
                                                    </button>

                                                    <div id="versions-{{ $doc->id }}" class="mt-2.5 space-y-2">
                                                        @foreach($doc->versions as $ver)
                                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs">
                                                                <div class="space-y-0.5">
                                                                    <div class="flex items-center gap-2 flex-wrap">
                                                                        <span class="font-mono font-bold px-2 py-0.5 rounded bg-slate-900 text-white text-[10px]">v{{ $ver->version_number }}</span>
                                                                        <span class="font-semibold text-corporate">{{ $ver->original_filename }}</span>
                                                                        <span class="text-slate-400">({{ $ver->file_size ?: '-' }})</span>
                                                                        @if($ver->version_number === $doc->version)
                                                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">Versi Terbaru</span>
                                                                        @else
                                                                            <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">Versi Sebelumnya</span>
                                                                        @endif
                                                                    </div>
                                                                    <p class="text-[11px] text-slate-500">
                                                                        Diunggah: {{ $ver->created_at->format('d M Y, H:i') }} &bull; Keterangan: {{ $ver->revision_notes ?: '-' }}
                                                                    </p>
                                                                    @if($ver->reviewer_notes)
                                                                        <p class="text-[11px] text-rose-700">Catatan Petugas: {{ $ver->reviewer_notes }}</p>
                                                                    @endif
                                                                </div>
                                                                <a href="{{ route('documents.version.download', $ver->id) }}" class="px-3 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold rounded-lg text-[11px] flex items-center gap-1 shrink-0 self-start sm:self-center">
                                                                    <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh v{{ $ver->version_number }}
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- 4. Rating & Ulasan Layanan -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm" id="ulasan">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                        <div>
                            <h3 class="text-lg font-bold text-corporate flex items-center gap-2">
                                <i data-lucide="star" class="w-5 h-5 text-amber-500"></i>
                                Penilaian &amp; Ulasan Layanan
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Berikan rating bintang 1–5 dan ulasan setelah proyek perizinan selesai.</p>
                        </div>
                    </div>

                    @if($order->status === 'completed')
                        @php $currentRating = (int) old('rating', $order->review?->rating ?? 5); @endphp
                        <form action="{{ route('klien.orders.review.store', $order->id) }}" method="POST" class="space-y-4 max-w-2xl">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Rating Layanan (1 – 5 Bintang) *</label>
                                <div class="flex items-center gap-2" id="starSelector">
                                    @for($star = 1; $star <= 5; $star++)
                                        <label class="cursor-pointer flex items-center gap-1 px-3 py-2 rounded-xl border transition {{ $currentRating === $star ? 'bg-amber-50 border-amber-400 text-amber-700 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                                            <input type="radio" name="rating" value="{{ $star }}" {{ $currentRating === $star ? 'checked' : '' }} class="accent-amber-500">
                                            <span class="text-xs">{{ $star }} ★</span>
                                        </label>
                                    @endfor
                                </div>
                            </div>

                            <div>
                                <label for="reviewComment" class="block text-xs font-bold text-slate-700 mb-1.5">Ulasan / Komentar Anda *</label>
                                <textarea id="reviewComment" name="comment" rows="3" required minlength="5" maxlength="1000"
                                          placeholder="Bagikan pengalaman Anda menggunakan layanan pendampingan perizinan kami..."
                                          class="w-full px-4 py-3 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 focus:bg-white">{{ old('comment', $order->review?->comment) }}</textarea>
                            </div>

                            <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                <span>{{ $order->review ? 'Perbarui Penilaian & Ulasan' : 'Kirim Penilaian & Ulasan' }}</span>
                            </button>
                        </form>
                    @else
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center gap-3">
                            <i data-lucide="lock" class="w-5 h-5 text-slate-400 shrink-0"></i>
                            <span>Fitur penilaian dan ulasan akan terbuka secara otomatis setelah proyek berstatus <strong>Selesai</strong> (100%).</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </main>

    <!-- Modal Zoom QRIS -->
    @if(isset($paymentMethods['QRIS']))
        <div id="qrisPreviewModal" class="hidden fixed inset-0 z-50 bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4"
             onclick="if (event.target === this) this.classList.add('hidden')">
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-center space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-corporate">QRIS Resmi IzinYou</h3>
                    <button type="button" onclick="document.getElementById('qrisPreviewModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <img src="{{ asset($paymentMethods['QRIS']['qr_image']) }}" alt="QRIS {{ $paymentMethods['QRIS']['merchant_name'] }}" class="w-full max-w-[280px] mx-auto rounded-2xl border border-slate-200">
                <div class="text-xs text-slate-600 space-y-0.5">
                    <p class="font-bold text-corporate">{{ $paymentMethods['QRIS']['merchant_name'] }}</p>
                    <p>NMID: <span class="font-mono font-semibold">{{ $paymentMethods['QRIS']['nmid'] }}</span></p>
                    <p class="font-bold text-sky-700 pt-1">Tagihan: Rp {{ number_format((float) $order->total_cost, 0, ',', '.') }}</p>
                </div>
                <button type="button" onclick="document.getElementById('qrisPreviewModal').classList.add('hidden')" class="w-full py-2.5 bg-corporate hover:bg-slate-800 text-white rounded-xl text-xs font-bold">
                    Tutup
                </button>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Pembatalan Pesanan -->
    @if($order->canBeCancelled())
        <div id="cancelOrderModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
             onclick="if (event.target === this) this.classList.add('hidden')">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
                <div class="flex items-start justify-between pb-3 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-corporate">Konfirmasi Pembatalan Pesanan</h3>
                            <p class="text-xs text-slate-500">{{ $order->order_number }} — {{ $service?->name ?? 'Layanan Perizinan' }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('cancelOrderModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Apakah Anda yakin ingin membatalkan pesanan ini? Pesanan yang dibatalkan tidak dapat dibayar lagi namun tetap tersimpan sebagai riwayat transaksi.
                </p>

                <form action="{{ route('klien.orders.cancel', $order->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="cancelReasonShow" class="block text-xs font-bold text-slate-700 mb-1">Alasan Pembatalan (Opsional)</label>
                        <textarea id="cancelReasonShow" name="cancel_reason" rows="3" maxlength="500"
                                  placeholder="Misal: Salah klik layanan atau ingin mengubah pesanan..."
                                  class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 focus:bg-white"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('cancelOrderModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                            Tidak, Kembali
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-sm">
                            Ya, Batalkan Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        lucide.createIcons();

        function updatePaymentMethodDetails() {
            const select = document.getElementById('paymentMethodSelect');
            if (!select) return;
            const chosen = select.value;

            document.querySelectorAll('[data-payment-panel]').forEach((panel) => {
                panel.classList.toggle('hidden', panel.getAttribute('data-payment-panel') !== chosen);
            });

            const senderLabel = document.getElementById('senderNameLabel');
            const senderInput = document.getElementById('senderNameInput');
            if (senderLabel && senderInput) {
                if (chosen === 'QRIS') {
                    senderLabel.textContent = 'Nama Akun / Pengirim QRIS *';
                    senderInput.placeholder = 'Nama pemilik akun e-wallet / m-banking';
                } else {
                    senderLabel.textContent = 'Nama Pemilik Rekening Pengirim *';
                    senderInput.placeholder = 'Nama sesuai buku tabungan / bukti transfer';
                }
            }
        }

        function copyPaymentAccount(accountNumber) {
            navigator.clipboard?.writeText(accountNumber);
            alert('Nomor rekening ' + accountNumber + ' berhasil disalin.');
        }

        updatePaymentMethodDetails();

        const starSelector = document.getElementById('starSelector');
        if (starSelector) {
            const radios = starSelector.querySelectorAll('input[name="rating"]');
            radios.forEach((radio) => {
                radio.addEventListener('change', () => {
                    radios.forEach((r) => {
                        const label = r.closest('label');
                        if (!label) return;
                        if (r.checked) {
                            label.classList.add('bg-amber-50', 'border-amber-400', 'text-amber-700', 'font-bold');
                            label.classList.remove('bg-slate-50', 'border-slate-200', 'text-slate-600');
                        } else {
                            label.classList.remove('bg-amber-50', 'border-amber-400', 'text-amber-700', 'font-bold');
                            label.classList.add('bg-slate-50', 'border-slate-200', 'text-slate-600');
                        }
                    });
                });
            });
        }
    </script>
</body>
</html>

