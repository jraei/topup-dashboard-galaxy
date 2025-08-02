<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaketLayanan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'display_order' => 'integer',
    ];

    public function layanans(): HasMany
    {
        return $this->hasMany(Layanan::class);
    }

    public function fusionServices(): HasMany
    {
        return $this->hasMany(FusionService::class);
    }

    /**
     * Get all products associated with services in this package
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function products()
    {
        return $this->layanans()
            ->with('produk')
            ->get()
            ->pluck('produk')
            ->unique('id');
    }

    /**
     * Check if package contains services from multiple products
     */
    public function isCrossProduct(): bool
    {
        return $this->products()->count() > 1;
    }
}
