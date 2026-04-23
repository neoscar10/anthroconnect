<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'short_label',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship: A topic has many discussions.
     */
    public function discussions()
    {
        return $this->hasMany(CommunityDiscussion::class, 'community_topic_id');
    }

    /**
     * Scope: Only active topics.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
