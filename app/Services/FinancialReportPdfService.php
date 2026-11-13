<?php

namespace App\Services;

class FinancialReportPdfService
{
    /**
     * Generate binary PDF 1.4 resmi untuk Laporan Transaksi Keuangan.
     *
     * @return string Binary PDF content
     */
    public function exportTransactions(iterable $payments, ?string $title = null): string
    {
        $pageWidth = 842; // Landscape A4
        $pageHeight = 595;
        $commands = [];
        $y = 550;

        // Kop Dokumen
        $commands[] = $this->textCmd(40, $y, 'CV PRASTA SOLUSI INDONESIA', 14, true);
        $y -= 15;
        $commands[] = $this->textCmd(40, $y, 'DIVISI FINANCE & ADMINISTRATION - LAPORAN TRANSAKSI KEUANGAN', 10, true);
        $y -= 12;
        $commands[] = $this->textCmd(40, $y, 'Tanggal Ekspor: '.date('d F Y H:i:s').' | Status Transaksi: Semua Status Terpilih', 8, false);
        $y -= 8;
        $commands[] = $this->lineCmd(40, $y, 802, $y);

        // Header Tabel Kolom
        $y -= 18;
        $commands[] = $this->rectCmd(40, $y - 4, 762, 16, 0.95, 0.95, 0.95);
        $commands[] = $this->textCmd(45, $y, 'NO', 8, true);
        $commands[] = $this->textCmd(75, $y, 'NO. PESANAN', 8, true);
        $commands[] = $this->textCmd(170, $y, 'PERUSAHAAN KLIEN', 8, true);
        $commands[] = $this->textCmd(320, $y, 'NAMA PIC', 8, true);
        $commands[] = $this->textCmd(430, $y, 'METODE BAYAR', 8, true);
        $commands[] = $this->textCmd(530, $y, 'NOMINAL (IDR)', 8, true);
        $commands[] = $this->textCmd(640, $y, 'STATUS', 8, true);
        $commands[] = $this->textCmd(720, $y, 'TANGGAL', 8, true);
        $y -= 6;
        $commands[] = $this->lineCmd(40, $y, 802, $y);

        $no = 1;
        $totalAmount = 0;

        foreach ($payments as $p) {
            if ($y < 60) {
                // Break or wrap row if reaching bottom margin
                break;
            }
            $y -= 14;
            $amount = (float) ($p->amount ?? 0);
            $totalAmount += $amount;

            $statusText = match ($p->status ?? '') {
                'success', 'paid' => 'Lunas',
                'failed', 'rejected' => 'Ditolak',
                default => 'Menunggu',
            };

            $orderNo = $p->order_number ?? ($p->order?->order_number ?? '-');
            $compName = substr($p->company_name ?? ($p->order?->user?->company_name ?: ($p->order?->user?->name ?? '-')), 0, 24);
            $picName = substr($p->client_name ?? ($p->order?->user?->name ?? '-'), 0, 18);
            $method = substr($p->payment_method ?? 'Transfer Bank', 0, 16);
            $date = ! empty($p->created_at) ? date('d/m/Y H:i', strtotime($p->created_at)) : '-';

            $commands[] = $this->textCmd(45, $y, (string) ($no++), 8, false);
            $commands[] = $this->textCmd(75, $y, $orderNo, 8, true);
            $commands[] = $this->textCmd(170, $y, $compName, 8, false);
            $commands[] = $this->textCmd(320, $y, $picName, 8, false);
            $commands[] = $this->textCmd(430, $y, $method, 8, false);
            $commands[] = $this->textCmd(530, $y, number_format($amount, 0, ',', '.'), 8, true);
            $commands[] = $this->textCmd(640, $y, $statusText, 8, false);
            $commands[] = $this->textCmd(720, $y, $date, 7, false);
            $commands[] = $this->lineCmd(40, $y - 3, 802, $y - 3, 0.9);
        }

        // Total Row
        $y -= 18;
        $commands[] = $this->lineCmd(40, $y + 12, 802, $y + 12);
        $commands[] = $this->textCmd(45, $y, 'TOTAL AKUMULASI MUTASI KAS:', 9, true);
        $commands[] = $this->textCmd(530, $y, 'Rp '.number_format($totalAmount, 0, ',', '.'), 10, true);

        return $this->buildPdfBinary($commands, $pageWidth, $pageHeight);
    }

