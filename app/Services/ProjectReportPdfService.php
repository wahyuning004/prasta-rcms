<?php

namespace App\Services;

class ProjectReportPdfService
{
    /**
     * Generate binary PDF 1.4 resmi untuk Laporan Rekapitulasi Status Pengerjaan Proyek.
     *
     * @param  iterable  $projects  Koleksi model Order yang telah ditransformasi SLA
     * @param  array  $meta  Informasi metadata pelaporan (officerName, filterLabel, stats)
     * @return string Binary PDF content
     */
    public function exportProjects(iterable $projects, array $meta = []): string
    {
        $pageWidth = 842; // Landscape A4
        $pageHeight = 595;

        $officerName = $meta['officerName'] ?? 'Document & Licensing Officer';
        $filterLabel = $meta['filterLabel'] ?? 'Semua Status SLA';
        $dateStr = date('d F Y');
        $exportTime = date('d F Y, H:i').' WIB';

        // 1. Hitung Ringkasan Statistik
        $projectList = is_array($projects) ? $projects : iterator_to_array($projects);
        $totalProjects = count($projectList);
        $completedCount = 0;
        $inProgressCount = 0;
        $criticalCount = 0;
        $overdueCount = 0;
        $totalProgressPct = 0;

        foreach ($projectList as $proj) {
            $pct = (int) ($proj->progress_percentage ?? 0);
            $totalProgressPct += $pct;

            if ($proj->is_completed || ($proj->deadline_code ?? '') === 'completed') {
                $completedCount++;
            } elseif (($proj->deadline_code ?? '') === 'overdue') {
                $overdueCount++;
                $inProgressCount++;
            } elseif (($proj->deadline_code ?? '') === 'critical') {
                $criticalCount++;
                $inProgressCount++;
            } else {
                $inProgressCount++;
            }
        }

        $avgProgress = $totalProjects > 0 ? round($totalProgressPct / $totalProjects, 1) : 0;

        // 2. Chunking Data ke Halaman-Halaman PDF (Multi-Page Generator)
        $pagesCommands = [];
        $currentPageCmds = [];
        $no = 1;
        $projIndex = 0;
        $pageNumber = 1;

        // Header halaman 1
        $y = 560;
        $currentPageCmds[] = $this->textCmd(35, $y, 'CV PRASTA SOLUSI INDONESIA', 13, true);
        $y -= 14;
        $currentPageCmds[] = $this->textCmd(35, $y, 'DIVISI DOCUMENT & LICENSING OFFICER - LAPORAN REKAPITULASI PROYEK & KEPATUHAN SLA', 10, true);
        $y -= 12;
        $currentPageCmds[] = $this->textCmd(35, $y, "Tanggal Ekspor: {$exportTime} | Petugas: {$officerName} | Filter SLA: {$filterLabel}", 8, false);
        $y -= 8;
        $currentPageCmds[] = $this->lineCmd(35, $y, 807, $y);

        // Ringkasan Eksekutif (Kotak Metrik) di Halaman 1
        $y -= 22;
        $currentPageCmds[] = $this->rectCmd(35, $y - 6, 772, 22, 0.94, 0.97, 0.98);
        $currentPageCmds[] = $this->lineCmd(35, $y - 6, 807, $y - 6, 0.8);
        $currentPageCmds[] = $this->lineCmd(35, $y + 16, 807, $y + 16, 0.8);

        $summaryText = "TOTAL PROYEK: {$totalProjects}  |  SELESAI: {$completedCount}  |  BERJALAN: {$inProgressCount}  |  KRITIS (<= 3 HARI): {$criticalCount}  |  LEWAT TENGGAT: {$overdueCount}  |  RATA-RATA PROGRES: {$avgProgress}%";
        $currentPageCmds[] = $this->textCmd(42, $y, $summaryText, 8, true);

        // Header Tabel Kolom
        $y -= 20;
        $currentPageCmds[] = $this->buildTableHeaderCmds($y);

        while ($projIndex < $totalProjects) {
            $proj = $projectList[$projIndex];

            // Cek apakah sisa ruang halaman masih cukup (minimal 30pt untuk baris data)
            if ($y < 70) {
                // Simpan halaman saat ini dan buat halaman baru
                $pagesCommands[] = $currentPageCmds;
                $pageNumber++;
                $currentPageCmds = [];

                // Header Halaman Lanjutan
                $y = 560;
                $currentPageCmds[] = $this->textCmd(35, $y, 'CV PRASTA SOLUSI INDONESIA - LAPORAN REKAPITULASI PROYEK (LANJUTAN)', 11, true);
                $y -= 12;
                $currentPageCmds[] = $this->textCmd(35, $y, "Tanggal Ekspor: {$exportTime} | Filter: {$filterLabel} | Halaman {$pageNumber}", 8, false);
                $y -= 8;
                $currentPageCmds[] = $this->lineCmd(35, $y, 807, $y);

                $y -= 18;
                $currentPageCmds[] = $this->buildTableHeaderCmds($y);
            }

            // Gambar Baris Data Proyek
            $y -= 15;
            $orderNo = (string) ($proj->order_number ?? '-');
            $compName = substr((string) ($proj->user?->company_name ?: '-'), 0, 28);
            $picName = substr((string) ($proj->user?->name ?: 'Mitra'), 0, 20);
            $serviceName = substr((string) ($proj->service_name ?: ($proj->service?->name ?? 'Layanan')), 0, 30);
            $stageName = substr((string) ($proj->current_stage ?: 'Pesanan Diterima'), 0, 22);
            $pctText = ((int) ($proj->progress_percentage ?? 0)).'%';
            $deadlineStr = ! empty($proj->deadline_date) ? $proj->deadline_date->format('d/m/Y') : '-';
            $remainingStr = $proj->is_completed ? 'Selesai' : (string) ($proj->remaining_days ?? 0);
            $slaLabel = substr((string) ($proj->deadline_label ?: ($proj->is_completed ? 'Selesai' : 'On-Track')), 0, 18);

            $currentPageCmds[] = $this->textCmd(38, $y, (string) ($no++), 7, false);
            $currentPageCmds[] = $this->textCmd(62, $y, $orderNo, 7, true);
            $currentPageCmds[] = $this->textCmd(150, $y, $compName, 7, false);
            $currentPageCmds[] = $this->textCmd(265, $y, $picName, 7, false);
            $currentPageCmds[] = $this->textCmd(355, $y, $serviceName, 7, false);
            $currentPageCmds[] = $this->textCmd(495, $y, $stageName, 7, false);
            $currentPageCmds[] = $this->textCmd(600, $y, $pctText, 7, true);
            $currentPageCmds[] = $this->textCmd(645, $y, $deadlineStr, 7, false);
            $currentPageCmds[] = $this->textCmd(715, $y, $remainingStr, 7, false);
            $currentPageCmds[] = $this->textCmd(755, $y, $slaLabel, 7, true);

            $currentPageCmds[] = $this->lineCmd(35, $y - 3, 807, $y - 3, 0.9);

            $projIndex++;
        }

        // Cek ruang untuk Bagian Tanda Tangan Pengesahan (membutuhkan ~65pt)
        if ($y < 85) {
            $pagesCommands[] = $currentPageCmds;
            $pageNumber++;
            $currentPageCmds = [];

            $y = 560;
            $currentPageCmds[] = $this->textCmd(35, $y, 'CV PRASTA SOLUSI INDONESIA - LEMBAR PENGESAHAN LAPORAN', 11, true);
            $y -= 8;
            $currentPageCmds[] = $this->lineCmd(35, $y, 807, $y);
            $y -= 30;
        } else {
            $y -= 25;
        }

        // Kolom Tanda Tangan Pengesahan Officer
        $currentPageCmds[] = $this->textCmd(630, $y, "Bekasi, {$dateStr}", 8, false);
        $y -= 12;
        $currentPageCmds[] = $this->textCmd(630, $y, 'Document & Licensing Officer,', 8, true);
        $y -= 30;
        $currentPageCmds[] = $this->lineCmd(630, $y, 790, $y);
        $y -= 10;
        $currentPageCmds[] = $this->textCmd(630, $y, (string) $officerName, 8, true);

        $pagesCommands[] = $currentPageCmds;

        return $this->buildMultiPagePdfBinary($pagesCommands, $pageWidth, $pageHeight);
    }

