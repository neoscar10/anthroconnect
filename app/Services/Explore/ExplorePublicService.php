<?php

namespace App\Services\Explore;

use App\Models\ExploreArticle;
use App\Models\Topic;

class ExplorePublicService
{
    /**
     * Retrieve all active topics ordered by name.
     */
    public function getPublicTopics()
    {
        return Topic::active()->orderBy('name')->get();
    }

    /**
     * Retrieve the single featured article.
     * If a topic ID is provided, scope the featured article to that topic.
     * If no featured article exists, fall back to the most recent published article.
     */
    public function getFeaturedArticle($topicId = null)
    {
        $query = ExploreArticle::with(['topic', 'creator'])
            ->published()
            ->orderByDesc('published_at');

        if ($topicId) {
            $query->where('topic_id', $topicId);
        }

        // Clone to act as a fallback
        $fallbackQuery = clone $query;

        // Try getting the featured one first
        $featured = $query->featured()->first();

        return $featured ?? $fallbackQuery->first();
    }

    /**
     * Retrieve paginated published articles excluding the featured one.
     */
    public function getPublishedArticles($filters = [])
    {
        $query = ExploreArticle::with(['topic', 'creator'])
            ->published()
            ->orderByDesc('published_at');

        if (!empty($filters['topic_id'])) {
            $query->where('topic_id', $filters['topic_id']);
        }

        return $query->paginate(5);
    }
}
