<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Tagihan &amp; Invoice - Finance &amp; Administration</title>
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
        .text-gold { color: #d4af37; }
        .bg-gold { background-color: #d4af37; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-amber-600 selection:text-white flex h-screen overflow-hidden">

    <!-- Dark Sidebar -->
    @include('finance.partials.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/60">
        
        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-corporate">Manajemen &amp; Penerbitan Invoice Tagihan</h2>
                    <span class="bg-blue-100 text-blue-800 text-[11px] font-bold px-2.5 py-0.5 rounded-full">Billing &amp; Receivables</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Kelola tagihan transaksi perizinan, batas jatuh tempo, dan penerbitan invoice resmi untuk pelanggan.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="openIssueInvoiceModal()" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Terbitkan Invoice Tagihan</span>
                </button>
                <a href="{{ route('finance.export.xlsx') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all" title="Unduh Buku Transaksi format Excel (.xlsx)">
                    <i data-lucide="sheet" class="w-4 h-4"></i>
                    <span>Ekspor Excel (.xlsx)</span>
                </a>
                <a href="{{ route('finance.export.pdf') }}" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all" title="Unduh Buku Transaksi format PDF (.pdf)">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Ekspor PDF (.pdf)</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @include('finance.partials.alerts')

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 pt-4 space-y-6">

            <!-- 1. Statistik Tagihan & Piutang -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- Card 1: Total Nilai Tagihan Terbit -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Akumulasi Tagihan</p>
                            <h3 class="text-2xl font-black text-corporate mt-1">Rp {{ number_format($invoiceMetrics['totalBilled'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-slate-100 text-corporate rounded-xl flex items-center justify-center font-bold">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500">Dari {{ $invoices->total() }} invoice resmi terbit</p>
                </div>

                <!-- Card 2: Tagihan Terkumpul (Lunas) -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Terbayar (Kas Masuk)</p>
                            <h3 class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($invoiceMetrics['totalCollected'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-bold">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-xs text-emerald-700 font-medium">{{ $stats['paidInvoices'] ?? 0 }} invoice berstatus lunas</p>
                </div>

                <!-- Card 3: Piutang Usaha (Unpaid) -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Piutang Berjalan (Unpaid)</p>
                            <h3 class="text-2xl font-black text-blue-600 mt-1">Rp {{ number_format($invoiceMetrics['totalUnpaid'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-bold">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-xs text-blue-700 font-medium">Menunggu pelunasan klien</p>
                </div>

                <!-- Card 4: Tagihan Melewati Jatuh Tempo -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Melewati Jatuh Tempo</p>
                            <h3 class="text-2xl font-black text-rose-600 mt-1">{{ $invoiceMetrics['overdueCount'] ?? 0 }} <span class="text-xs font-normal text-slate-500">Invoice</span></h3>
                        </div>
                        <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center font-bold">
                            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-xs text-rose-600 font-medium">Perlu pengingat penagihan (Follow-up)</p>
                </div>

            </div>

            <!-- 2. Filter & Pencarian Tagihan -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                
                <form action="{{ route('finance.invoices') }}" method="GET" class="space-y-4">
                    
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                        
                        <!-- Status Tabs Filter -->
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-2 lg:pb-0">
                            <a href="{{ route('finance.invoices', array_merge(request()->query(), ['status' => 'all'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($filterStatus === 'all' || !$filterStatus) ? 'bg-corporate text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                Semua Tagihan ({{ $invoices->total() }})
                            </a>
                            <a href="{{ route('finance.invoices', array_merge(request()->query(), ['status' => 'unpaid'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterStatus === 'unpaid' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                Menunggu Pembayaran
                            </a>
                            <a href="{{ route('finance.invoices', array_merge(request()->query(), ['status' => 'paid'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterStatus === 'paid' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                Lunas (Paid)
                            </a>
                            <a href="{{ route('finance.invoices', array_merge(request()->query(), ['status' => 'overdue'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterStatus === 'overdue' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                Terlewat Tempo (Overdue)
                            </a>
                            <a href="{{ route('finance.invoices', array_merge(request()->query(), ['status' => 'cancelled'])) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterStatus === 'cancelled' ? 'bg-slate-700 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                Dibatalkan
                            </a>
                        </div>

                        <!-- Search Input -->
                        <div class="relative w-full lg:w-72">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                            <input type="text" name="search" value="{{ $searchTerm ?? '' }}" placeholder="Cari invoice, order, klien..." class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-600">
                        </div>

                    </div>

                    <!-- Date Range Filter Row -->
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-3">
                        <span class="text-xs font-bold text-slate-500 flex items-center gap-1">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i> Rentang Terbit:
                        </span>
                        <div class="flex items-center gap-2">
                            <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white">
                            <span class="text-xs text-slate-400">s/d</span>
                            <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 focus:bg-white">
                        </div>

                        <button type="submit" class="px-3 py-1.5 bg-corporate hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-all flex items-center gap-1">
                            <i data-lucide="filter" class="w-3.5 h-3.5"></i> Terapkan
                        </button>

                        @if(!empty($searchTerm) || !empty($startDate) || !empty($endDate) || ($filterStatus && $filterStatus !== 'all'))
                            <a href="{{ route('finance.invoices') }}" class="text-xs text-rose-600 font-bold hover:underline flex items-center gap-1">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Reset Filter
                            </a>
                        @endif
                    </div>

                </form>

                <!-- Tabel Data Invoice -->
                <div class="overflow-x-auto mt-6">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-4 px-6">No. Invoice &amp; Terbit</th>
                                <th class="py-4 px-6">Klien &amp; Pesanan</th>
                                <th class="py-4 px-6">Layanan Perizinan</th>
                                <th class="py-4 px-6">Nominal Tagihan</th>
                                <th class="py-4 px-6">Jatuh Tempo</th>
                                <th class="py-4 px-6">Status Tagihan</th>
                                <th class="py-4 px-6 text-right">Aksi Tagihan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($invoices as $inv)
                                @php
                                    $dueMeta = $inv->dueStatusMeta();
                                    $colorClass = match($inv->payment_status) {
                                        'paid', 'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'waiting_verification' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'rejected', 'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        default => 'bg-blue-50 text-blue-700 border-blue-200',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="file-text" class="w-4 h-4 text-amber-600"></i>
                                            <span class="font-mono text-xs font-extrabold text-corporate">{{ $inv->invoice_number }}</span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Terbit: {{ $inv->issued_at ? date('d M Y', strtotime($inv->issued_at)) : '-' }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-corporate text-xs">{{ $inv->customer_company ?: $inv->customer_name }}</p>
                                        <p class="text-[11px] text-slate-500 font-mono">Order: {{ $inv->order?->order_number ?? '-' }}</p>
                                        @if($inv->customer_phone)
                                            <p class="text-[10px] text-slate-400">Tel: {{ $inv->customer_phone }}</p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-xs font-semibold text-slate-700">{{ $inv->service_name }}</p>
                                        @if($inv->discount_amount > 0)
                                            <span class="inline-block text-[10px] bg-indigo-50 text-indigo-700 font-semibold px-2 py-0.5 rounded-full mt-0.5">
                                                Diskon: -Rp {{ number_format($inv->discount_amount, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-extrabold text-corporate text-xs">Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</p>
                                        @if($inv->discount_amount > 0)
                                            <p class="text-[10px] text-slate-400 line-through">Rp {{ number_format($inv->subtotal, 0, ',', '.') }}</p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex flex-col items-start gap-1">
                                            <span class="text-xs font-medium text-slate-700">
                                                {{ $inv->due_date ? date('d M Y', strtotime($inv->due_date)) : 'Tanpa Batas' }}
                                            </span>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                                                {{ $dueMeta['color'] === 'rose' ? 'bg-rose-100 text-rose-800' : ($dueMeta['color'] === 'amber' ? 'bg-amber-100 text-amber-800' : ($dueMeta['color'] === 'emerald' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600')) }}">
                                                {{ $dueMeta['label'] }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        @php
                                            $isInvCancelled = $inv->payment_status === 'cancelled' || ($inv->order && $inv->order->isCancelled());
                                            $invCancelReason = $inv->order?->cancellationReason();
                                        @endphp
                                        <span class="inline-flex items-center gap-1 border px-2.5 py-0.5 rounded-full text-xs font-bold {{ $colorClass }}">
                                            @if($inv->isPaid())
                                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                            @elseif($inv->payment_status === 'waiting_verification')
                                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                            @elseif($isInvCancelled)
                                                <i data-lucide="ban" class="w-3.5 h-3.5 text-rose-600"></i>
                                            @else
                                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                            @endif
                                            <span>{{ $inv->statusLabel() }}</span>
                                        </span>
                                        @if($isInvCancelled && $invCancelReason)
                                            <div class="mt-1.5 p-1.5 bg-rose-50 border border-rose-200/80 rounded-lg text-[10px] text-rose-900 leading-tight max-w-[220px]">
                                                <span class="font-bold text-rose-700 block">Alasan Pembatalan:</span>
                                                <span class="text-slate-600">{{ Str::limit($invCancelReason, 55) }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if($isInvCancelled)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-500 bg-rose-50 px-2 py-1 rounded-lg border border-rose-100">
                                                <i data-lucide="slash" class="w-3 h-3"></i> Akses Dinonaktifkan
                                            </span>
                                        @else
                                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                                <!-- Cetak / Buka HTML -->
                                                <a href="{{ route('finance.invoice.generate', $inv->order_id) }}" target="_blank" class="px-2.5 py-1.5 bg-corporate hover:bg-slate-800 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Buka / Cetak Invoice">
                                                    <i data-lucide="printer" class="w-3.5 h-3.5 text-gold"></i> Cetak
                                                </a>
                                                <!-- Unduh PDF -->
                                                <a href="{{ route('finance.invoice.pdf', $inv->order_id) }}" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Unduh PDF Resmi">
                                                    <i data-lucide="download" class="w-3.5 h-3.5"></i> PDF
                                                </a>
                                                <!-- Ubah Status & Tempo -->
                                                <button type="button" onclick="openEditInvoiceModal({{ $inv->id }}, '{{ addslashes($inv->invoice_number) }}', '{{ $inv->payment_status }}', '{{ $inv->due_date ? date('Y-m-d', strtotime($inv->due_date)) : '' }}')" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold flex items-center gap-1 transition-all" title="Ubah Status / Tempo">
                                                    <i data-lucide="settings-2" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                            <i data-lucide="file-question" class="w-6 h-6"></i>
                                        </div>
                                        <p class="font-bold text-slate-600">Tidak ada data invoice ditemukan</p>
                                        <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter status atau kata kunci pencarian Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($invoices->hasPages())
                    <div class="p-4 border-t border-slate-100 flex justify-between items-center text-xs">
                        <span class="text-slate-500">Menampilkan {{ $invoices->firstItem() }} - {{ $invoices->lastItem() }} dari {{ $invoices->total() }} tagihan</span>
                        <div>{{ $invoices->links() }}</div>
                    </div>
                @endif

            </div>

        </div>
    </main>

    <!-- MODAL TERBITKAN INVOICE BARU UNTUK PESANAN -->
    <div id="issueInvoiceModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                        <i data-lucide="file-plus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-corporate text-base">Terbitkan Invoice Tagihan Baru</h3>
                        <p class="text-xs text-slate-400">Pilih pesanan transaksi perizinan yang akan dibuatkan invoice.</p>
                    </div>
                </div>
                <button type="button" onclick="closeIssueInvoiceModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('finance.invoices.issue') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Pesanan Layanan *</label>
                    <select name="order_id" required class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-600">
                        <option value="">-- Pilih Pesanan (Klien / Layanan) --</option>
                        @foreach($ordersWithoutInvoice as $ord)
                            <option value="{{ $ord->id }}">
                                {{ $ord->order_number }} &bull; {{ $ord->user->company_name ?: $ord->user->name }} &bull; {{ $ord->service?->name ?? 'Layanan' }} (Rp {{ number_format($ord->total_cost, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @if($ordersWithoutInvoice->isEmpty())
                        <p class="text-[11px] text-amber-600 mt-1">Seluruh pesanan aktif saat ini telah memiliki invoice resmi.</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batas Jatuh Tempo (Due Date)</label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}" class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No. Invoice Khusus (Opsional)</label>
                        <input type="text" name="invoice_number" placeholder="Otomatis jika kosong" class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan / Termin Pembayaran</label>
                    <textarea name="notes" rows="2" placeholder="Cth: Pembayaran termin 1 (50%) wajib dilunasi sebelum verifikasi dokumen dimulai." class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-600"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeIssueInvoiceModal()" class="px-4 py-2 text-xs font-semibold text-slate-500">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Terbitkan Invoice</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KELOLA STATUS & JATUH TEMPO INVOICE -->
    <div id="editInvoiceModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div>
                    <h3 class="font-bold text-corporate text-base">Kelola Tagihan Invoice</h3>
                    <p class="text-xs text-slate-400 font-mono" id="modalInvoiceLabel">INV-...</p>
                </div>
                <button type="button" onclick="closeEditInvoiceModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form 1: Ubah Status Pembayaran -->
            <form id="updateStatusForm" method="POST" class="space-y-4 mb-6 pb-6 border-b border-slate-100">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Perbarui Status Pembayaran *</label>
                    <select name="payment_status" id="modalPaymentStatus" required class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-600">
                        <option value="unpaid">Menunggu Pembayaran (Unpaid)</option>
                        <option value="waiting_verification">Menunggu Verifikasi Bukti</option>
                        <option value="paid">Lunas (Paid)</option>
                        <option value="cancelled">Dibatalkan (Cancelled)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Perubahan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Cth: Dikonfirmasi via mutasi rekening giro bank." class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-600">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-corporate hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                        Simpan Status
                    </button>
                </div>
            </form>

            <!-- Form 2: Perpanjang / Atur Ulang Jatuh Tempo -->
            <form id="updateDueDateForm" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Atur Ulang Jatuh Tempo (Due Date)</label>
                    <input type="date" name="due_date" id="modalDueDate" required class="w-full text-xs p-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-amber-600">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                        Perbarui Jatuh Tempo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function openIssueInvoiceModal() {
            document.getElementById('issueInvoiceModal').classList.remove('hidden');
        }

        function closeIssueInvoiceModal() {
            document.getElementById('issueInvoiceModal').classList.add('hidden');
        }

        function openEditInvoiceModal(invoiceId, invoiceNumber, currentStatus, currentDueDate) {
            document.getElementById('modalInvoiceLabel').innerText = 'Nomor: ' + invoiceNumber;
            document.getElementById('modalPaymentStatus').value = currentStatus;
            document.getElementById('modalDueDate').value = currentDueDate || '';
            document.getElementById('updateStatusForm').action = '/finance/invoices/' + invoiceId + '/status';
            document.getElementById('updateDueDateForm').action = '/finance/invoices/' + invoiceId + '/due-date';
            document.getElementById('editInvoiceModal').classList.remove('hidden');
        }

        function closeEditInvoiceModal() {
            document.getElementById('editInvoiceModal').classList.add('hidden');
        }
    </script>
</body>
</html>

