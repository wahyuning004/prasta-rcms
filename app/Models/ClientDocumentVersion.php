<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_document_id',
        'version_number',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'revision_notes',
        'review_status',
        'reviewer_notes',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'version_number' => 'integer',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(ClientDocument::class, 'client_document_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