    /**
     * Generate binary PDF 1.4 resmi untuk Laporan Rekapitulasi Pendapatan & Arus Kas.
     *
     * @return string Binary PDF content
     */
    public function exportCashFlowReport(array $reportData): string
    {
        $pageWidth = 595; // Portrait A4
        $pageHeight = 842;
        $commands = [];
        $y = 800;

        // Kop Surat Perusahaan
        $commands[] = $this->textCmd(40, $y, 'CV PRASTA SOLUSI INDONESIA', 15, true);
        $y -= 14;
        $commands[] = $this->textCmd(40, $y, 'DIVISI FINANCE & ADMINISTRATION - LAPORAN KEUANGAN RESMI', 9, true);
        $y -= 11;
        $commands[] = $this->textCmd(40, $y, 'Cluster New Liverpool Blok P18A No 46, Babelan, Kab. Bekasi 17610 | Email: prastasolusi.indonesia@gmail.com', 8, false);
        $y -= 8;
        $commands[] = $this->lineCmd(40, $y, 555, $y);

        // Judul Laporan & Periode
        $y -= 22;
        $commands[] = $this->textCmd(40, $y, 'LAPORAN REKAPITULASI PENDAPATAN & ARUS KAS', 12, true);
        $y -= 14;
        $commands[] = $this->textCmd(40, $y, 'Periode: '.($reportData['periodLabel'] ?? 'Semua Periode').' | Tanggal Cetak: '.date('d F Y H:i:s'), 9, false);
        $y -= 12;
        $commands[] = $this->textCmd(40, $y, 'Petugas Pengesah: '.($reportData['officerName'] ?? 'Finance Officer'), 9, false);
        $y -= 10;
        $commands[] = $this->lineCmd(40, $y, 555, $y);

        // Bagian 1: Ringkasan Eksekutif Finansial
        $y -= 18;
        $commands[] = $this->textCmd(40, $y, '1. RINGKASAN EKSEKUTIF FINANSIAL & ARUS KAS', 10, true);
        $y -= 14;

        $metrics = [
            ['Arus Kas Masuk Riil (Actual Cash Inflow)', (float) ($reportData['cashInflow'] ?? 0), 'Dana masuk terverifikasi rekening giro bank'],
            ['Pendapatan Bersih Buku (Net Billed Revenue)', (float) ($reportData['netRevenue'] ?? 0), 'Total tagihan bersih setelah voucher diskon'],
            ['Pendapatan Kotor Layanan (Gross Billed)', (float) ($reportData['grossRevenue'] ?? 0), 'Akumulasi nilai awal tagihan pesanan'],
            ['Total Potongan Kupon Promo (Discounts)', (float) ($reportData['totalDiscounts'] ?? 0), 'Subsidi diskon promosi pelanggan'],
            ['Piutang Usaha Berjalan (Outstanding)', (float) ($reportData['receivables'] ?? 0), 'Tagihan berjalan yang belum dilunasi klien'],
        ];

        $commands[] = $this->rectCmd(40, $y - 3, 515, 14, 0.95, 0.95, 0.95);
        $commands[] = $this->textCmd(45, $y, 'INDIKATOR KEUANGAN', 8, true);
        $commands[] = $this->textCmd(300, $y, 'NILAI (IDR)', 8, true);
        $commands[] = $this->textCmd(410, $y, 'KETERANGAN', 8, true);
        $y -= 6;
        $commands[] = $this->lineCmd(40, $y, 555, $y);

        foreach ($metrics as $m) {
            $y -= 14;
            $commands[] = $this->textCmd(45, $y, $m[0], 8, false);
            $commands[] = $this->textCmd(300, $y, 'Rp '.number_format($m[1], 0, ',', '.'), 8, true);
            $commands[] = $this->textCmd(410, $y, substr($m[2], 0, 30), 7, false);
            $commands[] = $this->lineCmd(40, $y - 3, 555, $y - 3, 0.9);
        }

        $y -= 14;
        $commands[] = $this->textCmd(45, $y, 'Tingkat Realisasi Kas (Collection Rate)', 8, true);
        $commands[] = $this->textCmd(300, $y, ($reportData['collectionRate'] ?? 0).'%', 9, true);
        $commands[] = $this->textCmd(410, $y, 'Rasio Kas Masuk / Tagihan Bersih', 7, false);
        $commands[] = $this->lineCmd(40, $y - 3, 555, $y - 3);

        // Bagian 2: Rekapitulasi Arus Kas Harian
        $y -= 22;
        $commands[] = $this->textCmd(40, $y, '2. BUKU MUTASI ARUS KAS HARIAN (DAILY CASH INFLOW)', 10, true);
        $y -= 14;
        $commands[] = $this->rectCmd(40, $y - 3, 515, 14, 0.95, 0.95, 0.95);
        $commands[] = $this->textCmd(45, $y, 'TANGGAL MUTASI', 8, true);
        $commands[] = $this->textCmd(180, $y, 'JUMLAH TRANSAKSI', 8, true);
        $commands[] = $this->textCmd(300, $y, 'KAS MASUK (IDR)', 8, true);
        $commands[] = $this->textCmd(420, $y, 'SALDO AKUMULATIF (IDR)', 8, true);
        $y -= 6;
        $commands[] = $this->lineCmd(40, $y, 555, $y);

        if (! empty($reportData['dailyLedger'])) {
            $ledgerLimit = array_slice($reportData['dailyLedger']->toArray(), 0, 8);
            foreach ($ledgerLimit as $dl) {
                $y -= 13;
                $commands[] = $this->textCmd(45, $y, date('d/m/Y', strtotime($dl['tx_date'])), 8, false);
                $commands[] = $this->textCmd(180, $y, $dl['tx_count'].' Transaksi', 8, false);
                $commands[] = $this->textCmd(300, $y, 'Rp '.number_format((float) $dl['daily_inflow'], 0, ',', '.'), 8, true);
                $commands[] = $this->textCmd(420, $y, 'Rp '.number_format((float) $dl['running_balance'], 0, ',', '.'), 8, false);
                $commands[] = $this->lineCmd(40, $y - 3, 555, $y - 3, 0.9);
            }
        } else {
            $y -= 14;
            $commands[] = $this->textCmd(45, $y, 'Belum ada mutasi arus kas masuk pada rentang tanggal ini.', 8, false);
        }

        // Bagian 3: Komposisi Kanal Bank Penerima
        $y -= 22;
        $commands[] = $this->textCmd(40, $y, '3. DISTRIBUSI KANAL REKENING BANK PENERIMA', 10, true);
        $y -= 14;
        $commands[] = $this->rectCmd(40, $y - 3, 515, 14, 0.95, 0.95, 0.95);
        $commands[] = $this->textCmd(45, $y, 'METODE / KANAL BANK', 8, true);
        $commands[] = $this->textCmd(200, $y, 'TRANSAKSI', 8, true);
        $commands[] = $this->textCmd(300, $y, 'TOTAL KAS (IDR)', 8, true);
        $commands[] = $this->textCmd(440, $y, 'PORSI (%)', 8, true);
        $y -= 6;
        $commands[] = $this->lineCmd(40, $y, 555, $y);

        $totalInflow = (float) ($reportData['cashInflow'] ?? 1);
        if (! empty($reportData['channelBreakdown'])) {
            foreach ($reportData['channelBreakdown'] as $ch) {
                $y -= 13;
                $pct = $totalInflow > 0 ? round(($ch->total_amount / $totalInflow) * 100, 1) : 0;
                $commands[] = $this->textCmd(45, $y, $ch->payment_method ?? 'Transfer Bank', 8, false);
                $commands[] = $this->textCmd(200, $y, (string) $ch->count, 8, false);
                $commands[] = $this->textCmd(300, $y, 'Rp '.number_format((float) $ch->total_amount, 0, ',', '.'), 8, true);
                $commands[] = $this->textCmd(440, $y, $pct.'%', 8, false);
                $commands[] = $this->lineCmd(40, $y - 3, 555, $y - 3, 0.9);
            }
        }

        // Kolom Tanda Tangan Pengesahan Finansial
        $y = max($y - 45, 90);
        $commands[] = $this->textCmd(380, $y, 'Bekasi, '.date('d F Y'), 8, false);
        $y -= 12;
        $commands[] = $this->textCmd(380, $y, 'Petugas Pengesah Finance,', 8, true);
        $y -= 32;
        $commands[] = $this->lineCmd(380, $y, 520, $y);
        $y -= 10;
        $commands[] = $this->textCmd(380, $y, ($reportData['officerName'] ?? 'Finance Officer'), 8, true);

        return $this->buildPdfBinary($commands, $pageWidth, $pageHeight);
    }

