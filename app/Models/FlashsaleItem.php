<?php

namespace App\Models;

use App\Models\Layanan;
use App\Models\FlashsaleEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlashsaleItem extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    protected $casts = [
        'harga_flashsale' => 'integer',
        'stok' => 'integer',
        'batas_user' => 'integer',
    ];

    public function flashsaleEvent()
    {
        return $this->belongsTo(FlashsaleEvent::class);
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
    
    // Check if stock is available
    public function isStockAvailable()
    {
        return $this->stok > 0 || $this->stok === null;
    }
    
    // Check if the item is active
    public function isActive()
    {
        return $this->status === 'active' && $this->flashsaleEvent->isActive();
    }
}