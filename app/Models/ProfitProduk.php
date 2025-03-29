
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfitProduk extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public static function calculatePrice($basePrice, $userLevel = 'guest')
    {
        $profit = match($userLevel) {
            'basic' => $this->profit_basic,
            'premium' => $this->profit_premium,
            'h2h' => $this->profit_h2h,
            default => $this->profit_guest, // guest or any unknown level
        };
        
        return $basePrice + ($basePrice * $profit / 100);
    }
}
