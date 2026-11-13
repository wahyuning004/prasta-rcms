<?php

namespace App\Services;

use App\Models\Invoice;

class InvoicePdfService
{
    /**
     * Generate binary PDF 1.4 yang valid untuk Invoice tanpa dependensi eksternal.
     */
    public function generate(Invoice $invoice): string
    {
        $order = $invoice->order;
        $orderNumber = $order?->order_number ?? '-';
        $promoCode = $order?->promo_code ? ' (Voucher: '.$order->promo_code.')' : '';

        $lines = [];
        $y = 770;

        // Kop Perusahaan
        $lines[] = $this->textCmd(50, $y, 'CV PRASTA SOLUSI INDONESIA', 16, true);
        $y -= 16;
        $lines[] = $this->textCmd(50, $y, 'Platform Manajemen Perizinan & Sertifikasi Bisnis (IzinYou.com)', 9, false);
        $y -= 12;
        $lines[] = $this->textCmd(50, $y, 'Cluster New Liverpool Blok P18A No 46, Babelan, Kab. Bekasi, Jawa Barat 17610', 8, false);
        $y -= 12;
        $lines[] = $this->textCmd(50, $y, 'NPWP: 94.812.334.2-407.000 | Email: prastasolusi.indonesia@gmail.com | WA: +62 821-2876-5874', 8, false);
        $y -= 10;
        $lines[] = $this->lineCmd(50, $y, 545, $y);

        // Judul Invoice
        $y -= 26;
        $lines[] = $this->textCmd(50, $y, 'INVOICE TAGIHAN RESMI', 14, true);
        $lines[] = $this->textCmd(360, $y, 'No. Invoice: '.$invoice->invoice_number, 10, true);
        $y -= 16;
        $lines[] = $this->textCmd(50, $y, 'No. Pesanan: '.$orderNumber, 10, false);
        $lines[] = $this->textCmd(360, $y, 'Tanggal Terbit: '.$invoice->issued_at->format('d F Y'), 10, false);
        $y -= 14;
        $lines[] = $this->textCmd(50, $y, 'Status Pembayaran: '.strtoupper($invoice->statusLabel()), 10, true);
        if ($invoice->due_date) {
            $lines[] = $this->textCmd(360, $y, 'Jatuh Tempo: '.$invoice->due_date->format('d F Y'), 10, false);
        }

        // Identitas Pelanggan
        $y -= 26;
        $lines[] = $this->lineCmd(50, $y + 8, 545, $y + 8);
        $lines[] = $this->textCmd(50, $y - 4, 'DITAGIHKAN KEPADA:', 9, true);
        $y -= 18;
        $lines[] = $this->textCmd(50, $y, ($invoice->customer_company ?: $invoice->customer_name), 11, true);
        $y -= 14;
        $lines[] = $this->textCmd(50, $y, 'PIC / Nama Pelanggan: '.$invoice->customer_name, 9, false);
        $y -= 13;
        $lines[] = $this->textCmd(50, $y, 'Email / Telepon: '.($invoice->customer_email ?: '-').' | '.($invoice->customer_phone ?: '-'), 9, false);
        $y -= 13;
        foreach ($this->wrapText('Alamat: '.($invoice->customer_address ?: '-'), 85) as $addrLine) {
            $lines[] = $this->textCmd(50, $y, $addrLine, 9, false);
            $y -= 12;
        }

        // Tabel Rincian Layanan
        $y -= 14;
        $lines[] = $this->lineCmd(50, $y, 545, $y);
        $y -= 15;
        $lines[] = $this->textCmd(55, $y, 'NO', 9, true);
        $lines[] = $this->textCmd(90, $y, 'LAYANAN PERIZINAN & SERTIFIKASI', 9, true);
        $lines[] = $this->textCmd(380, $y, 'QTY', 9, true);
        $lines[] = $this->textCmd(435, $y, 'SUBTOTAL (IDR)', 9, true);
        $y -= 8;
        $lines[] = $this->lineCmd(50, $y, 545, $y);

        $y -= 18;
        $lines[] = $this->textCmd(55, $y, '1', 10, false);
        $serviceLines = $this->wrapText($invoice->service_name, 48);
        foreach ($serviceLines as $idx => $sLine) {
            $lines[] = $this->textCmd(90, $y - ($idx * 13), $sLine, 10, $idx === 0);
        }
        $lines[] = $this->textCmd(380, $y, '1 Paket', 10, false);
        $lines[] = $this->textCmd(435, $y, 'Rp '.number_format((float) $invoice->subtotal, 0, ',', '.'), 10, false);
        $y -= max(18, count($serviceLines) * 14);
        $lines[] = $this->lineCmd(50, $y, 545, $y);

        // Ringkasan Total
        $y -= 20;
        $lines[] = $this->textCmd(310, $y, 'Harga Layanan Normal', 10, false);
        $lines[] = $this->textCmd(435, $y, 'Rp '.number_format((float) $invoice->subtotal, 0, ',', '.'), 10, false);
        $y -= 16;
        $lines[] = $this->textCmd(310, $y, 'Potongan Diskon'.$promoCode, 10, false);
        $lines[] = $this->textCmd(435, $y, '- Rp '.number_format((float) $invoice->discount_amount, 0, ',', '.'), 10, false);
        $y -= 10;
        $lines[] = $this->lineCmd(310, $y, 545, $y);
        $y -= 18;
        $lines[] = $this->textCmd(310, $y, 'TOTAL TAGIHAN', 11, true);
        $lines[] = $this->textCmd(435, $y, 'Rp '.number_format((float) $invoice->total_amount, 0, ',', '.'), 11, true);

        // Informasi Rekening Pembayaran
        $y -= 40;
        $lines[] = $this->lineCmd(50, $y + 10, 545, $y + 10);
        $lines[] = $this->textCmd(50, $y - 4, 'INFORMASI REKENING PEMBAYARAN RESMI:', 9, true);
        $y -= 18;
        $lines[] = $this->textCmd(50, $y, 'Bank Mandiri KCP Bekasi - No. Rekening: 156-00-1928374-1 - a.n. CV PRASTA SOLUSI INDONESIA', 9, false);
        $y -= 13;
        $lines[] = $this->textCmd(50, $y, 'Bank BCA - No. Rekening: 522-0918273 - a.n. CV PRASTA SOLUSI INDONESIA', 9, false);
        $y -= 13;
        $lines[] = $this->textCmd(50, $y, 'Bank BNI - No. Rekening: 084-9201753 - a.n. CV PRASTA SOLUSI INDONESIA', 9, false);
        $y -= 13;
        $lines[] = $this->textCmd(50, $y, 'QRIS Resmi - CV PRASTA SOLUSI INDONESIA (NMID: ID1026001928374)', 9, false);
        $y -= 22;
        $lines[] = $this->textCmd(50, $y, 'Dokumen ini diterbitkan secara elektronik oleh sistem IzinYou (CV Prasta Solusi Indonesia) dan sah tanpa tanda tangan basah.', 8, false);

        $streamContent = implode("\n", $lines);

        return $this->buildPdfDocument($streamContent);
    }

