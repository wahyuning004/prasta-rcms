<?php

namespace App\Services;

use App\Models\ClientDocument;
use App\Models\ClientDocumentVersion;
use App\Models\Order;
use App\Models\ServiceRequirement;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SecureDocumentStorageService
{
    /**
     * Disk non-publik utama untuk dokumen persyaratan legalitas dan teknis pelanggan.
     * Disk 'local' terkonfigurasi pada 'storage/app/private', tidak dapat diakses langsung oleh web server.
     */
    public const NON_PUBLIC_DISK = 'local';

    /**
     * Daftar ekstensi berkas yang diperbolehkan untuk persyaratan perizinan.
     */
    public const ALLOWED_EXTENSIONS = [
        'pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx',
    ];

    /**
     * Header keamanan HTTP wajib saat menyajikan dokumen dari penyimpanan non-publik.
     */
    public const SECURITY_HEADERS = [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-XSS-Protection' => '1; mode=block',
        'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ];

    /**
     * Dapatkan instance storage disk non-publik.
     */
    public function getDisk(): Filesystem
    {
        return Storage::disk(self::NON_PUBLIC_DISK);
    }

    /**
     * Sanitasi path relatif berkas untuk mencegah serangan Directory Traversal (../ atau null bytes).
     *
     * @throws InvalidArgumentException
     */
    public function sanitizePath(string $path): string
    {
        $normalized = str_replace('\\', '/', trim($path));

        // Cegah Null-byte poisoning
        if (str_contains($normalized, "\0")) {
            throw new InvalidArgumentException('Terdeteksi karakter null-byte ilegal pada path berkas.');
        }

        // Cegah Directory Traversal (../ atau ..\)
        if (preg_match('#(?:^|/)\.\.(?:/|$)#', $normalized)) {
            throw new InvalidArgumentException('Akses ditolak: Terdeteksi pola traversal direktori (..) pada path berkas.');
        }

        return ltrim($normalized, '/');
    }

    /**
     * Sanitasi nama berkas untuk header Content-Disposition (mencegah HTTP Header Injection).
     */
    public function sanitizeFilename(string $filename): string
    {
        $clean = preg_replace('/[\r\n\t]+/', ' ', $filename);
        $clean = str_replace(['"', "'", '/', '\\'], '_', (string) $clean);
        $clean = trim((string) $clean);

        return $clean !== '' ? $clean : 'dokumen_legalitas.bin';
    }

    /**
     * Simpan berkas dokumen persyaratan pelanggan ke direktori penyimpanan non-publik.
     *
     * @return array{
     *     stored_path: string,
     *     original_filename: string,
     *     file_size: string,
     *     file_size_bytes: int,
     *     mime_type: string,
     *     sha256: string
     * }
     */
    public function storeUploadedFile(UploadedFile $file, Order $order, ServiceRequirement|int $requirement): array
    {
        $reqId = $requirement instanceof ServiceRequirement ? $requirement->id : (int) $requirement;
        $extension = strtolower((string) ($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin'));

        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new InvalidArgumentException("Ekstensi file [{$extension}] tidak diizinkan untuk dokumen persyaratan legalitas.");
        }

        $detectedMime = (string) ($file->getMimeType() ?: 'application/octet-stream');

        // Validasi konten biner berbahaya untuk mencegah penyamaran skrip (.php, .sh, .exe)
        $realPath = $file->getRealPath();
        if ($realPath && is_readable($realPath)) {
            $magic = (string) @file_get_contents($realPath, false, null, 0, 32);
            if (str_starts_with($magic, '<?php') || str_starts_with($magic, '#!/bin/') || str_starts_with($magic, 'MZ')) {
                throw new InvalidArgumentException('Konten file terdeteksi mengandung format eksekusi biner terlarang.');
            }
        }

        // Direktori terisolasi non-publik per pesanan & persyaratan
        $safeDir = "client_documents/{$order->id}/req_{$reqId}";
        $randomHash = Str::random(32).'.'.$extension;
        $targetRelativePath = "{$safeDir}/{$randomHash}";

        // Simpan langsung ke disk non-publik
        $fileContents = (string) file_get_contents($realPath ?: $file->path());
        $this->getDisk()->put($targetRelativePath, $fileContents);

        $sizeBytes = strlen($fileContents);
        $sha256 = hash('sha256', $fileContents);

        return [
            'stored_path' => $targetRelativePath,
            'original_filename' => $this->sanitizeFilename($file->getClientOriginalName()),
            'file_size' => $this->formatBytes($sizeBytes),
            'file_size_bytes' => $sizeBytes,
            'mime_type' => $detectedMime,
            'sha256' => $sha256,
        ];
    }

    /**
     * Otorisasi Role-Based Access Control (RBAC) ketat untuk dokumen legalitas dan persyaratan teknis.
     *
     * Berwenang:
     * - Document & Licensing Officer (officer, petugas)
     * - Administrator / Direktur (admin, direktur)
     * - Legal & Regulatory Specialist (legal, konsultan)
     * - Finance Staff (finance)
     * - Klien pemilik sah pesanan (order->user_id === user->id)
     *
     * Dilarang:
     * - Klien pesanan lain (IDOR defense)
     * - Staf Marketing
     * - Pengguna tanpa login (guest)
     */
    public function canAccess(?User $user, ClientDocument|ClientDocumentVersion $documentOrVersion): bool
    {
        if (! $user) {
            return false;
        }

        $order = $documentOrVersion instanceof ClientDocumentVersion
            ? $documentOrVersion->document?->order
            : $documentOrVersion->order;

        if (! $order && $documentOrVersion instanceof ClientDocumentVersion) {
            $documentOrVersion->load('document.order');
            $order = $documentOrVersion->document?->order;
        } elseif (! $order && $documentOrVersion instanceof ClientDocument) {
            $documentOrVersion->load('order');
            $order = $documentOrVersion->order;
        }

        // 1. Role Petugas Operasional & Pengawas Internal Berwenang
        if ($user->isOfficer() || $user->isAdmin() || $user->isLegal() || $user->isFinance()) {
            return true;
        }

        // 2. Klien Pemilik Dokumen yang Sah
        if ($order && (int) $order->user_id === (int) $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Pastikan pengguna memiliki izin akses sah, jika tidak batalkan dengan kode HTTP 403 Forbidden.
     *
     * @throws HttpException
     */
    public function authorize(?User $user, ClientDocument|ClientDocumentVersion $documentOrVersion): void
    {
        if (! $this->canAccess($user, $documentOrVersion)) {
            abort(403, 'Akses ditolak (RBAC). Dokumen legalitas dan berkas teknis ini tersimpan dalam penyimpanan server terbatas (non-public vault) dan hanya dapat diakses oleh petugas perizinan yang sah atau pemilik pesanan.');
        }
    }

    /**
     * Sajikan unduhan berkas dokumen aktif secara aman dari penyimpanan non-publik.
     */
    public function serveDocumentDownload(
        ClientDocument $document,
        ?User $user = null,
        bool $asAttachment = true
    ): BinaryFileResponse {
        $this->authorize($user, $document);

        $path = (string) $document->file_path;
        $fallbackName = $document->document_name
            ? $document->document_name.'.'.pathinfo($path, PATHINFO_EXTENSION)
            : basename($path);
        $downloadName = $document->original_filename ?: $fallbackName;

        return $this->deliverFile($path, $downloadName, $asAttachment, $document->id, $user, (int) ($document->version ?? 1));
    }

    /**
     * Sajikan unduhan versi spesifik dokumen secara aman dari penyimpanan non-publik.
     */
    public function serveVersionDownload(
        ClientDocumentVersion $version,
        ?User $user = null,
        bool $asAttachment = true
    ): BinaryFileResponse {
        $this->authorize($user, $version);

        $path = (string) $version->file_path;
        $fallbackName = 'v'.$version->version_number.'_'.basename($path);
        $downloadName = $version->original_filename ?: $fallbackName;

        return $this->deliverFile($path, $downloadName, $asAttachment, $version->client_document_id, $user, (int) $version->version_number);
    }

    /**
     * Resolusi fisik file dari disk non-publik dan pengiriman respons dengan security headers.
     */
    public function deliverFile(
        string $rawPath,
        string $downloadName,
        bool $asAttachment = true,
        ?int $documentId = null,
        ?User $user = null,
        int $versionNumber = 1
    ): BinaryFileResponse {
        $cleanPath = $this->sanitizePath($rawPath);
        $disk = $this->getDisk();

        $physicalPath = null;

        // 1. Cek pada non-public disk (storage/app/private via disk local)
        if ($disk->exists($cleanPath)) {
            $physicalPath = $disk->path($cleanPath);
        }
        // 2. Relokasi otomatis jika berkas lama tersimpan di public disk agar tidak bocor
        elseif (Storage::disk('public')->exists($cleanPath)) {
            try {
                $content = Storage::disk('public')->get($cleanPath);
                $disk->put($cleanPath, $content);
                Storage::disk('public')->delete($cleanPath);
                $physicalPath = $disk->path($cleanPath);
            } catch (\Throwable) {
                $physicalPath = Storage::disk('public')->path($cleanPath);
            }
        } elseif (@file_exists($rawPath)) {
            $physicalPath = $rawPath;
        }

        if (! $physicalPath || ! @is_file($physicalPath) || ! @is_readable($physicalPath)) {
            abort(404, 'Berkas dokumen legalitas tidak ditemukan di penyimpanan server non-publik.');
        }

        $safeDownloadName = $this->sanitizeFilename($downloadName);

        // Pencatatan jejak audit (Audit Log) untuk kepatuhan regulasi keamanan berkas perizinan
        if ($user) {
            Log::info('Audit Akses Dokumen Non-Publik', [
                'document_id' => $documentId,
                'version' => $versionNumber,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role,
                'file_name' => $safeDownloadName,
                'mode' => $asAttachment ? 'attachment_download' : 'inline_view',
                'ip_address' => request()->ip(),
            ]);
        }

        $headers = self::SECURITY_HEADERS;

        if ($asAttachment) {
            return response()->download($physicalPath, $safeDownloadName, $headers);
        }

        return response()->file($physicalPath, $headers);
    }

    /**
     * Format ukuran biner.
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }

        return number_format(max(1, $bytes / 1024), 1).' KB';
    }
}
