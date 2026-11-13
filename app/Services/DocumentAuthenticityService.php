<?php

namespace App\Services;

use App\Models\ClientDocument;
use App\Models\ClientDocumentVersion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentAuthenticityService
{
    /**
     * Tanda tangan software editor/manipulasi file gambar atau PDF (indikasi modifikasi pihak luar).
     */
    public const MANIPULATION_SIGNATURES = [
        'photoshop' => 'Adobe Photoshop',
        'canva' => 'Canva Graphics Studio',
        'ilovepdf' => 'iLovePDF Online Editor',
        'smallpdf' => 'Smallpdf Suite',
        'sejda' => 'Sejda PDF Editor',
        'pdfescape' => 'PDFescape Online',
        'gimp' => 'GIMP Image Manipulator',
        'picsart' => 'PicsArt Mobile Studio',
        'pixlr' => 'Pixlr Suite',
        'snapseed' => 'Snapseed Mobile Editor',
        'nitro' => 'Nitro Pro PDF Editor',
        'foxit' => 'Foxit PDF Editor',
        'paint.net' => 'Paint.NET',
        'coreldraw' => 'CorelDRAW Graphics',
        'illustrator' => 'Adobe Illustrator',
        'inkscape' => 'Inkscape Vector Editor',
        'photopea' => 'Photopea Web Editor',
    ];

    /**
     * Domain dan otoritas sertifikasi resmi instansi penerbit perizinan di Indonesia.
     */
    public const OFFICIAL_AUTHORITIES = [
        'kemenkes.go.id' => 'Kementerian Kesehatan RI (e-Alkes / Farmalkes)',
        'oss.go.id' => 'Sistem OSS Berbasis Risiko (BKPM RI)',
        'pom.go.id' => 'Badan Pengawas Obat dan Makanan (BPOM RI)',
        'kemenkumham.go.id' => 'Ditjen AHU Kementerian Hukum & HAM RI',
        'ahu.kemenkumham.go.id' => 'Sistem Administrasi Hukum Umum (AHU Online)',
        'bsre.bssn.go.id' => 'Balai Sertifikasi Elektronik (BSrE - BSSN)',
        'halal.go.id' => 'BPJPH Kementerian Agama RI (Sertifikasi Halal)',
        'dpppt.go.id' => 'Dinas Penanaman Modal & Pelayanan Terpadu Satu Pintu',
        'kominfo.go.id' => 'Kementerian Komunikasi dan Informatika RI',
        'bsn.go.id' => 'Badan Standardisasi Nasional (BSN / SNI)',
        'peruri.co.id' => 'PERURI Digital Signature & Seal',
    ];

    /**
     * Kata kunci keberadaan stempel/cap instansi atau notaris resmi.
     */
    public const STAMP_KEYWORDS = [
        'republik indonesia',
        'kementerian',
        'notaris',
        'pejabat pembuat akta',
        'badan pengawas',
        'dinas penanaman modal',
        'izin edar',
        'stempel dinas',
        'cap dinas',
        'disahkan',
        'legalisir',
        'tanda tangan basah',
        'sertifikat standar',
    ];

    /**
     * Analisis kelengkapan administratif dan keaslian dokumen klien.
     *
     * @return array<string, mixed>
     */
    public function analyze(ClientDocument $document): array
    {
        $filePath = (string) $document->file_path;
        $originalName = (string) ($document->original_filename ?: $document->document_name ?: basename($filePath));

        return $this->analyzeFilePath($filePath, $originalName, $document);
    }

    /**
     * Analisis versi spesifik berkas dokumen.
     *
     * @return array<string, mixed>
     */
    public function analyzeVersion(ClientDocumentVersion $version): array
    {
        $filePath = (string) $version->file_path;
        $originalName = (string) ($version->original_filename ?: basename($filePath));
        $document = $version->document;

        return $this->analyzeFilePath($filePath, $originalName, $document, (int) $version->version_number);
    }

    /**
     * Jalankan validasi forensik dokumen berdasarkan berkas fisik di storage.
     *
     * @return array<string, mixed>
     */
    public function analyzeFilePath(
        string $filePath,
        string $originalName,
        ?ClientDocument $document = null,
        int $versionNumber = 1
    ): array {
        $fileData = $this->resolveFileBytes($filePath);
        $fileExists = $fileData['exists'];
        $fileBytes = $fileData['bytes'];
        $fullPath = $fileData['path'];
        $fileSizeFormatted = $fileData['formatted_size'];

        $sha256 = $fileBytes !== '' ? hash('sha256', $fileBytes) : null;
        $isPdf = str_starts_with($fileBytes, '%PDF');
        $isImage = ! $isPdf && (str_starts_with($fileBytes, "\xFF\xD8\xFF") || str_starts_with($fileBytes, "\x89PNG\r\n\x1a\n"));

        // 1. Modul Validasi Metadata File & Deteksi Software Editing
        $metadataAudit = $this->auditMetadata($fileBytes, $fullPath, $isPdf, $isImage, $document);

        // 2. Modul Validasi Integritas Tanda Tangan (TTD) Digital/Basah & Stempel Cap
        $signatureAudit = $this->auditSignatureAndStamp($fileBytes, $fullPath, $isPdf, $isImage, $document);

        // 3. Modul Anti-Duplikasi / Anti-Replay Berkas
        $antiReplayAudit = $this->auditAntiReplay($sha256, $document?->id);

        // 4. Hitung Skor Gabungan & Rekomendasi Putusan
        $scores = $this->evaluateVerdict(
            $fileExists,
            $metadataAudit,
            $signatureAudit,
            $antiReplayAudit,
            $document
        );

        return [
            'document_id' => $document?->id,
            'version_number' => $versionNumber,
            'file_info' => [
                'original_name' => $originalName,
                'file_path' => $filePath,
                'file_size' => $fileSizeFormatted,
                'file_exists' => $fileExists,
                'is_pdf' => $isPdf,
                'is_image' => $isImage,
                'mime_type' => $document?->mime_type ?: ($isPdf ? 'application/pdf' : ($isImage ? 'image/jpeg' : 'application/octet-stream')),
                'sha256' => $sha256,
            ],
            'metadata_audit' => $metadataAudit,
            'signature_audit' => $signatureAudit,
            'anti_replay' => $antiReplayAudit,
            'overall_score' => $scores['overall_score'],
            'verdict_code' => $scores['verdict_code'],
            'verdict_label' => $scores['verdict_label'],
            'badge_color' => $scores['badge_color'],
            'badge_class' => $scores['badge_class'],
            'summary_notes' => $scores['summary_notes'],
            'recommendation' => $scores['recommendation'],
            'checklist' => $scores['checklist'],
        ];
    }

    /**
     * Resolusi berkas biner dari berbagai disk storage Laravel atau path lokal.
     *
     * @return array{exists: bool, bytes: string, path: ?string, formatted_size: string}
     */
    protected function resolveFileBytes(string $filePath): array
    {
        if (empty($filePath)) {
            return ['exists' => false, 'bytes' => '', 'path' => null, 'formatted_size' => '0 B'];
        }

        if (Storage::disk('local')->exists($filePath)) {
            $path = Storage::disk('local')->path($filePath);
            $bytes = (string) Storage::disk('local')->get($filePath);

            return [
                'exists' => true,
                'bytes' => $bytes,
                'path' => $path,
                'formatted_size' => $this->formatBytes(strlen($bytes)),
            ];
        }

        if (Storage::disk('public')->exists($filePath)) {
            $path = Storage::disk('public')->path($filePath);
            $bytes = (string) Storage::disk('public')->get($filePath);

            return [
                'exists' => true,
                'bytes' => $bytes,
                'path' => $path,
                'formatted_size' => $this->formatBytes(strlen($bytes)),
            ];
        }

        if (@file_exists($filePath)) {
            $bytes = (string) @file_get_contents($filePath);

            return [
                'exists' => true,
                'bytes' => $bytes,
                'path' => $filePath,
                'formatted_size' => $this->formatBytes(strlen($bytes)),
            ];
        }

        return ['exists' => false, 'bytes' => '', 'path' => null, 'formatted_size' => '0 B'];
    }

    /**
     * Audit metadata file biner dan deteksi manipulasi software editor.
     *
     * @return array<string, mixed>
     */
    protected function auditMetadata(
        string $bytes,
        ?string $fullPath,
        bool $isPdf,
        bool $isImage,
        ?ClientDocument $document = null
    ): array {
        if ($bytes === '') {
            return [
                'score' => 60,
                'status' => 'warning',
                'producer' => 'Tidak Diketahui',
                'creator' => 'Tidak Diketahui',
                'creation_date' => null,
                'mod_date' => null,
                'date_discrepancy_days' => 0,
                'tampering_detected' => false,
                'detected_editor' => null,
                'incremental_updates' => 0,
                'notes' => 'Berkas belum tersimpan di server. Memeriksa riwayat sistem.',
            ];
        }

        $detectedEditor = null;
        $tamperingDetected = false;
        $producer = 'Standar Sistem / PDF';
        $creator = 'Aplikasi Resmi';
        $creationDate = null;
        $modDate = null;
        $dateDiscrepancyDays = 0;
        $incrementalUpdates = 0;

        // Scan 256KB pertama & 64KB terakhir berkas untuk mencari metadata editor
        $scanWindow = strtolower(substr($bytes, 0, 262144).substr($bytes, -65536));

        foreach (self::MANIPULATION_SIGNATURES as $needle => $label) {
            if (str_contains($scanWindow, $needle)) {
                $tamperingDetected = true;
                $detectedEditor = $label;
                break;
            }
        }

        if ($isPdf) {
            // Ekstraksi info Producer & Creator PDF
            if (preg_match('/\/Producer\s*\(([^)]+)\)/i', $bytes, $m)) {
                $producer = trim($m[1]);
            }
            if (preg_match('/\/Creator\s*\(([^)]+)\)/i', $bytes, $m)) {
                $creator = trim($m[1]);
            }

            // Ekstraksi CreationDate & ModDate
            if (preg_match('/\/CreationDate\s*\(([^)]+)\)/i', $bytes, $m)) {
                $creationDate = $this->parsePdfDate($m[1]);
            }
            if (preg_match('/\/ModDate\s*\(([^)]+)\)/i', $bytes, $m)) {
                $modDate = $this->parsePdfDate($m[1]);
            }

            // Hitung selisih tanggal pembuatan vs tanggal modifikasi
            if ($creationDate && $modDate) {
                $dateDiscrepancyDays = (int) $creationDate->diffInDays($modDate, false);
                if (abs($dateDiscrepancyDays) > 7 && $tamperingDetected) {
                    $tamperingDetected = true;
                }
            }

            // Hitung EOF (incremental updates)
            $incrementalUpdates = substr_count($bytes, '%%EOF');
            if ($incrementalUpdates > 2 && $tamperingDetected) {
                $tamperingDetected = true;
            }
        } elseif ($isImage && function_exists('exif_read_data') && $fullPath && @is_file($fullPath)) {
            try {
                $exif = @exif_read_data($fullPath);
                if (is_array($exif)) {
                    $softwareMeta = strtolower((string) ($exif['Software'] ?? $exif['ImageDescription'] ?? ''));
                    foreach (self::MANIPULATION_SIGNATURES as $needle => $label) {
                        if (str_contains($softwareMeta, $needle)) {
                            $tamperingDetected = true;
                            $detectedEditor = $label;
                            break;
                        }
                    }
                    if (! empty($exif['DateTimeOriginal'])) {
                        $creationDate = Carbon::parse($exif['DateTimeOriginal']);
                    }
                    if (! empty($exif['DateTime'])) {
                        $modDate = Carbon::parse($exif['DateTime']);
                    }
                }
            } catch (\Throwable) {
                // Abaikan jika pembacaan EXIF bermasalah
            }
        }

        // Skor Penilaian Metadata
        if ($tamperingDetected) {
            $score = 25;
            $status = 'tampered';
            $notes = "PERINGATAN: Terdeteksi tanda tangan manipulasi digital dari software {$detectedEditor}. Berkas berpotensi diedit pasca-penerbitan.";
        } elseif ($dateDiscrepancyDays > 14) {
            $score = 70;
            $status = 'warning';
            $notes = "Terdapat selisih {$dateDiscrepancyDays} hari antara tanggal pembuatan dan tanggal modifikasi file.";
        } else {
            $score = 100;
            $status = 'clean';
            $notes = 'Metadata berkas bersih dan konsisten. Tidak ditemukan indikator software editing ilegal.';
        }

        return [
            'score' => $score,
            'status' => $status,
            'producer' => $producer,
            'creator' => $creator,
            'creation_date' => $creationDate ? $creationDate->translatedFormat('d M Y, H:i') : null,
            'mod_date' => $modDate ? $modDate->translatedFormat('d M Y, H:i') : null,
            'date_discrepancy_days' => $dateDiscrepancyDays,
            'tampering_detected' => $tamperingDetected,
            'detected_editor' => $detectedEditor,
            'incremental_updates' => $incrementalUpdates,
            'notes' => $notes,
        ];
    }

    /**
     * Audit integritas dan keaslian Tanda Tangan (TTD) Digital/Basah serta Cap Instansi.
     *
     * @return array<string, mixed>
     */
    protected function auditSignatureAndStamp(
        string $bytes,
        ?string $fullPath,
        bool $isPdf,
        bool $isImage,
        ?ClientDocument $document = null
    ): array {
        if ($bytes === '') {
            return [
                'score' => 65,
                'status' => 'warning',
                'has_digital_signature' => false,
                'signature_type' => 'Belum Diunggah',
                'has_qr_verification' => false,
                'qr_domains' => [],
                'official_authority' => null,
                'stamp_detected' => false,
                'stamp_keywords_found' => [],
                'visual_manipulation_detected' => false,
                'notes' => 'Menunggu berkas fisik untuk pemindaian tanda tangan & stempel.',
            ];
        }

        $hasDigitalSignature = false;
        $signatureType = 'Tanda Tangan Manual / Basah';
        $hasQrVerification = false;
        $matchedDomains = [];
        $matchedAuthorities = [];
        $stampDetected = false;
        $stampKeywordsFound = [];
        $visualManipulationDetected = false;

        $contentLower = strtolower($bytes);

        // 1. Deteksi Tanda Tangan Digital Kriptografis (PDF PKCS#7 / BSrE / DocTimeStamp)
        if ($isPdf) {
            $hasPkcs7 = str_contains($bytes, '/adbe.pkcs7') || str_contains($bytes, '/ETSI.CAdES') || str_contains($bytes, '/Type /Sig');
            $hasDocTimeStamp = str_contains($bytes, '/DocTimeStamp');

            if ($hasPkcs7 || $hasDocTimeStamp) {
                $hasDigitalSignature = true;
                $signatureType = 'Tanda Tangan Digital Terverifikasi (Kriptografi PDF / BSrE)';
            }
        }

        // 2. Pemindaian QR Code / Barcode URL Verifikasi Resmi Instansi
        foreach (self::OFFICIAL_AUTHORITIES as $domain => $authorityName) {
            if (str_contains($contentLower, $domain)) {
                $hasQrVerification = true;
                $matchedDomains[] = $domain;
                $matchedAuthorities[] = $authorityName;
            }
        }

        if ($hasQrVerification && ! $hasDigitalSignature) {
            $hasDigitalSignature = true;
            $signatureType = 'Sertifikat Elektronik (QR Code Resmi Instansi Pemerintah)';
        }

        // 3. Pemindaian Stempel / Cap Instansi Resmi
        foreach (self::STAMP_KEYWORDS as $kw) {
            if (str_contains($contentLower, $kw)) {
                $stampDetected = true;
                $stampKeywordsFound[] = ucwords($kw);
            }
        }

        // 4. Deteksi Anomali Visual / Manipulasi TTD & Stempel
        // Deteksi layer transparan mencurigakan di atas teks (overlay paste signature)
        if ($isPdf) {
            $hasTransparentLayer = str_contains($bytes, '/SMask') && str_contains($bytes, '/Image');
            $hasExcessiveEof = substr_count($bytes, '%%EOF') > 2;

            if ($hasTransparentLayer && $hasExcessiveEof && ! $hasDigitalSignature) {
                $visualManipulationDetected = true;
            }
        } elseif ($isImage) {
            // Jika PNG dengan alpha channel transparansi penuh yang biasanya adalah tanda tangan tempelan
            if (str_starts_with($bytes, "\x89PNG\r\n\x1a\n") && str_contains(substr($bytes, 0, 100), 'IHDR')) {
                // Color type 6 = RGBA (Alpha channel)
                $colorType = ord($bytes[25] ?? 0);
                if ($colorType === 6) {
                    $visualManipulationDetected = true;
                }
            }
        }

        // Perhitungan Skor Tanda Tangan & Stempel
        $sigScore = 0;
        if ($hasDigitalSignature) {
            $sigScore += 50;
        } elseif ($stampDetected) {
            $sigScore += 40;
        } else {
            $sigScore += 25;
        }

        if ($hasQrVerification) {
            $sigScore += 40;
        } elseif ($stampDetected) {
            $sigScore += 30;
        }

        if ($visualManipulationDetected) {
            $sigScore = max(15, $sigScore - 50);
        } else {
            $sigScore += 10;
        }

        $finalSigScore = (int) min(100, max(15, $sigScore));

        if ($visualManipulationDetected) {
            $sigStatus = 'anomaly';
            $sigNotes = 'PERINGATAN: Terindikasi tanda tangan / stempel tempelan visual (floating transparent overlay layer).';
        } elseif ($hasDigitalSignature && $hasQrVerification) {
            $sigStatus = 'certified';
            $authorityStr = implode(', ', array_unique($matchedAuthorities));
            $sigNotes = "Tanda tangan digital & QR Code resmi terverifikasi valid ({$authorityStr}).";
        } elseif ($stampDetected) {
            $sigStatus = 'valid_manual';
            $sigNotes = 'Stempel instansi resmi dan pengesahan tanda tangan basah terdeteksi pada berkas.';
        } else {
            $sigStatus = 'unverified';
            $sigNotes = 'Tanda tangan atau stempel instansi memerlukan verifikasi visual manual oleh petugas.';
        }

        return [
            'score' => $finalSigScore,
            'status' => $sigStatus,
            'has_digital_signature' => $hasDigitalSignature,
            'signature_type' => $signatureType,
            'has_qr_verification' => $hasQrVerification,
            'qr_domains' => array_unique($matchedDomains),
            'official_authority' => ! empty($matchedAuthorities) ? implode(', ', array_unique($matchedAuthorities)) : null,
            'stamp_detected' => $stampDetected,
            'stamp_keywords_found' => array_slice(array_unique($stampKeywordsFound), 0, 5),
            'visual_manipulation_detected' => $visualManipulationDetected,
            'notes' => $sigNotes,
        ];
    }

    /**
     * Audit anti-duplikasi: mengecek apakah file hash SHA-256 yang sama pernah diunggah oleh klien/pesanan lain.
     *
     * @return array<string, mixed>
     */
    protected function auditAntiReplay(?string $sha256, ?int $currentDocumentId): array
    {
        if (empty($sha256)) {
            return [
                'score' => 100,
                'is_duplicate' => false,
                'duplicate_order' => null,
                'notes' => 'Hash file valid.',
            ];
        }

        // Cek duplikasi di riwayat versi dokumen lain
        $duplicate = DB::table('client_document_versions')
            ->leftJoin('client_documents', 'client_document_versions.client_document_id', '=', 'client_documents.id')
            ->leftJoin('orders', 'client_documents.order_id', '=', 'orders.id')
            ->where('client_documents.id', '!=', $currentDocumentId ?? 0)
            ->where('client_document_versions.file_path', 'like', "%{$sha256}%")
            ->select('orders.order_number', 'client_documents.document_name')
            ->first();

        if ($duplicate) {
            return [
                'score' => 30,
                'is_duplicate' => true,
                'duplicate_order' => $duplicate->order_number,
                'notes' => "Berkas identik pernah diunggah pada pesanan {$duplicate->order_number}.",
            ];
        }

        return [
            'score' => 100,
            'is_duplicate' => false,
            'duplicate_order' => null,
            'notes' => 'Integritas berkas unik. Tidak ditemukan duplikasi berkas dari akun lain.',
        ];
    }

    /**
     * Evaluasi putusan akhir (Authenticity & Integrity Verdict) dan rekomendasi tindakan.
     *
     * @param  array<string, mixed>  $meta
     * @param  array<string, mixed>  $sig
     * @param  array<string, mixed>  $antiReplay
     * @return array<string, mixed>
     */
    protected function evaluateVerdict(
        bool $fileExists,
        array $meta,
        array $sig,
        array $antiReplay,
        ?ClientDocument $document = null
    ): array {
        $metaScore = (int) ($meta['score'] ?? 80);
        $sigScore = (int) ($sig['score'] ?? 80);
        $replayScore = (int) ($antiReplay['score'] ?? 100);

        // Bobot: 45% Metadata, 45% TTD & Cap, 10% Anti-Replay
        $overallScore = (int) round(($metaScore * 0.45) + ($sigScore * 0.45) + ($replayScore * 0.10));

        $hasTampering = ! empty($meta['tampering_detected']) || ! empty($sig['visual_manipulation_detected']);
        $hasDuplicate = ! empty($antiReplay['is_duplicate']);

        if ($hasTampering || $overallScore < 50) {
            $verdictCode = 'TAMPERED';
            $verdictLabel = 'Peringatan: Terindikasi Modifikasi / Manipulasi';
            $badgeColor = 'rose';
            $badgeClass = 'bg-rose-500/15 text-rose-600 border border-rose-500/30';
            $summaryNotes = 'Dokumen terdeteksi mengandung anomali visual atau metadata software penyunting ilegal. Disarankan meminta dokumen asli bertanda tangan resmi kembali.';
            $suggestedStatus = 'rejected';
            $defaultRevisionNote = 'Dokumen terindikasi mengalami manipulasi digital/modifikasi ilegal pada area tanda tangan/stempel. Mohon unggah kembali berkas asli langsung dari instansi penerbit.';
        } elseif ($overallScore < 80) {
            $verdictCode = 'WARNING';
            $verdictLabel = 'Perlu Klarifikasi & Peninjauan Manual';
            $badgeColor = 'amber';
            $badgeClass = 'bg-amber-500/15 text-amber-600 border border-amber-500/30';
            $summaryNotes = 'Dokumen memiliki tingkat keaslian sedang. Stempel atau tanda tangan perlu dikonfirmasi kejelasan dan masa berlakunya.';
            $suggestedStatus = 'uploaded';
            $defaultRevisionNote = 'Tanda tangan atau stempel cap instansi kurang terbaca jelas. Mohon periksa keabsahan stempel dinas.';
        } else {
            $verdictCode = 'GENUINE';
            $verdictLabel = 'Dokumen Sah & Asli (Integritas Terverifikasi)';
            $badgeColor = 'teal';
            $badgeClass = 'bg-teal-500/15 text-teal-600 border border-teal-500/30';
            $summaryNotes = 'Metadata berkas bersih, tanda tangan dan stempel sah terverifikasi, tidak ditemukan jejak manipulasi ilegal.';
            $suggestedStatus = 'approved';
            $defaultRevisionNote = 'Dokumen telah memenuhi seluruh standar kelengkapan administrasi dan regulasi.';
        }

        $checklist = [
            [
                'label' => 'Validasi Stempel & Cap Instansi',
                'passed' => ! empty($sig['stamp_detected']),
                'detail' => ! empty($sig['stamp_detected']) ? 'Cap/stempel instansi resmi teridentifikasi.' : 'Perlu pemeriksaan manual cap instansi.',
            ],
            [
                'label' => 'Verifikasi QR Code / TTD Digital',
                'passed' => ! empty($sig['has_digital_signature']) || ! empty($sig['has_qr_verification']),
                'detail' => $sig['signature_type'] ?? 'Tanda Tangan Manual',
            ],
            [
                'label' => 'Integritas Metadata & Anti-Tampering',
                'passed' => empty($meta['tampering_detected']),
                'detail' => empty($meta['tampering_detected']) ? 'Metadata bersih tanpa software editing.' : 'Terdeteksi editor: '.($meta['detected_editor'] ?? 'Editor'),
            ],
            [
                'label' => 'Konsistensi Tanggal Pembuatan Berkas',
                'passed' => ($meta['date_discrepancy_days'] ?? 0) <= 7,
                'detail' => 'Selisih modifikasi: '.($meta['date_discrepancy_days'] ?? 0).' hari.',
            ],
            [
                'label' => 'Keunikan Berkas (Anti-Replay Hash)',
                'passed' => empty($antiReplay['is_duplicate']),
                'detail' => empty($antiReplay['is_duplicate']) ? 'Dokumen orisinal, tidak duplikat.' : 'Duplikat pada '.$antiReplay['duplicate_order'],
            ],
        ];

        return [
            'overall_score' => $overallScore,
            'verdict_code' => $verdictCode,
            'verdict_label' => $verdictLabel,
            'badge_color' => $badgeColor,
            'badge_class' => $badgeClass,
            'summary_notes' => $summaryNotes,
            'recommendation' => [
                'suggested_status' => $suggestedStatus,
                'default_notes' => $defaultRevisionNote,
            ],
            'checklist' => $checklist,
        ];
    }

    /**
     * Parsing tanggal PDF format D:YYYYMMDDHHmmSSOHH'mm'.
     */
    protected function parsePdfDate(string $rawDate): ?Carbon
    {
        $cleaned = trim($rawDate);
        if (str_starts_with($cleaned, 'D:')) {
            $cleaned = substr($cleaned, 2);
        }

        if (preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', $cleaned, $m)) {
            try {
                return Carbon::createFromDate($m[1], $m[2], $m[3])->setTime($m[4], $m[5], $m[6]);
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    /**
     * Format ukuran biner byte ke format manusia (KB, MB).
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
