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
}
