<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubKategori extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'kelompok');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }
}
