<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Mutasi Keuangan - IZINYOU (CV Prasta Solusi Indonesia)</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 20px;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 2px 0 0 0;
        }
        .meta-box {
            margin-bottom: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            border-radius: 6px;
        }
        .meta-table {
            width: 100%;
        }
        .meta-table td {
            font-size: 11px;
            padding: 2px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 6px;
            border: 1px solid #0f172a;
            text-align: left;
        }
        .data-table td {
            padding: 7px 6px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .total-row {
            background-color: #f1f5f9;
            font-weight: bold;
        }
        .signature-section {
            width: 100%;
            margin-top: 40px;
        }
        .signature-box {
            text-align: center;
            width: 250px;
            float: right;
        }
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #0f172a;
            font-weight: bold;
        }
        .no-print {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn-print {
            background-color: #059669;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Dokumen / Simpan PDF</button>
    </div>

    <!-- Official Header -->
    <table class="header-table">
        <tr>
            <td style="width: 70px; vertical-align: top;">
                <div style="width: 55px; height: 55px; background: #0f172a; border-radius: 8px; text-align: center; line-height: 55px; color: #10b981; font-weight: bold; font-size: 22px;">
                    IY
                </div>
            </td>
            <td style="vertical-align: middle;">
                <h1 class="title">CV PRASTA SOLUSI INDONESIA</h1>
                <p class="subtitle">Platform Manajemen Perizinan &amp; Sertifikasi Industri Terintegrasi (Sistem IZINYOU)</p>
                <p class="subtitle" style="font-size: 9px;">Kantor Operasional: Babelan, Bekasi, Jawa Barat &bull; Hotline: 0812-3456-7890 &bull; Website: www.izinyou.com</p>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <div style="font-size: 14px; font-weight: bold; color: #0f172a;">REKAPITULASI KEUANGAN</div>
                <div style="font-size: 10px; color: #64748b;">Ref: FIN-{{ date('Ymd') }}-{{ rand(100, 999) }}</div>
            </td>
        </tr>
    </table>

    <!-- Meta Information -->
    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td style="width: 18%;"><strong>Jenis Dokumen:</strong></td>
                <td style="width: 32%;">Rekapitulasi Mutasi Keuangan &amp; Transaksi Layanan</td>
                <td style="width: 18%;"><strong>Tanggal Terbit:</strong></td>
                <td style="width: 32%;">{{ date('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td><strong>Periode Data:</strong></td>
                <td>Akumulasi Berjalan (Live System)</td>
                <td><strong>Status Verifikasi:</strong></td>
                <td><span style="color: #059669; font-weight: bold;">Terverifikasi Bagian Keuangan</span></td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No.</th>
                <th style="width: 16%;">No. Pesanan</th>
                <th style="width: 14%;">Tanggal</th>
                <th style="width: 24%;">Klien / Perusahaan</th>
                <th style="width: 16%;">Metode Pembayaran</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 14%;" class="text-right">Nominal (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse($orders as $index => $item)
                @php $total += (float)$item->total_cost; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $item->order_number }}</td>
                    <td>{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>
                    <td>
                        <strong>{{ $item->client_name }}</strong><br>
                        <span style="color: #64748b; font-size: 9px;">{{ $item->company_name ?? '-' }}</span>
                    </td>
                    <td>{{ $item->payment_method ?? 'Transfer Bank' }}</td>
                    <td>
                        <span style="font-weight: bold; color: {{ $item->status === 'completed' ? '#059669' : '#d97706' }};">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td class="text-right font-bold">
                        Rp {{ number_format($item->total_cost, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Belum ada riwayat transaksi.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="6" class="text-right font-bold" style="padding: 10px;">TOTAL MUTASI AKUMULATIF:</td>
                <td class="text-right font-bold" style="font-size: 11px; color: #0f172a;">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <p style="margin: 0;">Bekasi, {{ date('d F Y') }}</p>
            <p style="margin: 3px 0 0 0; font-weight: bold;">Mengetahui &amp; Menyetujui,</p>
            <p style="margin: 0; color: #64748b; font-size: 10px;">Finance &amp; Licensing Officer</p>
            
            <div style="height: 50px;"></div>
            
            <div class="signature-line">
                Dania, S.E. / Iqbal
            </div>
            <p style="margin: 3px 0 0 0; font-size: 9px; color: #64748b;">CV Prasta Solusi Indonesia</p>
        </div>
    </div>

</body>
</html>
