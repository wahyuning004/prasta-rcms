<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceRequirement;
use Illuminate\Support\Collection;

class ServiceRequirementSyncService
{
    /**
     * Pastikan setiap layanan memiliki daftar dokumen wajib dan opsional pada database.
     *
     * @return Collection<int, ServiceRequirement>
     */
    public function ensureForService(Service $service): Collection
    {
        $existing = ServiceRequirement::where('service_id', $service->id)
            ->orderByDesc('is_mandatory')
            ->orderBy('id')
            ->get();

        if ($existing->isEmpty()) {
            // 1. Dokumen Legalitas Wajib Dasar
            $legalDefaults = [
                [
                    'document_name' => 'Nomor Induk Berusaha (NIB) OSS-RBA',
                    'document_type' => 'Dokumen Legalitas',
                    'is_mandatory' => true,
                    'description' => 'NIB berbasis risiko dengan KBLI yang sesuai ruang lingkup layanan.',
                ],
                [
                    'document_name' => 'Akta Pendirian & Perubahan Perusahaan (SK Kemenkumham)',
                    'document_type' => 'Dokumen Legalitas',
                    'is_mandatory' => true,
                    'description' => 'Salinan akta notaris beserta pengesahan Kemenkumham terbaru.',
                ],
            ];

            foreach ($legalDefaults as $def) {
                ServiceRequirement::create(array_merge(['service_id' => $service->id], $def));
            }

            // 2. Persyaratan Teknis Wajib dari kolom requirements_info layanan
            $rawDocs = $service->requirements_info ?: $service->requirements_summary ?: '';
            $lines = array_values(array_filter(array_map(function (string $line): string {
                return trim(ltrim(trim($line), "•-\t "));
            }, explode("\n", $rawDocs))));

            foreach ($lines as $line) {
                $lower = strtolower($line);
                if (str_contains($lower, 'nib') && str_contains($lower, 'akta')) {
                    continue;
                }
                if ($line !== '') {
                    ServiceRequirement::create([
                        'service_id' => $service->id,
                        'document_name' => mb_substr($line, 0, 240),
                        'document_type' => 'Persyaratan Teknis',
                        'is_mandatory' => true,
                        'description' => 'Persyaratan teknis utama sesuai ketentuan instansi terkait.',
                    ]);
                }
            }
        }

        // 3. Pastikan selalu ada minimal 2 dokumen opsional untuk fleksibilitas unggah dokumen pendukung
        $hasOptional = ServiceRequirement::where('service_id', $service->id)
            ->where('is_mandatory', false)
            ->exists();

        if (! $hasOptional) {
            $optionalDefaults = [
                [
                    'document_name' => 'Sertifikat ISO / Sistem Manajemen Mutu (Jika Ada)',
                    'document_type' => 'Dokumen Pendukung',
                    'is_mandatory' => false,
                    'description' => 'Dokumen pendukung opsional untuk mempercepat proses verifikasi teknis.',
                ],
                [
                    'document_name' => 'Surat Kuasa / Dokumen Pendukung Tambahan',
                    'document_type' => 'Dokumen Pendukung',
                    'is_mandatory' => false,
                    'description' => 'Lampiran tambahan opsional seperti surat kuasa pengurusan atau portofolio produk.',
                ],
            ];

            foreach ($optionalDefaults as $opt) {
                ServiceRequirement::create(array_merge(['service_id' => $service->id], $opt));
            }
        }

        return ServiceRequirement::where('service_id', $service->id)
            ->orderByDesc('is_mandatory')
            ->orderBy('id')
            ->get();
    }
}
