<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produk extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function subKategori(): BelongsTo
    {
        return $this->belongsTo(SubKategori::class, 'kelompok');
    }
}
