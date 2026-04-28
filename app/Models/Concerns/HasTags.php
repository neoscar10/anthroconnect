<?php

namespace App\Models\Concerns;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;

trait HasTags
{
    /**
     * Polymorphic relationship with Tags.
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    /**
     * Get tags filtered by group slug.
     */
    public function tagsByGroup(string $groupSlug)
    {
        return $this->tags()->whereHas('group', function ($query) use ($groupSlug) {
            $query->where('slug', $groupSlug);
        })->get();
    }

    /**
     * Sync tags for the model.
     */
    public function syncTags(array $tagIds)
    {
        return $this->tags()->sync($tagIds);
    }

    /**
     * Scope to filter models by a single tag.
     */
    public function scopeWithTag(Builder $query, $tagId)
    {
        return $query->whereHas('tags', function ($q) use ($tagId) {
            $q->where('tags.id', $tagId);
        });
    }

    /**
     * Scope to filter models by multiple tags (any of).
     */
    public function scopeWithTags(Builder $query, array $tagIds)
    {
        return $query->whereHas('tags', function ($q) use ($tagIds) {
            $q->whereIn('tags.id', $tagIds);
        });
    }

    /**
     * Scope to filter models by a specific tag group and its tags.
     */
    public function scopeWithTagGroup(Builder $query, string $groupSlug, array $tagIds = [])
    {
        return $query->whereHas('tags', function ($q) use ($groupSlug, $tagIds) {
            $q->whereHas('group', function ($g) use ($groupSlug) {
                $g->where('slug', $groupSlug);
            });

            if (!empty($tagIds)) {
                $q->whereIn('tags.id', $tagIds);
            }
        });
    }
}
