<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FusionService extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'layanan_ids' => 'array',
        'custom_price' => 'decimal:2',
    ];

    public function paketLayanan(): BelongsTo
    {
        return $this->belongsTo(PaketLayanan::class, 'paket_layanan_id');
    }

    /**
     * Get the services that are part of this fusion
     */
    public function services()
    {
        return Layanan::whereIn('id', $this->layanan_ids ?? [])->get();
    }

    /**
     * Calculate the total price for this fusion based on individual service prices
     */
    public function calculateTotalPrice($userRoleId = null): float
    {
        if ($this->custom_price) {
            return (float) $this->custom_price;
        }

        $services = $this->services();
        $totalPrice = 0;

        foreach ($services as $service) {
            $totalPrice += $service->getHargaLayanan($userRoleId);
        }

        return $totalPrice;
    }

    /**
     * Get all service codes for API processing
     */
    public function getServiceCodes(): array
    {
        $services = $this->services();
        return $services->pluck('kode_layanan')->toArray();
    }

    /**
     * Get services grouped by provider for batch processing
     */
    public function getServicesByProvider(): array
    {
        $services = $this->services()->load('produk.provider');
        $grouped = [];

        foreach ($services as $service) {
            $providerName = strtolower($service->produk->provider->provider_name);
            if (!isset($grouped[$providerName])) {
                $grouped[$providerName] = [];
            }
            $grouped[$providerName][] = $service;
        }

        return $grouped;
    }
}