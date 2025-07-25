<?php

namespace App\Models;

use App\Models\Produk;
use App\Models\FlashsaleItem;
use App\Models\FlashsaleEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Layanan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'harga_beli' => 'float',
        'harga_beli_idr' => 'float',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function flashSaleItem()
    {
        return $this->hasMany(FlashsaleItem::class);
    }

    public function flashsaleEvent()
    {
        return $this->belongsToMany(FlashsaleEvent::class, 'flashsale_items')
            ->withPivot('harga_flashsale', 'stok', 'batas_user')
            ->withTimestamps();
    }

    public function paketLayanan(): BelongsTo
    {
        return $this->belongsTo(PaketLayanan::class);
    }

    public function priceRules()
    {
        return $this->hasOneThrough(
            ProfitProduk::class,
            Produk::class,
            'id', // Foreign key on produks table
            'produk_id', // Foreign key on profit_produks table
            'produk_id', // Local key on layanans table
            'id' // Local key on produks table
        );
    }

    /**
     * Get the regular price for this service with enhanced caching
     * Priority: Package profit rules > Product profit rules > Base price
     * 
     * @param int|null $userRoleId The user role ID to consider for profit calculation
     * @return float The calculated price
     */
    public function getHargaLayanan($userRoleId = null)
    {
        $basePrice = $this->harga_beli_idr ?: $this->harga_beli;

        // If no user role specified, return base price
        if (!$userRoleId) {
            return $basePrice;
        }

        // Use cached query when possible to reduce DB calls
        static $profitRulesCache = [];
        static $packageProfitRulesCache = [];

        // First, check if this service belongs to a package and has package-specific profit rules
        if ($this->paket_layanan_id) {
            $packageCacheKey = "{$this->paket_layanan_id}_{$userRoleId}";

            if (!isset($packageProfitRulesCache[$packageCacheKey])) {
                $packageProfitRulesCache[$packageCacheKey] = \App\Models\ProfitPaketLayanan::where('paket_layanan_id', $this->paket_layanan_id)
                    ->where('user_roles_id', $userRoleId)
                    ->first();
            }

            $packageProfitRule = $packageProfitRulesCache[$packageCacheKey];

            // If package profit rule exists, use it
            if ($packageProfitRule) {
                return $packageProfitRule->calculatePrice($basePrice);
            }
        }

        // Fallback to product-specific profit rules
        $productCacheKey = "{$this->produk_id}_{$userRoleId}";

        if (!isset($profitRulesCache[$productCacheKey])) {
            // Try to find a product-specific profit rule
            $profitRulesCache[$productCacheKey] = ProfitProduk::where('produk_id', $this->produk_id)
                ->where('user_roles_id', $userRoleId)
                ->first();
        }

        $profitRule = $profitRulesCache[$productCacheKey];

        // If no specific rule found, return base price
        if (!$profitRule) {
            return $basePrice;
        }

        // Calculate price based on profit rule
        return $profitRule->calculatePrice($basePrice);
    }

    protected $appends = ['harga_jual'];

    /**
     * Assessor for harga_jual
     * Cek harga jual kalau udah login
     */
    public function getHargaJualAttribute()
    {
        $user = auth()->user();
        $userRoleId = $user->user_role_id ?? null;

        static $guestRoleId = null;

        if (!$userRoleId) {
            if ($guestRoleId === null) {
                $guestRoleId = \App\Models\UserRole::where('role_name', 'guest')->value('id');
            }
            $userRoleId = $guestRoleId;
        }

        return $this->getHargaLayanan($userRoleId);
    }



    /**
     * Calculate the final price considering all factors
     * 
     * @param int|null $userRoleId The user role ID
     * @param bool $applyPromotions Whether to apply active promotions
     * @return float The final calculated price
     */
    public function calculateFinalPrice($userRoleId = null, $applyPromotions = true)
    {
        // Get the base price with profit margins applied
        $price = $this->getHargaLayanan($userRoleId);

        // Apply active promotions if requested
        if ($applyPromotions) {
            $activeFlashItem = $this->getActiveFlashsaleItem();
            if ($activeFlashItem) {
                return $activeFlashItem->harga_flashsale;
            }
        }

        return $price;
    }

    /**
     * Check if this layanan is currently on flash sale
     */
    public function isOnFlashsale()
    {
        return $this->flashSaleItem()
            ->whereHas('flashsaleEvent', function ($query) {
                $query->where('status', 'active')
                    ->where('event_start_date', '<=', now())
                    ->where('event_end_date', '>=', now());
            })
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Get the active flash sale item for this layanan if available
     */
    public function getActiveFlashsaleItem()
    {
        return $this->flashSaleItem()
            ->whereHas('flashsaleEvent', function ($query) {
                $query->where('status', 'active')
                    ->where('event_start_date', '<=', now())
                    ->where('event_end_date', '>=', now());
            })
            ->where('status', 'active')
            ->first();
    }
}