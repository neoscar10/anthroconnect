<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class CommunityDiscussionReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'community_discussion_replies';

    protected $fillable = [
        'community_discussion_id',
        'user_id',
        'parent_id',
        'body',
        'is_expert_reply',
        'is_featured',
        'is_pinned',
        'status',
        'upvotes_count',
        'downvotes_count',
        'replies_count',
        'depth',
        'published_at',
    ];

    protected $casts = [
        'is_expert_reply' => 'boolean',
        'is_featured' => 'boolean',
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
        'depth' => 'integer',
        'upvotes_count' => 'integer',
        'downvotes_count' => 'integer',
        'replies_count' => 'integer',
    ];

    /**
     * Relationship: A reply belongs to a discussion.
     */
    public function discussion()
    {
        return $this->belongsTo(CommunityDiscussion::class, 'community_discussion_id');
    }

    /**
     * Relationship: An author belongs to the reply.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: A reply can have a parent reply.
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Relationship: A reply can have many children (threaded).
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Relationship: Engagement votes.
     */
    public function votes()
    {
        return $this->morphMany(CommunityVote::class, 'votable');
    }

    /**
     * Scope: Only published replies.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at');
    }

    /**
     * Scope: Top-level replies only.
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Expert insights only.
     */
    public function scopeExpert($query)
    {
        return $query->where('is_expert_reply', true);
    }

    /**
     * Accessor: Calculate net votes.
     */
    public function getNetVotesAttribute()
    {
        return $this->upvotes_count - $this->downvotes_count;
    }
}
