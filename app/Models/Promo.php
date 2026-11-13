<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'discount_amount',
        'discount_percentage',
        'min_transaction',
        'terms',
        'badge_text',
        'quota',
        'used_count',
        'is_active',
        'is_featured_homepage',
        'voucher_image',
        'exhausted_image',
        'expired_date',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'min_transaction' => 'decimal:2',
            'quota' => 'integer',
            'used_count' => 'integer',
            'is_active' => 'boolean',
            'is_featured_homepage' => 'boolean',
            'expired_date' => 'date',
        ];
    }

    /**
     * Filter voucher yang aktif, belum kedaluwarsa, dan kuotanya masih tersedia.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q): void {
                $q->whereNull('expired_date')
                    ->orWhere('expired_date', '>=', now()->toDateString());
            })
            ->where(function (Builder $q): void {
                $q->where('quota', '<=', 0)
                    ->orWhereNull('quota')
                    ->orWhereColumn('used_count', '<', 'quota');
            });
    }

    /**
     * Pesanan yang menggunakan promo ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Sisa kuota voucher.
     */
    public function remainingQuota(): int
    {
        return max(0, (int) $this->quota - (int) $this->used_count);
    }

    /**
     * Cek apakah voucher sudah kedaluwarsa.
     */
    public function isExpired(): bool
    {
        return $this->expired_date !== null && $this->expired_date->endOfDay()->isPast();
    }

    /**
     * Hitung nominal potongan untuk suatu subtotal transaksi.
     */
    public function calculateDiscountFor(float $subtotal): float
    {
        $discount = 0.0;
        if ((float) $this->discount_amount > 0) {
            $discount = (float) $this->discount_amount;
        } elseif ((float) ($this->discount_percentage ?? 0) > 0) {
            $discount = round($subtotal * ((float) $this->discount_percentage / 100), 2);
        }

        return min($subtotal, max(0.0, $discount));
    }
}
