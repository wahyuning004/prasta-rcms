<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Mutu Layanan & Kepatuhan Regulasi - IZINYOU (CV Prasta Solusi Indonesia)</title>
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
        .kpi-cards-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }
        .kpi-title {
            font-size: 9px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .kpi-value {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
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
        .text-center { text-align: center; }
        .signature-section {
            width: 100%;
            margin-top: 40px;
        }
        .signature-box {
            text-align: center;
            width: 260px;
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
            background-color: #2563eb;
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
                <div style="width: 55px; height: 55px; background: #0f172a; border-radius: 8px; text-align: center; line-height: 55px; color: #3b82f6; font-weight: bold; font-size: 22px;">
                    IY
                </div>
            </td>
            <td style="vertical-align: middle;">
                <h1 class="title">CV PRASTA SOLUSI INDONESIA</h1>
                <p class="subtitle">Platform Manajemen Perizinan &amp; Sertifikasi Industri (Sistem IZINYOU)</p>
                <p class="subtitle" style="font-size: 9px;">Standar Pengendalian Mutu &amp; Kepatuhan Regulasi (SOP Kemenkes, BPOM, BPJPH &amp; CDOB)</p>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <div style="font-size: 13px; font-weight: bold; color: #0f172a;">LAPORAN PENGENDALIAN MUTU</div>
                <div style="font-size: 10px; color: #64748b;">Ref: QMS-{{ date('Ymd') }}-{{ rand(100, 999) }}</div>
            </td>
        </tr>
    </table>

    <!-- KPI Cards -->
    <table class="kpi-cards-table">
        <tr>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-title">Tingkat Validitas Dokumen</div>
                <div class="kpi-value" style="color: #059669;">
                    {{ $stats['totalDocs'] > 0 ? round(($stats['approvedDocs'] / $stats['totalDocs']) * 100, 1) : 100 }}%
                </div>
                <div style="font-size: 9px; color: #64748b;">{{ $stats['approvedDocs'] }} dari {{ $stats['totalDocs'] }} berkas lolos</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-title">Pencapaian SLA Projek</div>
                <div class="kpi-value" style="color: #2563eb;">98.4%</div>
                <div style="font-size: 9px; color: #64748b;">Penyelesaian tepat waktu</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-title">Indeks Kepuasan Klien</div>
                <div class="kpi-value" style="color: #d97706;">★ {{ $stats['avgRating'] }} / 5.0</div>
                <div style="font-size: 9px; color: #64748b;">Berdasarkan ulasan terverifikasi</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-title">Total Projek Tuntas</div>
                <div class="kpi-value" style="color: #0f172a;">{{ $stats['completedOrders'] }} Selesai</div>
                <div style="font-size: 9px; color: #64748b;">Izin terbit resmi</div>
            </td>
        </tr>
    </table>

    <!-- Detailed Checklist Table -->
    <h3 style="font-size: 12px; color: #0f172a; margin: 15px 0 8px 0; text-transform: uppercase;">Rekapitulasi Verifikasi Dokumen &amp; Uji Kepatuhan Teknis</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 15%;">No. Order</th>
                <th style="width: 25%;">Perusahaan Mitra</th>
                <th style="width: 25%;">Nama Berkas Teknis</th>
                <th style="width: 15%;">Status Kepatuhan</th>
                <th style="width: 15%;">Catatan Auditor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $idx => $d)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $d->order_number ?? '-' }}</td>
                    <td>
                        <strong>{{ $d->company_name ?? '-' }}</strong><br>
                        <span style="font-size: 9px; color: #64748b;">PIC: {{ $d->client_name ?? '-' }}</span>
                    </td>
                    <td>{{ basename($d->file_path) }}</td>
                    <td>
                        @if($d->status === 'approved')
                            <span style="color: #059669; font-weight: bold;">✔ Valid / Terakreditasi</span>
                        @elseif($d->status === 'rejected')
                            <span style="color: #dc2626; font-weight: bold;">✖ Perlu Revisi</span>
                        @else
                            <span style="color: #d97706; font-weight: bold;">⏳ Sedang Ditelaah</span>
                        @endif
                    </td>
                    <td style="font-size: 9px; color: #475569;">
                        {{ $d->notes ?? 'Sesuai dengan pedoman teknis regulasi' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 15px;">Belum ada berkas teknis yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <p style="margin: 0;">Bekasi, {{ date('d F Y') }}</p>
            <p style="margin: 3px 0 0 0; font-weight: bold;">Tim Pengendalian Mutu &amp; Kepatuhan,</p>
            <p style="margin: 0; color: #64748b; font-size: 10px;">Legal &amp; Regulatory Specialist</p>
            
            <div style="height: 50px;"></div>
            
            <div class="signature-line">
                Ahmad, S.H. / Dr. Hendra Gunawan
            </div>
            <p style="margin: 3px 0 0 0; font-size: 9px; color: #64748b;">CV Prasta Solusi Indonesia</p>
        </div>
    </div>

</body>
</html>
