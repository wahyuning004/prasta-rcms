<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'stage_code',
        'stage_order',
        'stage_name',
        'description',
        'status',
        'progress_percentage',
        'date_recorded',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'stage_order' => 'integer',
            'progress_percentage' => 'integer',
            'date_recorded' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
