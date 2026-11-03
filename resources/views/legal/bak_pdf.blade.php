<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Kajian Teknis (BAK) - {{ $document->order_number ?? 'DOKUMEN' }}</title>
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
        .bak-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .bak-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 4px;
        }
        .bak-number {
            font-size: 11px;
            color: #475569;
            font-family: monospace;
        }
        .content-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .content-table td {
            padding: 5px 4px;
            vertical-align: top;
        }
        .result-box {
            border: 2px solid {{ $document->status === 'approved' ? '#059669' : '#dc2626' }};
            background-color: {{ $document->status === 'approved' ? '#f0fdf4' : '#fef2f2' }};
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .result-title {
            font-size: 14px;
            font-weight: bold;
            color: {{ $document->status === 'approved' ? '#166534' : '#991b1b' }};
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .notes-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 25px;
        }
        .signature-section {
            width: 100%;
            margin-top: 35px;
        }
        .signature-box {
            text-align: center;
            width: 250px;
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
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Berita Acara (BAK) / Simpan PDF</button>
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
                <p class="subtitle">Platform Manajemen Perizinan &amp; Sertifikasi Industri Terintegrasi (Sistem IZINYOU)</p>
                <p class="subtitle">Unit Kerja: Divisi Legal, Regulatory &amp; Technical Compliance Specialist</p>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <div style="font-size: 10px; color: #64748b;">Standar Kemenkes RI, BPOM &amp; Halal BPJPH</div>
                <div style="font-size: 10px; font-weight: bold; color: #059669;">AKREDITASI QMS</div>
            </td>
        </tr>
    </table>

    <!-- Judul Berita Acara -->
    <div class="bak-header">
        <div class="bak-title">BERITA ACARA KAJIAN DOKUMEN TEKNIS &amp; LEGALITAS (BAK)</div>
        <div class="bak-number">Nomor: BAK/IY-PSI/{{ date('Y/m') }}/{{ str_pad($document->id, 4, '0', STR_PAD_LEFT) }}</div>
    </div>

    <p>
        Pada hari ini, <strong>{{ date('d F Y') }}</strong>, telah dilakukan pemeriksaan, analisis kelengkapan data, dan uji kepatuhan teknis terhadap dokumen pengajuan perizinan dan sertifikasi sebagai berikut:
    </p>

    <!-- Data Pemohon -->
    <table class="content-table">
        <tr>
            <td style="width: 25%;"><strong>Nama Perusahaan Pemohon</strong></td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%;"><strong>{{ $document->company_name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td><strong>Penanggung Jawab (PIC)</strong></td>
            <td>:</td>
            <td>{{ $document->client_name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Nomor Registrasi Projek</strong></td>
            <td>:</td>
            <td><code style="font-weight: bold;">{{ $document->order_number ?? '-' }}</code></td>
        </tr>
        <tr>
            <td><strong>Nama Berkas / Dokumen Diuji</strong></td>
            <td>:</td>
            <td><strong>{{ basename($document->file_path) }}</strong></td>
        </tr>
        <tr>
            <td><strong>Tanggal Pengunggahan</strong></td>
            <td>:</td>
            <td>{{ date('d F Y, H:i', strtotime($document->created_at)) }} WIB</td>
        </tr>
    </table>

    <!-- Hasil Keputusan Uji -->
    <div class="result-box">
        <div class="result-title">
            @if($document->status === 'approved')
                STATUS: VALID / DISENTUJUI (MEMENUHI KETENTUAN STANDAR)
            @elseif($document->status === 'rejected')
                STATUS: PERLU PERBAIKAN DOKUMEN (REVISI KLIEN)
            @else
                STATUS: MENUNGGU TELAAH TEKNIS LANJUTAN
            @endif
        </div>
        <div style="font-size: 11px; color: #334155;">
            @if($document->status === 'approved')
                Dokumen teknis telah memenuhi persyaratan regulasi standar kementerian terkait dan siap dilanjutkan ke proses pendaftaran instansi resmi.
            @else
                Dokumen teknis memerlukan penyempurnaan atau penyesuaian formulasi/legalitas sesuai catatan rekomendasi tim auditor.
            @endif
        </div>
    </div>

    <!-- Catatan & Rekomendasi Legalitas -->
    <div class="notes-box">
        <strong style="color: #0f172a; text-transform: uppercase; font-size: 10px; display: block; margin-bottom: 5px;">
            Catatan Auditor &amp; Rekomendasi Legalitas:
        </strong>
        <p style="margin: 0; font-size: 11px; color: #1e293b;">
            {{ $document->notes ?? 'Dokumen telah melalui uji kepatuhan regulasi oleh Tim Legal & Technical Specialist CV Prasta Solusi Indonesia.' }}
        </p>
    </div>

    <p style="font-size: 10px; color: #64748b;">
        Demikian Berita Acara Kajian ini diterbitkan dengan sebenarnya untuk dipergunakan sebagaimana mestinya dalam proses perizinan dan sertifikasi resmi.
    </p>

    <!-- Signatures -->
    <table class="signature-section">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="signature-box" style="float: left;">
                    <p style="margin: 0;">Perwakilan Pemohon,</p>
                    <p style="margin: 3px 0 0 0; font-weight: bold;">{{ $document->company_name ?? 'Klien Mitra' }}</p>
                    
                    <div style="height: 50px;"></div>
                    
                    <div class="signature-line">
                        {{ $document->client_name ?? 'Penanggung Jawab PIC' }}
                    </div>
                    <p style="margin: 2px 0 0 0; font-size: 9px; color: #64748b;">Klien Mitra IzinYou</p>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="signature-box" style="float: right;">
                    <p style="margin: 0;">Bekasi, {{ date('d F Y') }}</p>
                    <p style="margin: 3px 0 0 0; font-weight: bold;">Auditor Legal &amp; Regulatory Specialist,</p>
                    
                    <div style="height: 50px;"></div>
                    
                    <div class="signature-line">
                        {{ $legalSpecialist->name ?? 'Ahmad, S.H. (Legal Specialist)' }}
                    </div>
                    <p style="margin: 2px 0 0 0; font-size: 9px; color: #64748b;">CV Prasta Solusi Indonesia</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
