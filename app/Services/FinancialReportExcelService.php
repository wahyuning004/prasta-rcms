<?php

namespace App\Services;

class FinancialReportExcelService
{
    /**
     * Generate binary XLSX (Excel OpenXML) untuk Laporan Transaksi Keuangan.
     *
     * @return string Binary file content .xlsx
     */
    public function exportTransactions(iterable $payments, ?string $title = null): string
    {
        $rows = [];
        $r = 1;

        // Header Dokumen
        $rows[] = $this->rowText($r++, [
            ['A', 'DATA TRANSAKSI KEUANGAN & PEMBAYARAN DIGITAL', true],
        ]);
        $rows[] = $this->rowText($r++, [
            ['A', 'CV PRASTA SOLUSI INDONESIA - DIVISI FINANCE & ADMINISTRATION', true],
        ]);
        $rows[] = $this->rowText($r++, [
            ['A', 'Tanggal Ekspor: '.date('d F Y H:i:s'), false],
        ]);
        $r++; // Spasi kosong

        // Header Tabel Kolom
        $headerCols = [
            ['A', 'No.', true],
            ['B', 'No. Order', true],
            ['C', 'Perusahaan Klien', true],
            ['D', 'Nama PIC / Kontak', true],
            ['E', 'Metode Pembayaran', true],
            ['F', 'Nominal (IDR)', true],
            ['G', 'Status Pembayaran', true],
            ['H', 'Tanggal Transaksi', true],
        ];
        $rows[] = $this->rowText($r++, $headerCols, true);

        $no = 1;
        $totalAmount = 0;

        foreach ($payments as $p) {
            $amount = (float) ($p->amount ?? 0);
            $totalAmount += $amount;

            $statusText = match ($p->status ?? '') {
                'success', 'paid' => 'Lunas / Terverifikasi',
                'failed', 'rejected' => 'Ditolak',
                default => 'Menunggu Verifikasi',
            };

            $dateFormatted = ! empty($p->created_at) ? date('d/m/Y H:i', strtotime($p->created_at)) : '-';

            $rows[] = '<row r="'.$r.'">'.
                $this->cellNumber('A'.$r, $no++).
                $this->cellText('B'.$r, $p->order_number ?? ($p->order?->order_number ?? '-')).
                $this->cellText('C'.$r, $p->company_name ?? ($p->order?->user?->company_name ?: ($p->order?->user?->name ?? '-'))).
                $this->cellText('D'.$r, $p->client_name ?? ($p->order?->user?->name ?? '-')).
                $this->cellText('E'.$r, $p->payment_method ?? 'Transfer Bank').
                $this->cellNumber('F'.$r, $amount).
                $this->cellText('G'.$r, $statusText).
                $this->cellText('H'.$r, $dateFormatted).
                '</row>';
            $r++;
        }

        // Baris Total Akumulasi
        $r++;
        $rows[] = '<row r="'.$r.'">'.
            $this->cellText('A'.$r, 'TOTAL AKUMULASI MUTASI KAS MASUK', true).
            $this->cellText('B'.$r, '').
            $this->cellText('C'.$r, '').
            $this->cellText('D'.$r, '').
            $this->cellText('E'.$r, '').
            $this->cellNumber('F'.$r, $totalAmount).
            $this->cellText('G'.$r, '').
            $this->cellText('H'.$r, '').
            '</row>';

        return $this->assembleXlsx($rows, 'Mutasi Keuangan');
    }

