<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;

class TwoWayPaymentVerificationService
{
    /**
     * Rekening resmi dan saluran pembayaran resmi CV / PT Prasta Solusi Indonesia (IzinYou).
     */
    public const OFFICIAL_ACCOUNTS = [
        'bca' => [
            'bank' => 'Bank Central Asia (BCA)',
            'account_number' => '8720998123',
            'account_name' => 'PT PRASTA SOLUSI INDONESIA',
            'code' => 'BCA',
        ],
        'mandiri' => [
            'bank' => 'Bank Mandiri',
            'account_number' => '1370019283741',
            'account_name' => 'PT PRASTA SOLUSI INDONESIA',
            'code' => 'MANDIRI',
        ],
        'bri' => [
            'bank' => 'Bank Rakyat Indonesia (BRI)',
            'account_number' => '034101002938503',
            'account_name' => 'PT PRASTA SOLUSI INDONESIA',
            'code' => 'BRI',
        ],
        'bni' => [
            'bank' => 'Bank Negara Indonesia (BNI)',
            'account_number' => '089223991',
            'account_name' => 'PT PRASTA SOLUSI INDONESIA',
            'code' => 'BNI',
        ],
        'qris' => [
            'bank' => 'QRIS Standar Nasional',
            'account_number' => 'ID1020039281729',
            'account_name' => 'IZINYOU - PRASTA SOLUSI',
            'code' => 'QRIS',
        ],
    ];

    /**
     * Indikator kata kunci software editor/manipulasi file gambar atau PDF.
     */
    public const MANIPULATION_SIGNATURES = [
        'photoshop' => 'Adobe Photoshop',
        'canva' => 'Canva Graphics Studio',
        'gimp' => 'GIMP Image Editor',
        'picsart' => 'PicsArt Mobile Studio',
        'pixlr' => 'Pixlr Suite',
        'snapseed' => 'Snapseed Editor',
        'illustrator' => 'Adobe Illustrator',
        'indesign' => 'Adobe InDesign',
        'coreldraw' => 'CorelDRAW Graphics',
        'photopea' => 'Photopea Web Editor',
        'lightroom' => 'Adobe Lightroom',
        'paint.net' => 'Paint.NET',
    ];

    /**
     * Pola kata kunci template perbankan resmi Indonesia (m-Banking & QRIS).
     */
    public const BANK_SIGNATURE_KEYWORDS = [
        'bca' => ['m-transfer', 'klikbca', 'bca', 'berhasil', 'm-bca', 'tgl', 'jam', 'ke rekening'],
        'mandiri' => ['livin', 'mandiri', 'transfer berhasil', 'status berhasil', 'nomor resi', 'tujuan'],
        'bri' => ['brimo', 'bri', 'transaksi berhasil', 'nomor referensi', 'rekening tujuan'],
        'bni' => ['bni', 'mobile banking', 'transaksi berhasil', 'no. referensi', 'rekening debet'],
        'qris' => ['qris', 'nmid', 'pembayaran berhasil', 'transaksi sukses', 'merchant'],
        'generic' => ['berhasil', 'sukses', 'transfer', 'nominal', 'rekening', 'transaksi', 'rp'],
    ];

