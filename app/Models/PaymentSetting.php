<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PaymentSetting extends Model
{
    protected $fillable = [
        'gateway',
        'display_name',
        'is_enabled',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to only include enabled gateways.
     */
    public function scopeScopeEnabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope to only include the default gateway.
     */
    public function scopeScopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to order gateways by sort order.
     */
    public function scopeScopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Retrieve all enabled gateways.
     */
    public static function enabled()
    {
        return self::where('is_enabled', true)->orderBy('sort_order', 'asc')->get();
    }

    /**
     * Retrieve the default gateway.
     */
    public static function default()
    {
        return self::where('is_default', true)->first();
    }

    /**
     * Retrieve all gateways ordered by priority.
     */
    public static function ordered()
    {
        return self::orderBy('sort_order', 'asc')->get();
    }
}