    /**
     * Generate binary XLSX untuk Laporan Rekapitulasi Pendapatan & Arus Kas (Cash Flow Statement).
     *
     * @return string Binary file content .xlsx
     */
    public function exportCashFlowReport(array $reportData): string
    {
        $rows = [];
        $r = 1;

        // Header Dokumen
        $rows[] = $this->rowText($r++, [
            ['A', 'LAPORAN REKAPITULASI PENDAPATAN & ARUS KAS PERUSAHAAN', true],
        ]);
        $rows[] = $this->rowText($r++, [
            ['A', 'CV PRASTA SOLUSI INDONESIA - DIVISI FINANCE & ADMINISTRATION', true],
        ]);
        $rows[] = $this->rowText($r++, [
            ['A', 'Periode Laporan: '.($reportData['periodLabel'] ?? 'Semua Waktu'), false],
        ]);
        $rows[] = $this->rowText($r++, [
            ['A', 'Tanggal Diterbitkan: '.date('d F Y H:i:s').' | Petugas: '.($reportData['officerName'] ?? 'Finance Officer'), false],
        ]);
        $r++; // Spasi

        // Bagian 1: Ringkasan Eksekutif Finansial
        $rows[] = $this->rowText($r++, [
            ['A', '1. RINGKASAN EKSEKUTIF FINANSIAL & ARUS KAS', true],
        ]);
        $rows[] = $this->rowText($r++, [
            ['A', 'Indikator Finansial', true],
            ['B', 'Nilai (IDR)', true],
            ['C', 'Keterangan Analisis', true],
        ], true);

        $metrics = [
            ['Total Pendapatan Kotor (Gross Billed)', (float) ($reportData['grossRevenue'] ?? 0), 'Total tagihan awal layanan sebelum potongan diskon'],
            ['Total Potongan Kupon Promo (Discounts)', (float) ($reportData['totalDiscounts'] ?? 0), 'Akumulasi diskon voucher yang dimanfaatkan pelanggan'],
            ['Pendapatan Bersih Buku (Net Accrual Revenue)', (float) ($reportData['netRevenue'] ?? 0), 'Pendapatan riil transaksi setelah diskon'],
            ['Arus Kas Masuk Riil (Actual Cash Inflow)', (float) ($reportData['cashInflow'] ?? 0), 'Kas riil yang telah terverifikasi masuk ke rekening bank'],
            ['Piutang Usaha Berjalan (Outstanding Receivables)', (float) ($reportData['receivables'] ?? 0), 'Tagihan berjalan yang belum dilunasi klien'],
        ];

        foreach ($metrics as $m) {
            $rows[] = '<row r="'.$r.'">'.
                $this->cellText('A'.$r, $m[0]).
                $this->cellNumber('B'.$r, $m[1]).
                $this->cellText('C'.$r, $m[2]).
                '</row>';
            $r++;
        }

        // Tingkat Realisasi Kas (%)
        $rows[] = '<row r="'.$r.'">'.
            $this->cellText('A'.$r, 'Tingkat Realisasi Kas Masuk (Collection Rate)').
            $this->cellText('B'.$r, ($reportData['collectionRate'] ?? 0).'%').
            $this->cellText('C'.$r, 'Rasio perputaran kas masuk terhadap tagihan bersih').
            '</row>';
        $r += 2;

        // Bagian 2: Buku Kas Harian (Daily Cash Ledger)
        $rows[] = $this->rowText($r++, [
            ['A', '2. BUKU REKAPITULASI ARUS KAS HARIAN (DAILY CASH INFLOW LEDGER)', true],
        ]);
        $rows[] = $this->rowText($r++, [
            ['A', 'Tanggal Mutasi', true],
            ['B', 'Jumlah Mutasi Masuk', true],
            ['C', 'Kas Masuk Riil (IDR)', true],
            ['D', 'Saldo Kas Akumulatif (IDR)', true],
        ], true);

        if (! empty($reportData['dailyLedger'])) {
            foreach ($reportData['dailyLedger'] as $dl) {
                $rows[] = '<row r="'.$r.'">'.
                    $this->cellText('A'.$r, date('d/m/Y', strtotime($dl->tx_date))).
                    $this->cellNumber('B'.$r, (int) $dl->tx_count).
                    $this->cellNumber('C'.$r, (float) $dl->daily_inflow).
                    $this->cellNumber('D'.$r, (float) $dl->running_balance).
                    '</row>';
                $r++;
            }
        }
        $r += 2;

        // Bagian 3: Komposisi Kanal Bank Penerima Kas
        $rows[] = $this->rowText($r++, [
            ['A', '3. KOMPOSISI KANAL PERBANKAN PENERIMA KAS', true],
        ]);
        $rows[] = $this->rowText($r++, [
            ['A', 'Kanal Pembayaran / Bank', true],
            ['B', 'Jumlah Transaksi', true],
            ['C', 'Total Kas Masuk (IDR)', true],
            ['D', 'Porsi Kontribusi (%)', true],
        ], true);

        $totalInflow = (float) ($reportData['cashInflow'] ?? 1);
        if (! empty($reportData['channelBreakdown'])) {
            foreach ($reportData['channelBreakdown'] as $ch) {
                $chAmount = (float) $ch->total_amount;
                $pct = $totalInflow > 0 ? round(($chAmount / $totalInflow) * 100, 1) : 0;
                $rows[] = '<row r="'.$r.'">'.
                    $this->cellText('A'.$r, $ch->payment_method ?? 'Transfer Bank').
                    $this->cellNumber('B'.$r, (int) $ch->count).
                    $this->cellNumber('C'.$r, $chAmount).
                    $this->cellText('D'.$r, $pct.'%').
                    '</row>';
                $r++;
            }
        }
        $r += 2;

        // Bagian 4: Rincian Transaksi Layanan Perizinan
        $rows[] = $this->rowText($r++, [
            ['A', '4. RINCIAN PESANAN & STATUS TAGIHAN INVOICE', true],
        ]);
        $rows[] = $this->rowText($r++, [
            ['A', 'No.', true],
            ['B', 'No. Order', true],
            ['C', 'No. Invoice', true],
            ['D', 'Perusahaan Klien', true],
            ['E', 'Layanan Perizinan', true],
            ['F', 'Subtotal (IDR)', true],
            ['G', 'Diskon (IDR)', true],
            ['H', 'Total Tagihan (IDR)', true],
            ['I', 'Status Tagihan', true],
            ['J', 'Tanggal Order', true],
        ], true);

        $idx = 1;
        if (! empty($reportData['orders'])) {
            foreach ($reportData['orders'] as $ord) {
                $subtotal = (float) ($ord->base_price > 0 ? $ord->base_price : ($ord->total_cost + $ord->discount_amount));
                $meta = $ord->paymentStatusMeta();
                $rows[] = '<row r="'.$r.'">'.
                    $this->cellNumber('A'.$r, $idx++).
                    $this->cellText('B'.$r, $ord->order_number).
                    $this->cellText('C'.$r, $ord->invoice?->invoice_number ?? 'Belum Terbit').
                    $this->cellText('D'.$r, $ord->user?->company_name ?: ($ord->user?->name ?? '-')).
                    $this->cellText('E'.$r, $ord->service?->name ?? 'Layanan Perizinan').
                    $this->cellNumber('F'.$r, $subtotal).
                    $this->cellNumber('G'.$r, (float) $ord->discount_amount).
                    $this->cellNumber('H'.$r, (float) $ord->total_cost).
                    $this->cellText('I'.$r, $meta['label']).
                    $this->cellText('J'.$r, date('d/m/Y', strtotime($ord->created_at))).
                    '</row>';
                $r++;
            }
        }

        return $this->assembleXlsx($rows, 'Rekapitulasi Arus Kas');
    }

