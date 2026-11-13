<?php

namespace App\Models;

use App\Services\TwoWayPaymentVerificationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'invoice_id',
        'payment_method',
        'bank_name',
        'sender_name',
        'amount',
        'payment_proof',
        'original_filename',
        'file_size',
        'notes',
        'rejection_reason',
        'status',
        'verified_by',
        'verified_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'file_size' => 'integer',
            'verified_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'success' => 'Berhasil',
            'failed' => 'Ditolak',
            default => 'Menunggu Verifikasi',
        };
    }

    /**
     * Jalankan validasi keaslian bukti transfer Dua Arah (Two-Way Verification).
     *
     * @return array<string, mixed>
     */
    public function twoWayVerification(): array
    {
        return app(TwoWayPaymentVerificationService::class)->verify($this);
    }
}
