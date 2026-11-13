<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'invoice_number',
        'issued_at',
        'due_date',
        'customer_name',
        'customer_company',
        'customer_email',
        'customer_phone',
        'customer_address',
        'service_name',
        'subtotal',
        'discount_amount',
        'total_amount',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Label status pembayaran pada invoice.
     */
    public function statusLabel(): string
    {
        return match ($this->payment_status) {
            'paid', 'success' => 'Berhasil (Lunas)',
            'waiting_verification' => 'Menunggu Verifikasi',
            'rejected', 'failed' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default => 'Menunggu Pembayaran',
        };
    }

    /**
     * Cek apakah invoice sudah lunas.
     */
    public function isPaid(): bool
    {
        return in_array($this->payment_status, ['paid', 'success'], true);
    }

    /**
     * Cek apakah invoice telah melewati batas jatuh tempo.
     */
    public function isOverdue(): bool
    {
        if ($this->isPaid() || ! $this->due_date) {
            return false;
        }

        return now()->startOfDay()->greaterThan($this->due_date->startOfDay());
    }

    /**
     * Warna badge status pembayaran (Tailwind palette).
     */
    public function statusBadgeColor(): string
    {
        return match ($this->payment_status) {
            'paid', 'success' => 'emerald',
            'waiting_verification' => 'amber',
            'rejected', 'failed' => 'rose',
            'cancelled' => 'slate',
            default => 'blue',
        };
    }

    /**
     * Informasi dan indikator visual jatuh tempo tagihan.
     *
     * @return array{label: string, color: string, is_overdue: bool}
     */
    public function dueStatusMeta(): array
    {
        if ($this->isPaid()) {
            return [
                'label' => 'Lunas',
                'color' => 'emerald',
                'is_overdue' => false,
            ];
        }

        if (! $this->due_date) {
            return [
                'label' => 'Tanpa Tempo',
                'color' => 'slate',
                'is_overdue' => false,
            ];
        }

        $today = now()->startOfDay();
        $due = $this->due_date->startOfDay();
        $diffDays = (int) $today->diffInDays($due, false);

        if ($diffDays < 0) {
            return [
                'label' => 'Terlewat '.abs($diffDays).' hari',
                'color' => 'rose',
                'is_overdue' => true,
            ];
        }

        if ($diffDays === 0) {
            return [
                'label' => 'Jatuh Tempo Hari Ini',
                'color' => 'amber',
                'is_overdue' => false,
            ];
        }

        return [
            'label' => 'Tempo dlm '.$diffDays.' hari',
            'color' => $diffDays <= 3 ? 'amber' : 'slate',
            'is_overdue' => false,
        ];
    }

    /**
     * Keterangan pembatalan dari order terkait.
     */
    public function cancellationReason(): ?string
    {
        return $this->order?->cancellationReason();
    }

    /**
     * Waktu pembatalan dari order terkait.
     */
    public function cancellationDate(): ?string
    {
        return $this->order?->cancellationDate();
    }
}
