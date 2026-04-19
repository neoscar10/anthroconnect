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

    /**
     * Retrieve a single published article by slug.
     */
    public function getArticleBySlug(string $slug): ?ExploreArticle
    {
        $article = ExploreArticle::with(['topic', 'creator'])
            ->published()
            ->where('slug', $slug)
            ->first();

        if ($article) {
            // Render markdown if HTML is not already stored
            if (empty($article->rendered_content_html) && !empty($article->markdown_content)) {
                $article->rendered_content_html = \Illuminate\Support\Str::markdown($article->markdown_content);
            }
        }

        return $article;
    }

    /**
     * Retrieve related articles, prioritizing the same topic.
     */
    public function getRelatedArticles(ExploreArticle $article, int $limit = 2)
    {
        // Get articles within same topic excluding current one
        $related = ExploreArticle::with(['topic', 'creator'])
            ->published()
            ->where('topic_id', $article->topic_id)
            ->where('id', '!=', $article->id)
            ->limit($limit)
            ->get();

        // If not enough related articles, backfill with latest published
        if ($related->count() < $limit) {
            $takenIds = $related->pluck('id')->push($article->id);
            
            $backfill = ExploreArticle::with(['topic', 'creator'])
                ->published()
                ->whereNotIn('id', $takenIds)
                ->orderByDesc('published_at')
                ->limit($limit - $related->count())
                ->get();

            $related = $related->concat($backfill);
        }

        return $related;
    }
}