    protected function buildTableHeaderCmds(float $y): string
    {
        $cmds = [];
        $cmds[] = $this->rectCmd(35, $y - 4, 772, 16, 0.92, 0.94, 0.96);
        $cmds[] = $this->textCmd(38, $y, 'NO', 7, true);
        $cmds[] = $this->textCmd(62, $y, 'NO. PESANAN', 7, true);
        $cmds[] = $this->textCmd(150, $y, 'PERUSAHAAN MITRA', 7, true);
        $cmds[] = $this->textCmd(265, $y, 'NAMA PIC', 7, true);
        $cmds[] = $this->textCmd(355, $y, 'LAYANAN PERIZINAN', 7, true);
        $cmds[] = $this->textCmd(495, $y, 'TAHAP SAAT INI', 7, true);
        $cmds[] = $this->textCmd(600, $y, 'PROGRES', 7, true);
        $cmds[] = $this->textCmd(645, $y, 'TARGET SLA', 7, true);
        $cmds[] = $this->textCmd(715, $y, 'SISA HARI', 7, true);
        $cmds[] = $this->textCmd(755, $y, 'STATUS SLA', 7, true);
        $cmds[] = $this->lineCmd(35, $y - 4, 807, $y - 4, 0.7);

        return implode('', $cmds);
    }

