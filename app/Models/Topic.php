<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Topic extends Model
{
    use HasFactory;

    protected $table = 'topics';

    protected $fillable = [
        'name',
        'short_description',
        'is_active',
        'is_members_only',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_members_only' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Relationship with Explore Articles.
     */
    public function exploreArticles()
    {
        return $this->hasMany(ExploreArticle::class, 'topic_id');
    }

    /**
     * Relationship with LMS Modules.
     */
    public function lmsModules()
    {
        return $this->hasMany(\App\Models\Lms\LmsModule::class, 'topic_id');
    }

    /**
     * Relationship with Anthropologists (Encyclopedia).
     */
    public function anthropologists()
    {
        return $this->belongsToMany(
            Encyclopedia\Anthropologist::class, 
            'anthropologist_encyclopedia_topic', 
            'topic_id', 
            'anthropologist_id'
        );
    }

    /**
     * Relationship with Community Discussions.
     */
    public function communityDiscussions()
    {
        return $this->hasMany(\App\Models\Community\CommunityDiscussion::class, 'topic_id');
    }

    /**
     * Scope for active topics.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
