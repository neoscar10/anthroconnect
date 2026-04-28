<?php

namespace App\Services\Explore;

use App\Models\ExploreArticle;
use App\Models\Topic;

class ExplorePublicService
{
    /**
     * Retrieve active tag groups that should be shown as filters.
     */
    public function getPublicTagGroups()
    {
        return \App\Models\TagGroup::getGroupsWithUsage(ExploreArticle::class);
    }

    public function getFeaturedArticle($topicId = null)
    {
        return $this->getFeaturedArticles($topicId)->first();
    }

    /**
     * Retrieve all featured articles.
     * If no featured articles exist, fall back to the most recent published article.
     */
    public function getFeaturedArticles($tagId = null)
    {
        $query = ExploreArticle::with(['tags', 'creator'])
            ->published()
            ->orderBy('sort_order', 'asc');

        if ($tagId) {
            $query->withTag($tagId);
        }

        $featured = (clone $query)->featured()->get();

        if ($featured->isEmpty()) {
            return $query->limit(1)->get();
        }

        return $featured;
    }

    /**
     * Retrieve paginated published articles excluding the featured one.
     */
    public function getPublishedArticles($filters = [])
    {
        $query = ExploreArticle::with(['tags', 'creator'])
            ->published()
            ->orderBy('sort_order', 'asc');

        if (!empty($filters['tag_id'])) {
            $query->withTag($filters['tag_id']);
        }

        if (!empty($filters['exclude_ids'])) {
            $query->whereNotIn('id', $filters['exclude_ids']);
        }

        return $query->paginate(6);
    }

    /**
     * Retrieve a single published article by slug.
     */
    public function getArticleBySlug(string $slug): ?ExploreArticle
    {
        $article = ExploreArticle::with(['tags', 'creator'])
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
        // Get articles sharing any tag with current one
        $tagIds = $article->tags->pluck('id');

        $related = ExploreArticle::with(['tags', 'creator'])
            ->published()
            ->where('id', '!=', $article->id)
            ->whereHas('tags', function($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            })
            ->orderBy('sort_order', 'asc')
            ->limit($limit)
            ->get();

        // If not enough related articles, backfill with latest published
        if ($related->count() < $limit) {
            $takenIds = $related->pluck('id')->push($article->id);
            
            $backfill = ExploreArticle::with(['tags', 'creator'])
                ->published()
                ->whereNotIn('id', $takenIds)
                ->orderBy('sort_order', 'asc')
                ->limit($limit - $related->count())
                ->get();

            $related = $related->concat($backfill);
        }

        return $related;
    }
}
