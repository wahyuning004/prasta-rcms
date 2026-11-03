<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Resmi - {{ $order->order_number ?? 'INVOICE' }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 25px;
            line-height: 1.5;
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
            font-size: 10px;
            color: #64748b;
            margin: 2px 0 0 0;
        }
        .invoice-box {
            width: 100%;
            margin-bottom: 25px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .invoice-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 6px;
            border: 1px solid #0f172a;
            text-align: left;
        }
        .invoice-table td {
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .total-row {
            background-color: #f1f5f9;
            font-weight: bold;
        }
        .stamp-box {
            border: 2px dashed #059669;
            background-color: #ecfdf5;
            color: #065f46;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
        }
        .signature-section {
            width: 100%;
            margin-top: 35px;
        }
        .signature-box {
            text-align: center;
            width: 250px;
            float: right;
        }
        .signature-line {
            margin-top: 55px;
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
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Invoice Resmi / Simpan PDF</button>
    </div>

    <!-- Kop Surat -->
    <table class="header-table">
        <tr>
            <td style="width: 70px; vertical-align: top;">
                <div style="width: 55px; height: 55px; background: #0f172a; border-radius: 8px; text-align: center; line-height: 55px; color: #10b981; font-weight: bold; font-size: 22px;">
                    IY
                </div>
            </td>
            <td style="vertical-align: middle;">
                <h1 class="title">CV PRASTA SOLUSI INDONESIA</h1>
                <p class="subtitle">Platform Manajemen Perizinan &amp; Sertifikasi Bisnis Terintegrasi (Sistem IZINYOU)</p>
                <p class="subtitle">Divisi Keuangan &amp; Penagihan Resmi &bull; NPWP: 94.812.334.2-407.000</p>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <div style="font-size: 16px; font-weight: bold; color: #0f172a;">INVOICE PEMBAYARAN</div>
                <div style="font-size: 11px; color: #475569; font-family: monospace; font-weight: bold;">INV/{{ date('Ymd') }}/{{ $order->order_number }}</div>
            </td>
        </tr>
    </table>

    <!-- Billing Info -->
    <table class="invoice-box">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <p style="font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; margin: 0 0 4px 0;">Ditagihkan Kepada:</p>
                <h3 style="font-size: 13px; font-weight: bold; color: #0f172a; margin: 0 0 2px 0;">{{ $order->company_name ?? $order->client_name }}</h3>
                <p style="margin: 0; color: #334155;">u.p. {{ $order->client_name }}</p>
                <p style="margin: 0; color: #64748b;">{{ $order->client_address ?? 'Kawasan Industri Mitra' }}</p>
                <p style="margin: 0; color: #64748b;">Kontak: {{ $order->client_phone ?? '-' }} &bull; {{ $order->client_email ?? '-' }}</p>
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <p style="margin: 2px 0;"><strong>Tanggal Faktur:</strong> {{ date('d F Y') }}</p>
                <p style="margin: 2px 0;"><strong>Nomor Projek:</strong> <code style="font-weight: bold;">{{ $order->order_number }}</code></p>
                <p style="margin: 2px 0;"><strong>Metode Transaksi:</strong> {{ $order->payment_method ?? 'Transfer Bank Mandiri' }}</p>
                <p style="margin: 2px 0;"><strong>Status Pembayaran:</strong> <span style="color: #059669; font-weight: bold;">LUNAS (VERIFIED)</span></p>
            </td>
        </tr>
    </table>

    <!-- Table Items -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th style="width: 8%;">No.</th>
                <th style="width: 52%;">Deskripsi Layanan &amp; Ruang Lingkup Sertifikasi</th>
                <th style="width: 15%;" class="text-center">Kuantitas</th>
                <th style="width: 25%;" class="text-right">Biaya (IDR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>
                    <strong>Jasa Pendampingan &amp; Pengurusan Perizinan Industri</strong><br>
                    <span style="color: #64748b; font-size: 9px;">Kajian berkas teknis, evaluasi standar SOP regulasi instansi, dan penyerahan sertifikat resmi.</span>
                </td>
                <td class="text-center">1 Paket</td>
                <td class="text-right font-bold">Rp {{ number_format($order->total_cost, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="3" class="text-right font-bold" style="padding: 10px;">TOTAL TAGIHAN LUNAS:</td>
                <td class="text-right font-bold" style="font-size: 12px; color: #0f172a;">
                    Rp {{ number_format($order->total_cost, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Stamp Lunas -->
    <div class="stamp-box">
        ✔ TELAH DIVERIFIKASI LUNAS OLEH DIVISI KEUANGAN CV PRASTA SOLUSI INDONESIA
    </div>

    <!-- Signatures -->
    <table class="signature-section">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div style="font-size: 10px; color: #64748b; line-height: 1.4;">
                    <strong>Rekening Resmi Perusahaan:</strong><br>
                    Bank Mandiri KCP Bekasi<br>
                    No. Rekening: 156-00-1928374-1<br>
                    Atas Nama: CV PRASTA SOLUSI INDONESIA
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="signature-box">
                    <p style="margin: 0;">Bekasi, {{ date('d F Y') }}</p>
                    <p style="margin: 3px 0 0 0; font-weight: bold;">Finance &amp; Licensing Officer,</p>
                    
                    <div style="height: 50px;"></div>
                    
                    <div class="signature-line">
                        {{ $financeOfficer->name ?? 'Dania, S.E. (Finance)' }}
                    </div>
                    <p style="margin: 2px 0 0 0; font-size: 9px; color: #64748b;">CV Prasta Solusi Indonesia</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
