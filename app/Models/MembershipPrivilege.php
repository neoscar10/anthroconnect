<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipPrivilege extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_setting_id',
        'privilege',
        'sort_order',
    ];

    /**
     * Get the setting that owns the privilege.
     */
    public function membershipSetting(): BelongsTo
    {
        return $this->belongsTo(MembershipSetting::class);
    }
}