    protected function textCmd(int $x, int $y, string $text, int $size = 10, bool $bold = false): string
    {
        $font = $bold ? '/F2' : '/F1';
        $safe = $this->escapePdfText($this->toAscii($text));

        return "BT {$font} {$size} Tf 1 0 0 1 {$x} {$y} Tm ({$safe}) Tj ET";
    }

    protected function lineCmd(int $x1, int $y1, int $x2, int $y2): string
    {
        return "0.5 w {$x1} {$y1} m {$x2} {$y2} l S";
    }

    protected function escapePdfText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    protected function toAscii(string $text): string
    {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        return $converted !== false ? $converted : preg_replace('/[^\x20-\x7E]/', ' ', $text);
    }

    /**
     * @return array<int, string>
     */
    protected function wrapText(string $text, int $width): array
    {
        return explode("\n", wordwrap(trim($text), $width, "\n", true));
    }

    protected function buildPdfDocument(string $contentStream): string
    {
        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
        $objects[3] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[5] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';
        $length = strlen($contentStream);
        $objects[6] = "<< /Length {$length} >>\nstream\n{$contentStream}\nendstream";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $num => $body) {
            $offsets[$num] = strlen($pdf);
            $pdf .= "{$num} 0 obj\n{$body}\nendobj\n";
        }

        $xrefPos = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 {$count}\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($objects as $num => $body) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$num]);
        }

        $pdf .= "trailer\n<< /Size {$count} /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefPos}\n%%EOF";

        return $pdf;
    }
}