    /**
     * Rakit struktur OpenXML spreadsheet menjadi arsip PKZip (.xlsx).
     */
    protected function assembleXlsx(array $sheetRows, string $sheetName = 'Laporan'): string
    {
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'.
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'.
            '<Default Extension="xml" ContentType="application/xml"/>'.
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'.
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'.
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'.
            '</Types>';

        $dotRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'.
            '</Relationships>';

        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'.
            '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'.
            '</Relationships>';

        $safeSheetName = htmlspecialchars(substr($sheetName, 0, 31), ENT_XML1, 'UTF-8');
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'.
            '<sheets><sheet name="'.$safeSheetName.'" sheetId="1" r:id="rId1"/></sheets>'.
            '</workbook>';

        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'.
            '<fonts count="2">'.
            '<font><name val="Calibri"/><sz val="10"/></font>'.
            '<font><b/><name val="Calibri"/><sz val="10"/></font>'.
            '</fonts>'.
            '<fills count="2">'.
            '<fill><patternFill patternType="none"/></fill>'.
            '<fill><patternFill patternType="gray125"/></fill>'.
            '</fills>'.
            '<borders count="1"><border><left/><right/><top/><bottom/></border></borders>'.
            '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'.
            '<cellXfs count="3">'.
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'. // 0: Normal
            '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/>'. // 1: Bold
            '<xf numFmtId="3" fontId="0" fillId="0" borderId="0" xfId="0"/>'. // 2: Number format (#,##0)
            '</cellXfs>'.
            '</styleSheet>';

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'.
            '<sheetData>'.implode('', $sheetRows).'</sheetData>'.
            '</worksheet>';

        $files = [
            '[Content_Types].xml' => $contentTypes,
            '_rels/.rels' => $dotRels,
            'xl/_rels/workbook.xml.rels' => $workbookRels,
            'xl/workbook.xml' => $workbook,
            'xl/styles.xml' => $styles,
            'xl/worksheets/sheet1.xml' => $sheetXml,
        ];

        return $this->packZip($files);
    }