    protected function textCmd(float $x, float $y, string $text, int $fontSize = 8, bool $isBold = false): string
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

    /**
     * Membangun file PDF 1.4 multi-halaman binary valid.
     */
    protected function buildMultiPagePdfBinary(array $pagesCommands, int $pageWidth, int $pageHeight): string
    {
        $pageCount = count($pagesCommands);
        $objects = [];

        // Font Objects (Helvetica & Helvetica-Bold)
        $font1Id = ($pageCount * 2) + 3;
        $font2Id = ($pageCount * 2) + 4;

        $pageObjIds = [];
        $objIndex = 3;

        for ($i = 0; $i < $pageCount; $i++) {
            $pageObjId = $objIndex++;
            $contentObjId = $objIndex++;
            $pageObjIds[] = $pageObjId;

            $streamContent = implode('', $pagesCommands[$i]);
            $streamLen = strlen($streamContent);

            $objects[$pageObjId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$pageWidth} {$pageHeight}] /Contents {$contentObjId} 0 R /Resources << /Font << /F1 {$font1Id} 0 R /F2 {$font2Id} 0 R >> >> >>";
            $objects[$contentObjId] = "<< /Length {$streamLen} >>\nstream\n{$streamContent}endstream";
        }

        $kidsStr = implode(' ', array_map(fn ($id) => "{$id} 0 R", $pageObjIds));

        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = "<< /Type /Pages /Kids [{$kidsStr}] /Count {$pageCount} >>";
        $objects[$font1Id] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[$font2Id] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        // Urutkan objek berdasarkan ID
        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $xrefOffsets = [];
        $xrefOffsets[0] = 0;

        $maxObjId = max(array_keys($objects));

        for ($i = 1; $i <= $maxObjId; $i++) {
            if (isset($objects[$i])) {
                $xrefOffsets[$i] = strlen($pdf);
                $pdf .= "{$i} 0 obj\n".$objects[$i]."\nendobj\n";
            }
        }

        $xrefStart = strlen($pdf);
        $totalXref = $maxObjId + 1;
        $pdf .= "xref\n0 {$totalXref}\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= $maxObjId; $i++) {
            $offset = $xrefOffsets[$i] ?? 0;
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size {$totalXref} /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefStart}\n%%EOF\n";

        return $pdf;
    }
}
