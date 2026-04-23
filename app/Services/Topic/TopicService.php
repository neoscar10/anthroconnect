<?php

namespace App\Services\Topic;

use App\Models\Topic;
use Illuminate\Support\Str;
use Exception;

class TopicService
{
    /**
     * Create a new topic.
     */
    public function createTopic(array $data): Topic
    {
        return Topic::create($data);
    }

    /**
     * Update an existing topic.
     */
    public function updateTopic(Topic $topic, array $data): Topic
    {
        $topic->update($data);
        return $topic;
    }

    /**
     * Delete a topic with safety checks.
     */
    public function deleteTopic(Topic $topic): bool
    {
        if ($topic->exploreArticles()->exists()) {
            // Safer production behavior: throw exception or handle deactivation elsewhere
            throw new Exception("This topic is currently linked to editorial content and cannot be deleted. Please reassign or archive those items first.");
        }
        return $topic->delete();
    }

    /**
     * List topics for selection.
     */
    public function getTopicsForSelection()
    {
        return Topic::orderBy('name', 'asc')->active()->get();
    }

    /**
     * Search and list topics for admin.
     */
    public function listTopicsForAdmin(array $filters = [])
    {
        $query = Topic::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('slug', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->latest();
    }
}