    protected function cellText(string $cellRef, string $value, bool $isBold = false): string
    {
        $escaped = htmlspecialchars($value, ENT_XML1, 'UTF-8');
        $styleAttr = $isBold ? ' s="1"' : '';

        return '<c r="'.$cellRef.'" t="inlineStr"'.$styleAttr.'><is><t>'.$escaped.'</t></is></c>';
    }

    protected function cellNumber(string $cellRef, float|int $value, bool $isBold = false): string
    {
        $styleAttr = $isBold ? ' s="1"' : ' s="2"';

        return '<c r="'.$cellRef.'"'.$styleAttr.'><v>'.$value.'</v></c>';
    }

    protected function rowText(int $rowNum, array $columns, bool $isHeader = false): string
    {
        $xml = '<row r="'.$rowNum.'">';
        foreach ($columns as $col) {
            $colLetter = $col[0];
            $text = $col[1];
            $bold = $col[2] ?? $isHeader;
            $xml .= $this->cellText($colLetter.$rowNum, $text, $bold);
        }
        $xml .= '</row>';

        return $xml;
    }

    /**
     * Buat format arsip ZIP biner standar tanpa dependensi eksternal.
     */
    protected function packZip(array $files): string
    {
        $cd = '';
        $body = '';
        $offset = 0;

        foreach ($files as $name => $content) {
            $compressed = gzdeflate($content, 6);
            $crc = crc32($content);
            $cLen = strlen($compressed);
            $uLen = strlen($content);
            $nameLen = strlen($name);

            // Local file header (0x04034b50)
            $local = pack('VvvvvvVVVvv', 0x04034B50, 20, 0, 8, 0, 0, $crc, $cLen, $uLen, $nameLen, 0).$name.$compressed;
            $body .= $local;

            // Central directory entry (0x02014b50)
            $cd .= pack('VvvvvvvVVVvvvvvVV', 0x02014B50, 20, 20, 0, 8, 0, 0, $crc, $cLen, $uLen, $nameLen, 0, 0, 0, 0, 32, $offset).$name;
            $offset += strlen($local);
        }

        $cdLen = strlen($cd);
        // End of central directory record (0x06054b50)
        $end = pack('VvvvvVVv', 0x06054B50, 0, 0, count($files), count($files), $cdLen, $offset, 0);

        return $body.$cd.$end;
    }
}
