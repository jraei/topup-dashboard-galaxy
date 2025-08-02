<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FusionService extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'layanan_ids' => 'array',
        'custom_price' => 'decimal:2',
        'display_order' => 'integer',
    ];

    public function paketLayanan(): BelongsTo
    {
        return $this->belongsTo(PaketLayanan::class, 'paket_layanan_id');
    }

    public function pembelians(): HasMany
    {
        return $this->hasMany(Pembelian::class);
    }

    /**
     * Get the services that are part of this fusion
     */
    public function services()
    {
        return Layanan::whereIn('id', $this->layanan_ids ?? [])->get();
    }

    public function layanans(): HasMany
    {
        return $this->hasMany(Layanan::class, 'id', 'layanan_ids');
    }

    public function getLayanansAttribute()
    {
        return Layanan::with('produk')->whereIn('id', $this->layanan_ids ?? [])->get();
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
     * Calculate fusion price with flash sale discounts applied
     */
    public function calculateFinalPrice($userRoleId = null, $applyPromotions = true): float
    {
        if ($this->custom_price) {
            return (float) $this->custom_price;
        }

        $services = $this->services();
        $totalPrice = 0;

        foreach ($services as $service) {
            if ($applyPromotions) {
                $totalPrice += $service->calculateFinalPrice($userRoleId, true);
            } else {
                $totalPrice += $service->getHargaLayanan($userRoleId);
            }
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

    /**
     * Validate that all services in fusion are active and compatible
     */
    public function validateServices(): array
    {
        $services = $this->services();
        $errors = [];

        if ($services->count() !== count($this->layanan_ids ?? [])) {
            $errors[] = 'Some services in this fusion are no longer available';
        }

        foreach ($services as $service) {
            if ($service->status !== 'active') {
                $errors[] = "Service '{$service->nama_layanan}' is not active";
            }
        }

        return $errors;
    }

    /**
     * Check if fusion has compatible input requirements
     */
    public function hasCompatibleInputs(): bool
    {
        $services = $this->services()->load('produk.inputFields');

        if ($services->isEmpty()) {
            return false;
        }

        // Get first service's input requirements as baseline
        $firstService = $services->first();
        $firstInputs = $firstService->produk->inputFields->pluck('name')->sort()->values();

        // Check if all services have same input requirements
        foreach ($services as $service) {
            $serviceInputs = $service->produk->inputFields->pluck('name')->sort()->values();
            if ($firstInputs->toArray() != $serviceInputs->toArray()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get breakdown of individual service prices
     */
    public function getPriceBreakdown($userRoleId = null): array
    {
        $services = $this->services();
        $breakdown = [];

        foreach ($services as $service) {
            $breakdown[] = [
                'service_id' => $service->id,
                'service_name' => $service->nama_layanan,
                'product_name' => $service->produk->nama_produk,
                'base_price' => $service->getHargaLayanan($userRoleId),
                'final_price' => $service->calculateFinalPrice($userRoleId, true),
                'provider' => $service->produk->provider->provider_name ?? null,
            ];
        }

        return $breakdown;
    }
}
