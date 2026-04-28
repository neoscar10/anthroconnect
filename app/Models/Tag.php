<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_group_id',
        'name',
        'slug',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship with TagGroup.
     */
    public function group()
    {
        return $this->belongsTo(TagGroup::class, 'tag_group_id');
    }

    /**
     * Relationship with the polymorphic pivot table.
     */
    public function taggables()
    {
        return $this->hasMany(Taggable::class, 'tag_id');
    }

    /**
     * Scope for active tags.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
