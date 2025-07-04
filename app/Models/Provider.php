<?php

namespace App\Models;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provider extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function Produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'provider_id');
    }

    public function Layanan(): HasMany
    {
        return $this->hasMany(Layanan::class);
    }

    public function kategori(): HasMany
    {
        return $this->hasMany(Kategori::class);
    }
}
