<?php

namespace App\Services\Community;

use App\Models\Community\CommunityDiscussion;
use App\Models\Topic;
use App\Models\Community\CommunityDiscussionTag;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Community\CommunityDiscussionReply;
use App\Models\Community\CommunityVote;

class CommunityDiscussionService
{
    /**
     * Get all active topics for browsing.
     */
    public function getBrowseTopics()
    {
        return Topic::active()
            ->withCount(['communityDiscussions' => function($q) {
                $q->published();
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the discussion feed based on filters and sorting.
     */
    public function getDiscussionFeed(array $filters = [])
    {
        $query = CommunityDiscussion::with(['author', 'topic', 'tags'])
            ->published();

        // Topic Filter
        if (!empty($filters['topic'])) {
            $query->topicId($filters['topic']);
        }

        // Tag Filter
        if (!empty($filters['tag'])) {
            $query->whereHas('tags', function($q) use ($filters) {
                $q->where('slug', $filters['tag']);
            });
        }

        // Search Filter
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $search = $filters['search'];
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        // Tab Filtering
        $tab = $filters['tab'] ?? 'all';
        switch ($tab) {
            case 'hot':
                $query->hot();
                break;
            case 'newest':
                $query->newest();
                break;
            case 'unsolved':
                $query->unsolved()->newest();
                break;
            default: // all
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('published_at', 'desc');
                break;
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Get popular discussions for the sidebar.
     */
    public function getPopularDiscussions(int $limit = 5)
    {
        return CommunityDiscussion::published()
            ->with(['topic'])
            ->orderBy('views_count', 'desc')
            ->orderBy('replies_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get trending tags for the sidebar.
     */
    public function getTrendingTags(int $limit = 8)
    {
        return CommunityDiscussionTag::active()
            ->withCount('discussions')
            ->orderBy('discussions_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the latest expert spotlight discussion.
     */
    public function getExpertSpotlight()
    {
        return CommunityDiscussion::published()
            ->with(['author', 'topic'])
            ->where('is_expert_spotlight', true)
            ->orderBy('published_at', 'desc')
            ->first();
    }

    /**
     * Get a discussion by its slug with all relations for detail view.
     */
    public function getDiscussionDetailBySlug(string $slug)
    {
        $discussion = CommunityDiscussion::with(['author', 'topic', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Increment views safely
        $discussion->increment('views_count');

        return $discussion;
    }

    /**
     * Get threaded replies for a discussion.
     */
    public function getDiscussionReplies(CommunityDiscussion $discussion)
    {
        return CommunityDiscussionReply::with(['author', 'children.author', 'children.children.author'])
            ->where('community_discussion_id', $discussion->id)
            ->topLevel()
            ->published()
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'asc')
            ->get();
    }

    /**
     * Get expert insights for a discussion.
     */
    public function getExpertInsights(CommunityDiscussion $discussion, int $limit = 3)
    {
        return CommunityDiscussionReply::with(['author'])
            ->where('community_discussion_id', $discussion->id)
            ->expert()
            ->published()
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get related discussions based on topic and tags.
     */
    public function getRelatedDiscussions(CommunityDiscussion $discussion, int $limit = 4)
    {
        $tagIds = $discussion->tags->pluck('id')->toArray();

        return CommunityDiscussion::published()
            ->where('id', '!=', $discussion->id)
            ->where(function($q) use ($discussion, $tagIds) {
                $q->where('topic_id', $discussion->topic_id)
                  ->orWhereHas('tags', function($t) use ($tagIds) {
                      $t->whereIn('community_discussion_tags.id', $tagIds);
                  });
            })
            ->with(['author', 'topic'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Cast or toggle a vote on a discussion or reply.
     */
    public function castVote($votable, User $user, int $value)
    {
        return DB::transaction(function() use ($votable, $user, $value) {
            $existing = CommunityVote::where('user_id', $user->id)
                ->where('votable_type', get_class($votable))
                ->where('votable_id', $votable->id)
                ->first();

            if ($existing) {
                if ($existing->vote === $value) {
                    // Toggle off if same vote
                    $this->adjustVoteCounts($votable, $value, false);
                    $existing->delete();
                    return null;
                } else {
                    // Change vote from up to down or vice-versa
                    $this->adjustVoteCounts($votable, $existing->vote, false);
                    $existing->update(['vote' => $value]);
                    $this->adjustVoteCounts($votable, $value, true);
                }
            } else {
                // New vote
                CommunityVote::create([
                    'user_id' => $user->id,
                    'votable_type' => get_class($votable),
                    'votable_id' => $votable->id,
                    'vote' => $value,
                ]);
                $this->adjustVoteCounts($votable, $value, true);
            }

            return $votable->fresh();
        });
    }

    /**
     * Internal helper to synchronize vote counts on the model.
     */
    protected function adjustVoteCounts($votable, int $voteType, bool $applying = true)
    {
        $isReply = $votable instanceof CommunityDiscussionReply;

        if ($voteType > 0) {
            // Action relates to UPVOTE (+1)
            $field = $isReply ? 'upvotes_count' : 'likes_count';
            $applying ? $votable->increment($field) : $votable->decrement($field);
        } else {
            // Action relates to DOWNVOTE (-1)
            if ($isReply) {
                $applying ? $votable->increment('downvotes_count') : $votable->decrement('downvotes_count');
            } else {
                // Discussion downvote logic (if no dedicated downvotes_count col)
                // New downvote decrements likes, reverting a downvote increments likes
                $applying ? $votable->decrement('likes_count') : $votable->increment('likes_count');
            }
        }
    }

    /**
     * Create a new reply.
     */
    public function createReply(CommunityDiscussion $discussion, array $data, User $user)
    {
        return DB::transaction(function() use ($discussion, $data, $user) {
            $parent = null;
            if (!empty($data['parent_id'])) {
                $parent = CommunityDiscussionReply::find($data['parent_id']);
            }

            $reply = CommunityDiscussionReply::create([
                'community_discussion_id' => $discussion->id,
                'user_id' => $user->id,
                'parent_id' => $parent?->id,
                'body' => $data['body'],
                'depth' => $parent ? ($parent->depth + 1) : 0,
                'status' => 'published',
                'published_at' => now(),
            ]);

            // Sync counts
            $discussion->increment('replies_count');
            $discussion->update(['last_activity_at' => now()]);
            
            if ($parent) {
                $parent->increment('replies_count');
            }

            return $reply;
        });
    }

    /**
     * Create a new scholarly discussion.
     */
    public function createDiscussion(array $data, User $user)
    {
        return DB::transaction(function() use ($data, $user) {
            $discussion = CommunityDiscussion::create([
                'user_id' => $user->id,
                'topic_id' => $data['topic_id'],
                'title' => $data['title'],
                'slug' => Str::slug($data['title']) . '-' . Str::random(5),
                'excerpt' => $data['excerpt'] ?? Str::words(strip_tags($data['body']), 25),
                'body' => $data['body'],
                'status' => 'published',
                'published_at' => now(),
                'last_activity_at' => now(),
            ]);

            if (!empty($data['tags'])) {
                $tags = is_array($data['tags']) ? $data['tags'] : explode(',', $data['tags']);
                $tagIds = [];
                foreach ($tags as $tagName) {
                    $tagName = trim($tagName);
                    if (empty($tagName)) continue;
                    
                    $tag = CommunityDiscussionTag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName]
                    );
                    $tagIds[] = $tag->id;
                }
                $discussion->tags()->sync($tagIds);
            }

            return $discussion;
        });
    }
}
