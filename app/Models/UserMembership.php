<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'membership_setting_id',
        'amount_paid_inr',
        'status',
        'started_at',
        'expires_at',
        'payment_reference',
    ];

    protected $casts = [
        'amount_paid_inr' => 'decimal:2',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the membership.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the setting the user subscribed under.
     */
    public function membershipSetting(): BelongsTo
    {
        return $this->belongsTo(MembershipSetting::class);
    }

    /**
     * Scope a query to only include active memberships.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
