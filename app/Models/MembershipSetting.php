<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'price_inr',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price_inr' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the privileges for the membership setting.
     */
    public function privileges(): HasMany
    {
        return $this->hasMany(MembershipPrivilege::class)->orderBy('sort_order');
    }

    /**
     * Get the user memberships for this setting.
     */
    public function userMemberships(): HasMany
    {
        return $this->hasMany(UserMembership::class);
    }
}
