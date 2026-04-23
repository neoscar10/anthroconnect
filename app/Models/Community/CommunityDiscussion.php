<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class CommunityDiscussion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'topic_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'status',
        'discussion_state',
        'is_featured',
        'is_expert_spotlight',
        'is_trending',
        'is_popular',
        'published_at',
        'last_activity_at',
        'views_count',
        'replies_count',
        'likes_count',
        'bookmarks_count',
        'shares_count',
        'solved_reply_id',
        'meta',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_expert_spotlight' => 'boolean',
        'is_trending' => 'boolean',
        'is_popular' => 'boolean',
        'published_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'meta' => 'json',
    ];

    /**
     * Relationship: An author belongs to the discussion.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: A discussion belongs to a topic.
     */
    public function topic()
    {
        return $this->belongsTo(\App\Models\Topic::class, 'topic_id');
    }

    /**
     * Relationship: A discussion can have many tags.
     */
    public function tags()
    {
        return $this->belongsToMany(CommunityDiscussionTag::class, 'community_discussion_tag', 'community_discussion_id', 'community_discussion_tag_id');
    }

    /**
     * Relationship: Top-level replies.
     */
    public function replies()
    {
        return $this->hasMany(CommunityDiscussionReply::class)->topLevel()->published();
    }

    /**
     * Relationship: All replies regardless of nesting.
     */
    public function allReplies()
    {
        return $this->hasMany(CommunityDiscussionReply::class);
    }

    /**
     * Relationship: Expert insights only.
     */
    public function expertReplies()
    {
        return $this->hasMany(CommunityDiscussionReply::class)->expert()->published();
    }

    /**
     * Relationship: Engagement votes.
     */
    public function votes()
    {
        return $this->morphMany(CommunityVote::class, 'votable');
    }

    /**
     * Accessor: Calculate net votes.
     */
    public function getNetVotesAttribute()
    {
        return $this->likes_count - $this->shares_count; // Simplified for now, or use dedicated count
    }

    /**
     * Scope: Only published discussions.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope: Filter by topic ID.
     */
    public function scopeTopicId($query, $id)
    {
        return $query->where('topic_id', $id);
    }

    /**
     * Scope: Order by newest first.
     */
    public function scopeNewest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    /**
     * Scope: Order by "Hot" (relevance engagement).
     */
    public function scopeHot($query)
    {
        return $query->orderByRaw('(replies_count * 3 + likes_count + views_count * 0.1) DESC')
                     ->orderBy('last_activity_at', 'desc');
    }

    /**
     * Scope: Filter unsolved discussions.
     */
    public function scopeUnsolved($query)
    {
        return $query->where('discussion_state', '!=', 'solved');
    }
}
