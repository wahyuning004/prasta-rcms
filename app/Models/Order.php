<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'promo_id',
        'promo_code',
        'service_id',
        'base_price',
        'discount_amount',
        'total_cost',
        'notes',
        'checkout_token',
        'status',
        'current_stage',
        'progress_percentage',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'progress_percentage' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('created_at');
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function paymentLogs(): HasMany
    {
        return $this->hasMany(PaymentLog::class)->latest('created_at');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClientDocument::class);
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(ProjectTracking::class)->orderBy('date_recorded')->orderBy('id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Resolve layanan utama pesanan (dari service_id atau item pertama).
     */
    public function resolvedService(): ?Service
    {
        if ($this->relationLoaded('service') && $this->service) {
            return $this->service;
        }

        if ($this->service_id) {
            return Service::find($this->service_id);
        }

        $firstItem = $this->items()->with('service')->first();

        return $firstItem?->service;
    }

    /**
     * Label status pembayaran yang mudah dipahami klien.
     *
     * @return array{code: string, label: string, badge: string}
     */
    public function paymentStatusMeta(): array
    {
        if ($this->status === 'cancelled') {
            return [
                'code' => 'cancelled',
                'label' => 'Dibatalkan',
                'badge' => 'rose',
            ];
        }

        $latest = $this->relationLoaded('latestPayment') ? $this->latestPayment : $this->latestPayment()->first();

        if (! $latest) {
            return [
                'code' => 'unpaid',
                'label' => 'Menunggu Pembayaran',
                'badge' => 'amber',
            ];
        }

        return match ($latest->status) {
            'success' => [
                'code' => 'paid',
                'label' => 'Berhasil',
                'badge' => 'emerald',
            ],
            'failed' => [
                'code' => 'rejected',
                'label' => 'Ditolak',
                'badge' => 'rose',
            ],
            default => [
                'code' => 'waiting_verification',
                'label' => 'Menunggu Verifikasi',
                'badge' => 'sky',
            ],
        };
    }

    /**
     * Label status order dalam bahasa Indonesia.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'verified' => 'Terverifikasi',
            'processing' => 'Dalam Proses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst((string) $this->status),
        };
    }

    /**
     * Cek apakah pesanan berstatus dibatalkan.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Cek apakah pesanan masih boleh dibatalkan oleh pelanggan
     * (hanya saat status masih pending, belum ada pembayaran terverifikasi/menunggu konfirmasi, & progres awal).
     */
    public function canBeCancelled(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        if ((int) ($this->progress_percentage ?? 15) > 25) {
            return false;
        }

        $latest = $this->relationLoaded('latestPayment') ? $this->latestPayment : $this->latestPayment()->first();
        if ($latest && in_array($latest->status, ['waiting_confirmation', 'success'], true)) {
            return false;
        }

        return true;
    }

    /**
     * Ambil alasan pembatalan pesanan dari log tracking atau payment log.
     */
    public function cancellationReason(): ?string
    {
        $cancelTrack = $this->relationLoaded('trackings')
            ? $this->trackings->firstWhere('stage_code', 'order_cancelled')
            : $this->trackings()->where('stage_code', 'order_cancelled')->latest('id')->first();

        if ($cancelTrack && ! empty($cancelTrack->description)) {
            return $cancelTrack->description;
        }

        $cancelLog = $this->relationLoaded('paymentLogs')
            ? $this->paymentLogs->firstWhere('status', 'cancelled')
            : $this->paymentLogs()->where('status', 'cancelled')->latest('id')->first();

        if ($cancelLog && ! empty($cancelLog->notes)) {
            return $cancelLog->notes;
        }

        return $this->isCancelled() ? 'Pesanan dibatalkan oleh pelanggan sebelum proses transaksi diselesaikan.' : null;
    }

    /**
     * Ambil tanggal & waktu saat pesanan dibatalkan.
     */
    public function cancellationDate(): ?string
    {
        $cancelTrack = $this->relationLoaded('trackings')
            ? $this->trackings->firstWhere('stage_code', 'order_cancelled')
            : $this->trackings()->where('stage_code', 'order_cancelled')->latest('id')->first();

        if ($cancelTrack && $cancelTrack->date_recorded) {
            return date('d M Y, H:i', strtotime((string) $cancelTrack->date_recorded));
        }

        $cancelLog = $this->relationLoaded('paymentLogs')
            ? $this->paymentLogs->firstWhere('status', 'cancelled')
            : $this->paymentLogs()->where('status', 'cancelled')->latest('id')->first();

        if ($cancelLog && $cancelLog->created_at) {
            return date('d M Y, H:i', strtotime((string) $cancelLog->created_at));
        }

        return $this->isCancelled() ? date('d M Y, H:i', strtotime((string) $this->updated_at)) : null;
    }
}
