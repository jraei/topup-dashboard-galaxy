<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaketLayanan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function layanans(): HasMany
    {
        return $this->hasMany(Layanan::class);
    }

    public function fusionServices(): HasMany
    {
        return $this->hasMany(FusionService::class);
    }
}
