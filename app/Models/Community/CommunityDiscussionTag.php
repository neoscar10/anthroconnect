<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityDiscussionTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: A tag belongs to many discussions.
     */
    public function discussions()
    {
        return $this->belongsToMany(CommunityDiscussion::class, 'community_discussion_tag');
    }

    /**
     * Scope: Only active tags.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
