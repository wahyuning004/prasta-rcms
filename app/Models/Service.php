<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'requirements_info',
        'timeline_estimation',
        'price',
        // Kolom legacy untuk kompatibilitas
        'requirements_summary',
        'estimation_time',
        'cost',
    ];

    /**
     * Relasi ke Kategori (Category).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke daftar persyaratan dokumen layanan.
     */
    public function requirements(): HasMany
    {
        return $this->hasMany(ServiceRequirement::class)->orderByDesc('is_mandatory')->orderBy('id');
    }

    /**
     * Relasi ke pesanan yang menggunakan layanan ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relasi ke ulasan publik layanan.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relasi ulasan yang sudah dipublikasikan.
     */
    public function publishedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'published')->latest();
    }

    /**
     * Harga efektif layanan (fallback ke cost atau default 3.500.000 jika kosong).
     */
    public function getEffectivePrice(): float
    {
        $val = (float) ($this->price ?: $this->cost ?: 0);

        return $val > 0 ? $val : 3500000.00;
    }

    /**
     * Format harga Rupiah.
     */
    public function getFormattedPriceAttribute(): string
    {
        $val = $this->price ?: $this->cost ?: 0;

        return 'Rp '.number_format($val, 0, ',', '.');
    }

    /**
     * Sinkronisasi data saat saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
            // Sinkronkan ke kolom legacy
            if (! empty($service->requirements_info) && empty($service->requirements_summary)) {
                $service->requirements_summary = $service->requirements_info;
            }
            if (! empty($service->timeline_estimation) && empty($service->estimation_time)) {
                $service->estimation_time = $service->timeline_estimation;
            }
            if ($service->price > 0 && empty($service->cost)) {
                $service->cost = $service->price;
            }
            if ($service->cost > 0 && empty($service->price)) {
                $service->price = $service->cost;
            }
        });
    }
}
