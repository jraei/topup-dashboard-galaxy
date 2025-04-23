
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAffiliate extends Model
{
    protected $fillable = [
        'user_id',
        'referral_code',
        'commission_rate'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referredUsers(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by', 'user_id');
    }
}