    protected function textCmd(float $x, float $y, string $text, int $fontSize = 10, bool $isBold = false): string
    {
        $fontName = $isBold ? '/F2' : '/F1';
        $safeText = $this->escapePdfText($text);

        return "BT {$fontName} {$fontSize} Tf {$x} {$y} Td ({$safeText}) Tj ET\n";
    }

    protected function lineCmd(float $x1, float $y1, float $x2, float $y2, float $gray = 0.5): string
    {
        return "{$gray} G {$x1} {$y1} m {$x2} {$y2} l S\n0 G\n";
    }

    protected function rectCmd(float $x, float $y, float $w, float $h, float $r = 0.9, float $g = 0.9, float $b = 0.9): string
    {
        return "{$r} {$g} {$b} rg {$x} {$y} {$w} {$h} re f\n0 0 0 rg\n";
    }

    protected function escapePdfText(string $text): string
    {
        $clean = preg_replace('/[^\x20-\x7E]/', '', $text);

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $clean);
    }

    protected function buildPdfBinary(array $streamCommands, int $pageWidth, int $pageHeight): string
    {
        $streamContent = implode('', $streamCommands);
        $streamLen = strlen($streamContent);

        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
        $objects[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$pageWidth} {$pageHeight}] /Contents 4 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> >>";
        $objects[4] = "<< /Length {$streamLen} >>\nstream\n{$streamContent}endstream";
        $objects[5] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[6] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $xrefOffsets = [];
        $xrefOffsets[0] = 0;

        for ($i = 1; $i <= 6; $i++) {
            $xrefOffsets[$i] = strlen($pdf);
            $pdf .= "{$i} 0 obj\n".$objects[$i]."\nendobj\n";
        }

        $xrefStart = strlen($pdf);
        $pdf .= "xref\n0 7\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= 6; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $xrefOffsets[$i]);
        }

        $pdf .= "trailer\n<< /Size 7 /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefStart}\n%%EOF\n";

        return $pdf;
    }
}
