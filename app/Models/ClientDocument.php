<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'document_name',
        'document_type',
        'service_requirement_id',
        'file_path',
        'original_filename',
        'version',
        'parent_id',
        'file_size',
        'mime_type',
        'status',
        'notes',
        'revision_note',
        'uploaded_by',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(ServiceRequirement::class, 'service_requirement_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ClientDocumentVersion::class)->orderByDesc('version_number');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Label status verifikasi dokumen dalam bahasa Indonesia.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'approved' => 'Diterima',
            'rejected' => 'Perlu Revisi',
            default => 'Menunggu Pemeriksaan',
        };
    }
}
