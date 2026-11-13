<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProjectTracking;

class OrderProgressService
{
    /**
     * Daftar tahapan standar pengerjaan proyek secara berurutan.
     *
     * @return array<int, array{code: string, order: int, name: string, percentage: int, default_description: string}>
     */
    public static function stages(): array
    {
        return [
            1 => [
                'code' => 'order_received',
                'order' => 1,
                'name' => 'Pesanan Diterima',
                'percentage' => 15,
                'default_description' => 'Pesanan layanan telah dikonfirmasi dan terdaftar dalam sistem antrean IzinYou.',
            ],
            2 => [
                'code' => 'payment_verification',
                'order' => 2,
                'name' => 'Verifikasi Pembayaran',
                'percentage' => 30,
                'default_description' => 'Pemeriksaan bukti transfer & konfirmasi pembayaran oleh tim Finance.',
            ],
            3 => [
                'code' => 'document_review',
                'order' => 3,
                'name' => 'Pemeriksaan Dokumen',
                'percentage' => 50,
                'default_description' => 'Telaah kelengkapan dokumen legalitas dan persyaratan teknis oleh tim Legal.',
            ],
            4 => [
                'code' => 'in_progress',
                'order' => 4,
                'name' => 'Proses Pengerjaan',
                'percentage' => 70,
                'default_description' => 'Penyusunan dokumen SOP, registrasi portal instansi resmi, dan pendampingan teknis.',
            ],
            5 => [
                'code' => 'awaiting_approval',
                'order' => 5,
                'name' => 'Menunggu Persetujuan',
                'percentage' => 85,
                'default_description' => 'Proses evaluasi akhir, verifikasi lapangan/audit, dan penerbitan izin dari instansi.',
            ],
            6 => [
                'code' => 'completed',
                'order' => 6,
                'name' => 'Selesai',
                'percentage' => 100,
                'default_description' => 'Sertifikat / izin resmi telah diterbitkan dan proyek dinyatakan selesai.',
            ],
        ];
    }

    /**
     * Cari metadata tahap berdasarkan stage_code atau nama tahap.
     *
     * @return array{code: string, order: int, name: string, percentage: int, default_description: string}
     */
    public function resolveStage(string $stageCodeOrName): array
    {
        $stages = self::stages();
        $needle = strtolower(trim($stageCodeOrName));

        foreach ($stages as $stage) {
            if (strtolower($stage['code']) === $needle || strtolower($stage['name']) === $needle) {
                return $stage;
            }
        }

        return [
            'code' => 'custom',
            'order' => 4,
            'name' => $stageCodeOrName,
            'percentage' => 70,
            'default_description' => $stageCodeOrName,
        ];
    }

    /**
     * Catat tahapan progres baru dan sinkronkan persentase & status pada tabel orders.
     */
    public function recordStage(
        Order $order,
        string $stageCodeOrName,
        string $status = 'completed',
        ?string $description = null,
        ?int $updatedBy = null
    ): ProjectTracking {
        if ($order->isCancelled() && $stageCodeOrName !== 'order_cancelled') {
            throw new \DomainException('Pesanan telah dibatalkan dan linimasa tidak dapat diperbarui.');
        }

        $stage = $this->resolveStage($stageCodeOrName);
        $percentage = $this->calculateStagePercentage($stage, $status, $order);

        $tracking = ProjectTracking::create([
            'order_id' => $order->id,
            'stage_code' => $stage['code'],
            'stage_order' => $stage['order'],
            'stage_name' => $stage['name'],
            'description' => $description ?: $stage['default_description'],
            'status' => $status,
            'progress_percentage' => $percentage,
            'date_recorded' => now(),
            'updated_by' => $updatedBy,
        ]);

        // Hitung persentase konsisten tertinggi yang pernah dicapai order
        $highestPercent = max((int) $order->progress_percentage, $percentage);
        if ($stage['code'] === 'completed' && $status === 'completed') {
            $highestPercent = 100;
        }

        $orderUpdate = [
            'current_stage' => $stage['name'],
            'progress_percentage' => $highestPercent,
        ];

        if ($stage['code'] === 'completed' && $status === 'completed') {
            $orderUpdate['status'] = 'completed';
            $orderUpdate['completed_at'] = $order->completed_at ?? now();
        } elseif ($stage['order'] >= 3 && $order->status === 'pending') {
            $orderUpdate['status'] = 'processing';
        } elseif ($stage['code'] === 'payment_verification' && $status === 'completed' && $order->status === 'pending') {
            $orderUpdate['status'] = 'processing';
        }

        $order->update($orderUpdate);

        return $tracking;
    }

    /**
     * Bangun struktur timeline 6 tahapan untuk UI pelanggan dan staf.
     *
     * @return array<int, array{code: string, order: int, name: string, status: string, description: string, updated_at: ?string, percentage: int}>
     */
    public function buildTimeline(Order $order): array
    {
        $trackings = $order->relationLoaded('trackings')
            ? $order->trackings
            : $order->trackings()->get();

        $byCode = [];
        $highestCompletedOrder = 0;

        foreach ($trackings as $track) {
            $code = $track->stage_code ?: $this->resolveStage($track->stage_name)['code'];
            $byCode[$code] = $track;
            $stageOrder = (int) ($track->stage_order ?: $this->resolveStage($track->stage_name)['order']);
            if ($track->status === 'completed' && $stageOrder > $highestCompletedOrder) {
                $highestCompletedOrder = $stageOrder;
            }
        }

        if ($order->status === 'completed') {
            $highestCompletedOrder = 6;
        }

        $timeline = [];
        foreach (self::stages() as $stage) {
            $matched = $byCode[$stage['code']] ?? null;
            $status = 'pending';

            if ($matched) {
                $status = in_array($matched->status, ['completed', 'in_progress', 'pending'], true)
                    ? $matched->status
                    : 'completed';
            } elseif ($stage['order'] <= $highestCompletedOrder) {
                $status = 'completed';
            }

            $timeline[] = [
                'code' => $stage['code'],
                'order' => $stage['order'],
                'name' => $stage['name'],
                'status' => $status,
                'description' => $matched?->description ?? $stage['default_description'],
                'updated_at' => $matched?->date_recorded?->format('d M Y, H:i') ?? null,
                'percentage' => $stage['percentage'],
            ];
        }

        return $timeline;
    }

    /**
     * Hitung persentase progres untuk tahapan tertentu.
     *
     * @param  array{code: string, order: int, name: string, percentage: int, default_description: string}  $stage
     */
    protected function calculateStagePercentage(array $stage, string $status, Order $order): int
    {
        if ($status === 'completed') {
            return $stage['percentage'];
        }

        if ($status === 'in_progress') {
            $prevOrder = max(1, $stage['order'] - 1);
            $prevPercent = self::stages()[$prevOrder]['percentage'] ?? 15;
            $inProgressPercent = (int) round(($prevPercent + $stage['percentage']) / 2);

            return max((int) $order->progress_percentage, $inProgressPercent);
        }

        return max(0, (int) $order->progress_percentage);
    }
}
