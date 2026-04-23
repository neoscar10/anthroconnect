<?php

namespace App\Services\Library;

use App\Models\LibraryResource;
use App\Models\Topic;
use Illuminate\Support\Facades\Cache;

class LibraryPublicService
{
    /**
     * Get featured resources for the library home.
     */
    public function getFeaturedResources($limit = 3)
    {
        return LibraryResource::published()
            ->featured()
            ->with(['resourceType'])
            ->orderBy('sort_order')
            ->latest('published_at')
            ->take($limit)
            ->get();
    }

    /**
     * Get latest published research papers.
     */
    public function getLatestResources($limit = 5)
    {
        return LibraryResource::published()
            ->with(['resourceType', 'region'])
            ->latest('published_at')
            ->take($limit)
            ->get();
    }

    /**
     * Get recommended resources (manually curated or based on history).
     */
    public function getRecommendedResources($limit = 2)
    {
        return LibraryResource::published()
            ->where('is_recommended', true)
            ->with(['resourceType'])
            ->latest('published_at')
            ->take($limit)
            ->get();
    }

    /**
     * Get topics used in library for "Browse by Topic".
     */
    public function getBrowseTopics()
    {
        // For now, return topics that have resources
        return Topic::active()
            ->whereHas('communityDiscussions') // placeholder logic or use a specific library count
            ->take(4)
            ->get();
    }

    /**
     * Search and filter library resources.
     */
    public function searchResources(array $filters = [], $perPage = 12)
    {
        $query = LibraryResource::published()
            ->with(['resourceType', 'region', 'tags']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $search = $filters['search'];
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%")
                  ->orWhere('author_display', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type'])) {
            $query->whereHas('resourceType', fn($q) => $q->where('slug', $filters['type']));
        }



        if (!empty($filters['region'])) {
            $query->whereHas('region', fn($q) => $q->where('slug', $filters['region']));
        }

        if (!empty($filters['year'])) {
            $query->where('publication_year', $filters['year']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get full details for a resource by slug.
     */
    public function getResourceBySlug($slug)
    {
        return LibraryResource::published()
            ->where('slug', $slug)
            ->with(['resourceType', 'region', 'topics', 'tags', 'relatedLearningItems.linkable'])
            ->firstOrFail();
    }

    /**
     * Get similar resources for the "More Resources" block.
     */
    public function getMoreResources(LibraryResource $resource, $limit = 2)
    {
        // First try explicit related resources
        $explicit = $resource->relatedResources()->published()->take($limit)->get();
        
        if ($explicit->count() >= $limit) return $explicit;

        // Fallback: Same discipline or topics
        $fallback = LibraryResource::published()
            ->where('id', '!=', $resource->id)
            ->where(function($q) use ($resource) {
                $q->whereHas('topics', function($sq) use ($resource) {
                      $sq->whereIn('topics.id', $resource->topics->pluck('id'));
                  });
            })
            ->take($limit - $explicit->count())
            ->get();

        return $explicit->concat($fallback);
    }
}
