<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} - IzinYou</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; }
            .invoice-sheet { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased py-8 px-4">

    <div class="max-w-4xl mx-auto mb-5 flex flex-wrap items-center justify-between gap-3 no-print">
        <a href="{{ route('klien.orders.show', $order->id) }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Detail Pesanan</span>
        </a>
        @if(! $order->isCancelled())
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak / Print</span>
                </button>
                <a href="{{ route('klien.orders.invoice.pdf', $order->id) }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Unduh Invoice (PDF)</span>
                </a>
            </div>
        @else
            <div class="px-3.5 py-1.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold rounded-xl flex items-center gap-1.5">
                <i data-lucide="slash" class="w-4 h-4"></i>
                <span>Akses Cetak &amp; Unduh Dinonaktifkan (Pesanan Dibatalkan)</span>
            </div>
        @endif
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xl p-6 sm:p-10 invoice-sheet">
        <!-- Kop Surat -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b-2 border-slate-900 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 flex items-center justify-center p-2 shrink-0">
                    <img src="{{ asset('images/logo-symbol-transparent.png') }}" alt="IzinYou" class="w-10 h-auto object-contain">
                </div>
                <div>
                    <h1 class="text-lg font-black text-slate-900 tracking-tight">CV PRASTA SOLUSI INDONESIA</h1>
                    <p class="text-xs text-slate-500">Platform Manajemen Perizinan &amp; Sertifikasi Bisnis (IzinYou.com)</p>
                    <p class="text-[11px] text-slate-400">Cluster New Liverpool Blok P18A No 46, Babelan, Kab. Bekasi &bull; NPWP: 94.812.334.2-407.000</p>
                </div>
            </div>
            <div class="sm:text-right">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">INVOICE TAGIHAN</span>
                <p class="font-mono text-base font-extrabold text-slate-900">{{ $invoice->invoice_number }}</p>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold mt-1
                    @if($paymentMeta['badge'] === 'emerald') bg-emerald-100 text-emerald-800
                    @elseif($paymentMeta['badge'] === 'rose') bg-rose-100 text-rose-800
                    @elseif($paymentMeta['badge'] === 'sky') bg-sky-100 text-sky-800
                    @else bg-amber-100 text-amber-800 @endif">
                    {{ $paymentMeta['label'] }}
                </span>
            </div>
        </div>

        <!-- Identitas Pelanggan & Metadata Invoice -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 text-xs">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ditagihkan Kepada:</span>
                <p class="text-sm font-extrabold text-slate-900">{{ $invoice->customer_company ?: $invoice->customer_name }}</p>
                <p class="text-slate-700 font-semibold">PIC: {{ $invoice->customer_name }}</p>
                <p class="text-slate-500">{{ $invoice->customer_address ?: '-' }}</p>
                <p class="text-slate-500">{{ $invoice->customer_phone ?: '-' }} &bull; {{ $invoice->customer_email ?: '-' }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5 sm:text-right">
                <p><span class="text-slate-400">No. Pesanan:</span> <strong class="font-mono text-slate-900">{{ $order->order_number }}</strong></p>
                <p><span class="text-slate-400">Tanggal Terbit:</span> <strong class="text-slate-900">{{ $invoice->issued_at->format('d F Y') }}</strong></p>
                <p><span class="text-slate-400">Jatuh Tempo:</span> <strong class="text-slate-900">{{ $invoice->due_date ? $invoice->due_date->format('d F Y') : '-' }}</strong></p>
                <p><span class="text-slate-400">Metode Pembayaran:</span> <strong class="text-slate-900">{{ $order->latestPayment?->payment_method ?? 'Transfer Bank Resmi' }}</strong></p>
            </div>
        </div>

        <!-- Tabel Item Layanan -->
        <div class="overflow-x-auto mb-8">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-900 text-white uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 rounded-tl-xl">No</th>
                        <th class="py-3 px-4">Rincian Layanan</th>
                        <th class="py-3 px-4 text-center">Kuantitas</th>
                        <th class="py-3 px-4 text-right rounded-tr-xl">Harga (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 border border-t-0 border-slate-200">
                    <tr>
                        <td class="py-4 px-4 font-semibold">1</td>
                        <td class="py-4 px-4">
                            <p class="font-bold text-slate-900 text-sm">{{ $invoice->service_name }}</p>
                            <p class="text-slate-500 mt-0.5">Pendampingan perizinan, pemeriksaan kelengkapan berkas legalitas/teknis, dan penerbitan izin resmi.</p>
                        </td>
                        <td class="py-4 px-4 text-center font-semibold">1 Paket</td>
                        <td class="py-4 px-4 text-right font-bold text-slate-900">Rp {{ number_format((float) $invoice->subtotal, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Ringkasan Harga, Diskon, Total -->
        <div class="flex flex-col sm:flex-row justify-between gap-6 items-start">
            <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200 text-xs text-sky-900 space-y-1 max-w-sm w-full">
                <p class="font-bold text-sky-800 uppercase text-[10px] tracking-wider">Rekening Pembayaran Resmi</p>
                <p><strong>Bank Mandiri KCP Bekasi:</strong> 156-00-1928374-1</p>
                <p><strong>Bank BCA:</strong> 522-0918273</p>
                <p><strong>Bank BNI:</strong> 084-9201753</p>
                <p><strong>QRIS Resmi:</strong> CV PRASTA SOLUSI INDONESIA (NMID: ID1026001928374)</p>
                <p>a.n. <strong>CV PRASTA SOLUSI INDONESIA</strong></p>
            </div>

            <div class="w-full sm:w-80 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Harga Layanan</span>
                    <span class="font-semibold text-slate-900">Rp {{ number_format((float) $invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-emerald-600 font-semibold">
                    <span>Potongan Diskon {{ $order->promo_code ? '(' . $order->promo_code . ')' : '' }}</span>
                    <span>- Rp {{ number_format((float) $invoice->discount_amount, 0, ',', '.') }}</span>
                </div>
                <div class="pt-3 border-t-2 border-slate-900 flex justify-between items-center text-base font-black text-slate-900">
                    <span>TOTAL TAGIHAN</span>
                    <span class="text-sky-700">Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>