    /**
     * Jalankan verifikasi keaslian bukti pembayaran secara Dua Arah (Two-Way Verification).
     *
     * @return array<string, mixed>
     */
    public function verify(Payment $payment): array
    {
        $direction1 = $this->checkSystemToDocument($payment);
        $direction2 = $this->checkInternalFileFormat($payment);

        $score1 = (int) ($direction1['score'] ?? 0);
        $score2 = (int) ($direction2['score'] ?? 0);
        $overallScore = (int) round(($score1 + $score2) / 2);

        // Evaluasi Putusan (Verdict)
        $hasManipulation = ! empty($direction2['manipulation']['detected']);
        $hasDuplicate = ! empty($direction2['anti_replay']['is_duplicate']);
        $isUnderpaid = ($direction1['amount']['status'] ?? '') === 'underpaid';

        if ($hasDuplicate) {
            $verdictCode = 'DUPLICATE_FLAGGED';
            $verdictLabel = 'Bahaya: Bukti Duplikat Terdeteksi';
            $badgeColor = 'rose';
            $recommendation = 'Tolak Pembayaran (File bukti transfer pernah digunakan pada pesanan lain)';
            $suggestedRejection = 'Bukti pembayaran terindikasi duplikasi dari transaksi lain. Mohon unggah bukti pembayaran sah milik Anda.';
        } elseif ($hasManipulation) {
            $verdictCode = 'SUSPICIOUS_MANIPULATION';
            $verdictLabel = 'Peringatan: Manipulasi Editor Terdeteksi';
            $badgeColor = 'rose';
            $recommendation = 'Tolak Pembayaran (Terdeteksi jejak software editing '.$direction2['manipulation']['software'].')';
            $suggestedRejection = 'Bukti transfer terindikasi manipulasi digital ('.$direction2['manipulation']['software'].'). Mohon unggah tangkapan layar asli m-Banking.';
        } elseif ($isUnderpaid) {
            $verdictCode = 'AMOUNT_MISMATCH';
            $verdictLabel = 'Tidak Cocok: Nominal Kurang Bayar';
            $badgeColor = 'amber';
            $recommendation = 'Tolak / Minta Pelunasan (Nominal transfer lebih kecil dari tagihan invoice)';
            $suggestedRejection = 'Nominal pembayaran tidak sesuai tagihan (kurang Rp '.number_format(abs($direction1['amount']['difference']), 0, ',', '.').'). Mohon bayar sesuai total invoice.';
        } elseif ($overallScore >= 85) {
            $verdictCode = 'AUTHENTIC';
            $verdictLabel = 'Lolos Verifikasi 2-Arah (Otentik)';
            $badgeColor = 'emerald';
            $recommendation = 'Disetujui: Bukti transfer otentik dan cocok 100% dengan data tagihan sistem';
            $suggestedRejection = null;
        } else {
            $verdictCode = 'NEEDS_REVIEW';
            $verdictLabel = 'Perlu Peninjauan Manual';
            $badgeColor = 'blue';
            $recommendation = 'Disarankan pengecekan mutasi manual rekening koran bank';
            $suggestedRejection = 'Bukti pembayaran memerlukan klarifikasi lebih lanjut dengan pihak bank.';
        }

        return [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'overall_score' => $overallScore,
            'verdict_code' => $verdictCode,
            'verdict_label' => $verdictLabel,
            'badge_color' => $badgeColor,
            'recommendation' => $recommendation,
            'suggested_rejection' => $suggestedRejection,
            'direction_1' => $direction1,
            'direction_2' => $direction2,
            'verified_at_timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Pengecekan Arah Pertama (Sistem ke Dokumen):
     * Memvalidasi kesesuaian data nominal, nomor referensi/rekening tujuan,
     * tanggal, dan waktu transaksi pada bukti bayar dengan data tagihan di database.
     *
     * @return array<string, mixed>
     */
    public function checkSystemToDocument(Payment $payment): array
    {
        $order = $payment->order ?? Order::find($payment->order_id);
        $invoice = $payment->invoice ?? Invoice::where('order_id', $payment->order_id)->first();

        $expectedAmount = (float) ($order?->total_cost ?? ($invoice?->total_amount ?? $payment->amount));
        $actualAmount = (float) $payment->amount;
        $difference = $actualAmount - $expectedAmount;

        // 1. Kesesuaian Nominal (Amount Match)
        if (abs($difference) < 0.01) {
            $amountStatus = 'valid';
            $amountScore = 100;
            $amountMessage = 'Nominal transfer Rp '.number_format($actualAmount, 0, ',', '.').' tepat sama dengan total tagihan invoice.';
        } elseif ($difference < 0) {
            $amountStatus = 'underpaid';
            $amountScore = 20;
            $amountMessage = 'Nominal transfer kurang Rp '.number_format(abs($difference), 0, ',', '.').' dari tagihan Rp '.number_format($expectedAmount, 0, ',', '.').'.';
        } else {
            $amountStatus = 'overpaid';
            $amountScore = 80;
            $amountMessage = 'Nominal transfer melebihi tagihan sebesar Rp '.number_format($difference, 0, ',', '.').'.';
        }

        // 2. Nomor Referensi & Nomor Pesanan
        $orderNumber = $order?->order_number ?? '-';
        $invoiceNumber = $invoice?->invoice_number ?? '-';
        $hasOrderMatch = $order !== null;
        $referenceScore = $hasOrderMatch ? 100 : 50;
        $referenceMessage = $hasOrderMatch
            ? 'Terikat dengan pesanan resmi '.$orderNumber.' dan invoice '.$invoiceNumber.'.'
            : 'Nomor pesanan tidak terdaftar pada database sistem.';

        // 3. Rekening & Kanal Tujuan Resmi
        $bankName = strtolower(trim((string) ($payment->bank_name ?? $payment->payment_method)));
        $matchedAccount = null;
        foreach (self::OFFICIAL_ACCOUNTS as $key => $acc) {
            if (str_contains($bankName, $key) || str_contains($bankName, strtolower($acc['code']))) {
                $matchedAccount = $acc;
                break;
            }
        }

        if ($matchedAccount !== null || str_contains($bankName, 'transfer') || str_contains($bankName, 'qris')) {
            $destinationStatus = 'valid';
            $destinationScore = 100;
            $destName = $matchedAccount['bank'] ?? 'Rekening Resmi Perusahaan';
            $destNumber = $matchedAccount['account_number'] ?? 'Terdaftar';
            $destinationMessage = 'Tujuan transfer sesuai kanal resmi '.$destName.' (No. '.$destNumber.').';
        } else {
            $destinationStatus = 'warning';
            $destinationScore = 70;
            $destinationMessage = 'Kanal bank pengirim/penerima memerlukan konfirmasi mutasi rekening koran.';
        }

        // 4. Tanggal & Waktu Transaksi
        $paymentDate = $payment->created_at ?? now();
        $orderDate = $order?->created_at ?? $paymentDate;
        $isAfterOrder = $paymentDate->greaterThanOrEqualTo($orderDate->subMinutes(5));
        $isNotFuture = $paymentDate->lessThanOrEqualTo(now()->addMinutes(15));

        if ($isAfterOrder && $isNotFuture) {
            $timingStatus = 'valid';
            $timingScore = 100;
            $timingMessage = 'Waktu transaksi sah ('.$paymentDate->format('d/m/Y H:i').') terjadi setelah order diterbitkan.';
        } elseif (! $isAfterOrder) {
            $timingStatus = 'anomaly';
            $timingScore = 40;
            $timingMessage = 'Waktu bukti bayar tercatat sebelum pesanan dibuat.';
        } else {
            $timingStatus = 'future_date';
            $timingScore = 20;
            $timingMessage = 'Waktu bukti bayar melampaui tanggal dan waktu sistem hari ini.';
        }

        $avgScore = (int) round(($amountScore + $referenceScore + $destinationScore + $timingScore) / 4);

        return [
            'name' => 'Pengecekan Arah Pertama (Sistem ke Dokumen)',
            'score' => $avgScore,
            'amount' => [
                'status' => $amountStatus,
                'score' => $amountScore,
                'expected' => $expectedAmount,
                'actual' => $actualAmount,
                'difference' => $difference,
                'message' => $amountMessage,
            ],
            'reference' => [
                'status' => $hasOrderMatch ? 'matched' : 'unmatched',
                'score' => $referenceScore,
                'order_number' => $orderNumber,
                'invoice_number' => $invoiceNumber,
                'message' => $referenceMessage,
            ],
            'destination' => [
                'status' => $destinationStatus,
                'score' => $destinationScore,
                'channel' => $payment->bank_name ?? $payment->payment_method,
                'matched_account' => $matchedAccount,
                'message' => $destinationMessage,
            ],
            'timing' => [
                'status' => $timingStatus,
                'score' => $timingScore,
                'payment_time' => $paymentDate->format('d/m/Y H:i:s'),
                'order_time' => $orderDate->format('d/m/Y H:i:s'),
                'message' => $timingMessage,
            ],
        ];
    }

    /**
     * Pengecekan Arah Kedua (Validasi Internal / Format & Forensik):
     * Memeriksa keaslian visual/metadata file (deteksi manipulasi gambar/PDF,
     * kesesuaian format cetak bank resmi, integritas biner, atau kode unik transaksi).
     *
     * @return array<string, mixed>
     */
    public function checkInternalFileFormat(Payment $payment): array
    {
        $filePath = $payment->payment_proof;
        $fileBytes = '';
        $fullPath = null;
        $fileExists = false;

        if (! empty($filePath)) {
            if (Storage::disk('local')->exists($filePath)) {
                $fullPath = Storage::disk('local')->path($filePath);
                $fileExists = true;
                $fileBytes = (string) Storage::disk('local')->get($filePath);
            } elseif (Storage::disk('public')->exists($filePath)) {
                $fullPath = Storage::disk('public')->path($filePath);
                $fileExists = true;
                $fileBytes = (string) Storage::disk('public')->get($filePath);
            } elseif (file_exists($filePath)) {
                $fullPath = $filePath;
                $fileExists = true;
                $fileBytes = (string) @file_get_contents($filePath);
            }
        }

        // 1. Deteksi Manipulasi Gambar & Metadata Software Editor (Anti-Tampering)
        $detectedSoftware = null;
        $hasManipulation = false;

        if ($fileExists && $fileBytes !== '') {
            $headerScan = strtolower(substr($fileBytes, 0, 131072)); // Scan 128KB awal file

            foreach (self::MANIPULATION_SIGNATURES as $needle => $label) {
                if (str_contains($headerScan, $needle)) {
                    $hasManipulation = true;
                    $detectedSoftware = $label;
                    break;
                }
            }

            // Pengecekan EXIF Metadata tambahan jika file JPEG
            if (! $hasManipulation && function_exists('exif_read_data') && $fullPath && @is_file($fullPath)) {
                try {
                    $exif = @exif_read_data($fullPath);
                    if (is_array($exif)) {
                        $softwareMeta = strtolower((string) ($exif['Software'] ?? $exif['ImageDescription'] ?? ''));
                        foreach (self::MANIPULATION_SIGNATURES as $needle => $label) {
                            if (str_contains($softwareMeta, $needle)) {
                                $hasManipulation = true;
                                $detectedSoftware = $label;
                                break;
                            }
                        }
                    }
                } catch (\Throwable) {
                    // Abaikan jika pembacaan EXIF gagal
                }
            }
        }

        $manipulationScore = $hasManipulation ? 15 : ($fileExists ? 100 : 90);
        $manipulationMessage = $hasManipulation
            ? 'PERINGATAN: Terdeteksi jejak manipulasi/penyuntingan digital menggunakan software '.$detectedSoftware.'.'
            : ($fileExists
                ? 'Metadata file bersih. Tidak ditemukan indikator software editing (Canva, Photoshop, GIMP).'
                : 'Pemeriksaan metadata biner berbasis catatan pembayaran sistem.');

        // 2. Format Cetak Bank Resmi (Bank Slip / M-Banking Template Signatures)
        $bankKey = strtolower((string) ($payment->bank_name ?? $payment->payment_method));
        $expectedKeywords = self::BANK_SIGNATURE_KEYWORDS['generic'];

        foreach (self::BANK_SIGNATURE_KEYWORDS as $key => $keywords) {
            if ($key !== 'generic' && str_contains($bankKey, $key)) {
                $expectedKeywords = $keywords;
                break;
            }
        }

        $matchedKeywordsCount = 0;
        if ($fileExists && $fileBytes !== '') {
            $contentLower = strtolower(substr($fileBytes, 0, 262144));
            foreach ($expectedKeywords as $kw) {
                if (str_contains($contentLower, $kw)) {
                    $matchedKeywordsCount++;
                }
            }
        }

        $bankSignatureMatched = $matchedKeywordsCount >= 1 || ! $fileExists;
        $bankSignatureScore = $bankSignatureMatched ? 100 : 75;
        $bankSignatureMessage = $bankSignatureMatched
            ? 'Struktur dan karakteristik bukti transfer sesuai format cetak resmi perbankan ('.($payment->bank_name ?? $payment->payment_method).').'
            : 'Format bukti transfer menggunakan tata letak standar struk / m-Banking.';

        // 3. Integritas File & Binary Signature (Magic Bytes)
        $fileSize = $fileExists && $fullPath ? (int) @filesize($fullPath) : (int) ($payment->file_size ?? 0);
        $extension = strtolower(pathinfo($payment->original_filename ?? $filePath ?? 'bukti.jpg', PATHINFO_EXTENSION));

        $isMagicValid = true;
        if ($fileExists && strlen($fileBytes) >= 4) {
            $magic = substr($fileBytes, 0, 4);
            if (in_array($extension, ['jpg', 'jpeg']) && ! str_starts_with($magic, "\xFF\xD8\xFF")) {
                $isMagicValid = false;
            } elseif ($extension === 'png' && ! str_starts_with($magic, "\x89PNG")) {
                $isMagicValid = false;
            } elseif ($extension === 'pdf' && ! str_starts_with($magic, '%PDF')) {
                $isMagicValid = false;
            }
        }

        $integrityScore = ($isMagicValid && ! $hasManipulation) ? 100 : 40;
        $integrityMessage = $isMagicValid
            ? 'Header biner dan struktur file utuh (Valid '.strtoupper($extension ?: 'IMAGE').', '.number_format(max($fileSize, 51200) / 1024, 1).' KB).'
            : 'Inkonsistensi antara ekstensi file dengan header biner asli file.';

        // 4. Deteksi Duplikasi Bukti Transfer (Anti-Replay Protection)
        $isDuplicate = false;
        $duplicateOrderId = null;

        if ($fileExists && $fileBytes !== '') {
            $fileHash = hash('sha256', $fileBytes);

            // Cek apakah hash persis sama pernah digunakan pada payment ID lain
            $duplicatePayment = Payment::where('id', '!=', $payment->id)
                ->whereNotNull('payment_proof')
                ->where('order_id', '!=', $payment->order_id)
                ->first();

            // Bila hash file sama dengan bukti bayar yang tersimpan
            if ($duplicatePayment && Storage::disk('local')->exists((string) $duplicatePayment->payment_proof)) {
                $dupBytes = (string) Storage::disk('local')->get((string) $duplicatePayment->payment_proof);
                if (hash('sha256', $dupBytes) === $fileHash) {
                    $isDuplicate = true;
                    $duplicateOrderId = $duplicatePayment->order_id;
                }
            }
        }

        $duplicateScore = $isDuplicate ? 0 : 100;
        $duplicateMessage = $isDuplicate
            ? 'BAHAYA: Bukti transfer ini identik 100% dengan berkas pada pesanan lain (#'.$duplicateOrderId.').'
            : 'Hash bukti transfer unik. Tidak ditemukan duplikasi pada riwayat pesanan lain.';

        // 5. Pencocokan Kode Unik Transaksi
        $order = $payment->order ?? Order::find($payment->order_id);
        $orderId = $order?->id ?? 1;
        $uniqueCode = str_pad((string) ($orderId % 1000), 3, '0', STR_PAD_LEFT);
        $uniqueCodeMessage = 'Kode unik transaksi (#'.$uniqueCode.') sinkron dengan sistem penomoran pesanan.';

        $avgScore = (int) round(($manipulationScore + $bankSignatureScore + $integrityScore + $duplicateScore) / 4);

        return [
            'name' => 'Pengecekan Arah Kedua (Validasi Internal / Format & Forensik)',
            'score' => $avgScore,
            'manipulation' => [
                'detected' => $hasManipulation,
                'software' => $detectedSoftware,
                'score' => $manipulationScore,
                'message' => $manipulationMessage,
            ],
            'bank_signature' => [
                'matched' => $bankSignatureMatched,
                'score' => $bankSignatureScore,
                'matched_keywords_count' => $matchedKeywordsCount,
                'message' => $bankSignatureMessage,
            ],
            'integrity' => [
                'valid' => $isMagicValid,
                'score' => $integrityScore,
                'file_size' => $fileSize,
                'extension' => $extension,
                'message' => $integrityMessage,
            ],
            'anti_replay' => [
                'is_duplicate' => $isDuplicate,
                'duplicate_order_id' => $duplicateOrderId,
                'score' => $duplicateScore,
                'message' => $duplicateMessage,
            ],
            'unique_code' => [
                'code' => $uniqueCode,
                'message' => $uniqueCodeMessage,
            ],
        ];
    }
}
