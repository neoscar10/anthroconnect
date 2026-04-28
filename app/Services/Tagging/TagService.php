<?php

namespace App\Services\Tagging;

use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Support\Str;

class TagService
{
    /**
     * Create or update a tag group.
     */
    public function saveTagGroup(array $data, ?TagGroup $group = null): TagGroup
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        
        if ($group) {
            $group->update($data);
            return $group;
        }

        return TagGroup::create($data);
    }

    /**
     * Create or update a tag.
     */
    public function saveTag(array $data, ?Tag $tag = null): Tag
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        if ($tag) {
            $tag->update($data);
            return $tag;
        }

        return Tag::create($data);
    }

    /**
     * Get all active tag groups with their active tags.
     */
    public function getActiveGroupsWithTags()
    {
        return TagGroup::active()
            ->with(['tags' => function ($query) {
                $query->active();
            }])
            ->orderBy('display_order')
            ->get();
    }

    /**
     * Sync tags for a model, ensuring single_select rules are respected if needed.
     * (Validation should ideally happen before this, but this is a helper).
     */
    public function syncModelTags($model, array $tagIds)
    {
        return $model->syncTags($tagIds);
    }
}
