<?php

namespace App\Models;

use App\Models\Kategori;
use App\Models\Provider;
use App\Models\ProfitProduk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function Kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
    public function Provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function ProfitProduk(): HasMany
    {
        return $this->hasMany(ProfitProduk::class, 'produk_id');
    }
}
