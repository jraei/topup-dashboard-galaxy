
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserRole extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'permissions' => 'json',
        'is_default' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Get the users associated with this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_role_id');
    }

    /**
     * Get the profit product associated with this role.
     */
    public function profitProduk(): HasOne
    {
        return $this->hasOne(ProfitProduk::class, 'user_roles_id');
    }

    /**
     * Check if the role is assigned to any users.
     */
    public function hasUsers(): bool
    {
        return $this->users()->exists();
    }
}
