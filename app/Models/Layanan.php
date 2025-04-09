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

    /**
     * Get the regular price for this service
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

        // Try to find a product-specific profit rule
        $profitRule = ProfitProduk::where('produk_id', $this->produk_id)
            ->where('user_roles_id', $userRoleId)
            ->first();

        // If no specific rule found, return base price
        if (!$profitRule) {
            return $basePrice;
        }

        // Calculate price based on profit rule
        return $profitRule->calculatePrice($basePrice);
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