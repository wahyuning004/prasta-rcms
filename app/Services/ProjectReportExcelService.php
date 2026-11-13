<?php

namespace App\Services;

class ProjectReportExcelService
{
    /**
     * Generate binary XLSX (Excel OpenXML) untuk Laporan Rekapitulasi Status Pengerjaan Proyek.
     *
     * @param  iterable  $projects  Koleksi model Order yang telah ditransformasi SLA
     * @param  array  $meta  Informasi metadata pelaporan (officerName, filterLabel, stats)
     * @return string Binary file content .xlsx
     */
    public function exportProjects(iterable $projects, array $meta = []): string
    {
        $rows = [];
        $r = 1;

        $officerName = $meta['officerName'] ?? 'Document & Licensing Officer';
        $filterLabel = $meta['filterLabel'] ?? 'Semua Status SLA';
        $exportDate = date('d F Y, H:i:s').' WIB';

        // 1. Header Dokumen Resmi
        $rows[] = $this->rowText($r++, [
            ['A', 'LAPORAN REKAPITULASI STATUS PENGERJAAN PROYEK PERIZINAN & SERTIFIKASI', true],
        ]);
        $rows[] = $this->rowText($r++, [
            ['A', 'CV PRASTA SOLUSI INDONESIA - DIVISI DOCUMENT & LICENSING OFFICER', true],
        ]);
        $rows[] = $this->rowText($r++, [
            ['A', "Tanggal Ekspor: {$exportDate} | Petugas: {$officerName} | Filter SLA: {$filterLabel}", false],
        ]);
        $r++; // Spasi pemisah

        // 2. Ringkasan Eksekutif (Statistik Proyek)
        $totalProjects = 0;
        $completedCount = 0;
        $inProgressCount = 0;
        $criticalCount = 0;
        $overdueCount = 0;
        $totalProgressPct = 0;

        foreach ($projects as $proj) {
            $totalProjects++;
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

        $rows[] = $this->rowText($r++, [
            ['A', 'RINGKASAN REKAPITULASI OPERASIONAL PROYEK', true],
        ]);
        $rows[] = $this->rowText($r++, [
            ['A', 'Total Seluruh Proyek:', true],
            ['B', (string) $totalProjects, true],
            ['C', 'Proyek Selesai:', true],
            ['D', (string) $completedCount, true],
            ['E', 'Proyek Berjalan:', true],
            ['F', (string) $inProgressCount, true],
            ['G', 'Kritis (<= 3 Hari):', true],
            ['H', (string) $criticalCount, true],
            ['I', 'Lewat Tenggat (Overdue):', true],
            ['J', (string) $overdueCount, true],
            ['K', 'Rata-rata Progres:', true],
            ['L', $avgProgress.'%', true],
        ]);
        $r++; // Spasi pemisah

        // 3. Header Tabel Kolom Rincian
        $headerCols = [
            ['A', 'No.', true],
            ['B', 'No. Pesanan', true],
            ['C', 'Perusahaan Mitra', true],
            ['D', 'Nama Klien (PIC)', true],
            ['E', 'Layanan Perizinan & Sertifikasi', true],
            ['F', 'Tahapan Pengerjaan Saat Ini', true],
            ['G', 'Progres (%)', true],
            ['H', 'Tanggal Mulai', true],
            ['I', 'Target Batas SLA', true],
            ['J', 'Sisa Hari SLA', true],
            ['K', 'Status Kepatuhan SLA', true],
            ['L', 'Status Pesanan', true],
            ['M', 'Catatan Perkembangan Terakhir', true],
        ];
        $rows[] = $this->rowText($r++, $headerCols, true);

        // 4. Data Baris Proyek
        $no = 1;
        foreach ($projects as $proj) {
            $companyName = $proj->user?->company_name ?: '-';
            $clientName = $proj->user?->name ?: 'Mitra';
            $serviceName = $proj->service_name ?: ($proj->service?->name ?? 'Layanan Perizinan');
            $stageName = $proj->current_stage ?: 'Pesanan Diterima';
            $progressPct = (int) ($proj->progress_percentage ?? 0);
            $startDateStr = ! empty($proj->start_date) ? $proj->start_date->format('d/m/Y') : '-';
            $deadlineDateStr = ! empty($proj->deadline_date) ? $proj->deadline_date->format('d/m/Y') : '-';
            $remainingDaysStr = $proj->is_completed ? '0 (Selesai)' : (string) ($proj->remaining_days ?? 0);
            $slaLabel = $proj->deadline_label ?: ($proj->is_completed ? 'Selesai' : 'Sesuai Jadwal');

            $orderStatus = match ($proj->status ?? '') {
                'completed' => 'Selesai',
                'processing' => 'Diproses',
                'cancelled' => 'Dibatalkan',
                default => 'Menunggu',
            };

            $latestNote = '-';
            if ($proj->relationLoaded('trackings') && $proj->trackings->isNotEmpty()) {
                $latestTrack = $proj->trackings->sortByDesc('id')->first();
                $latestNote = $latestTrack?->description ?: '-';
            }

            $rows[] = '<row r="'.$r.'">'.
                $this->cellNumber('A'.$r, $no++).
                $this->cellText('B'.$r, (string) ($proj->order_number ?? '-')).
                $this->cellText('C'.$r, $companyName).
                $this->cellText('D'.$r, $clientName).
                $this->cellText('E'.$r, $serviceName).
                $this->cellText('F'.$r, $stageName).
                $this->cellNumber('G'.$r, $progressPct).
                $this->cellText('H'.$r, $startDateStr).
                $this->cellText('I'.$r, $deadlineDateStr).
                $this->cellText('J'.$r, $remainingDaysStr).
                $this->cellText('K'.$r, $slaLabel).
                $this->cellText('L'.$r, $orderStatus).
                $this->cellText('M'.$r, $latestNote).
                '</row>';
            $r++;
        }

        // 5. Baris Total & Penutup
        $r++;
        $rows[] = '<row r="'.$r.'">'.
            $this->cellText('A'.$r, 'TOTAL PROYEK TERREKAPITULASI: '.$totalProjects.' PROYEK | RATA-RATA PROGRES: '.$avgProgress.'%', true).
            $this->cellText('B'.$r, '').
            $this->cellText('C'.$r, '').
            $this->cellText('D'.$r, '').
            $this->cellText('E'.$r, '').
            $this->cellText('F'.$r, '').
            $this->cellNumber('G'.$r, $avgProgress, true).
            $this->cellText('H'.$r, '').
            $this->cellText('I'.$r, '').
            $this->cellText('J'.$r, '').
            $this->cellText('K'.$r, '').
            $this->cellText('L'.$r, '').
            $this->cellText('M'.$r, '').
            '</row>';

        return $this->assembleXlsx($rows, 'Rekap Proyek SLA');
    }

    /**
     * Rakit struktur OpenXML spreadsheet menjadi arsip PKZip (.xlsx).
     */
    protected function assembleXlsx(array $sheetRows, string $sheetName = 'Rekap Proyek'): string
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
            '<xf numFmtId="3" fontId="0" fillId="0" borderId="0" xfId="0"/>'. // 2: Number (#,##0)
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
     * Mengemas file ke dalam format arsip ZIP biner standar PKZip.
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
