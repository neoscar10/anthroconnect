<?php

namespace App\Services\Library;

use App\Models\LibraryResource;
use App\Models\LibraryResourceType;
use App\Models\LibraryRegion;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LibraryFrontendService
{
    public function basePublishedQuery(): Builder
    {
        return LibraryResource::query()
            ->with(['resourceType', 'region', 'topics', 'tags'])
            ->where('status', 'published')
            ->where(function (Builder $query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function isPublished(LibraryResource $resource): bool
    {
        return $resource->status === 'published'
            && (is_null($resource->published_at) || $resource->published_at <= now());
    }

    public function getFeaturedResources(int $limit = 3): Collection
    {
        return $this->basePublishedQuery()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    public function getLatestResources(int $limit = 6): Collection
    {
        return $this->basePublishedQuery()
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function getRecommendedResources(?User $user, int $limit = 3): Collection
    {
        return $this->basePublishedQuery()
            ->where('is_recommended', true)
            ->orderBy('sort_order')
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    public function getBrowseTopics(int $limit = 8): Collection
    {
        return Topic::query()
            ->withCount(['libraryResources as published_resources_count' => function (Builder $query) {
                $query->where('status', 'published')
                    ->where(function (Builder $subQuery) {
                        $subQuery->whereNull('published_at')
                            ->orWhere('published_at', '<=', now());
                    });
            }])
            ->active()
            ->orderByDesc('published_resources_count')
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    public function getResourceTypes(): Collection
    {
        return LibraryResourceType::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function getRegions(): Collection
    {
        return LibraryRegion::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function getPublicationYears(): Collection
    {
        return LibraryResource::query()
            ->whereNotNull('publication_year')
            ->where('status', 'published')
            ->distinct()
            ->orderByDesc('publication_year')
            ->pluck('publication_year');
    }

    public function searchResources(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->basePublishedQuery();

        $search = trim($filters['search'] ?? '');

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('abstract', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('author_display', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhereHas('resourceType', fn (Builder $r) => $r->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('region', fn (Builder $r) => $r->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('topics', fn (Builder $t) => $t->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('tags', fn (Builder $t) => $t->where('name', 'like', "%{$search}%"));
            });
        }

        if (!empty($filters['type'])) {
            $query->whereHas('resourceType', fn (Builder $q) => $q->where('slug', $filters['type']));
        }

        if (!empty($filters['region'])) {
            $query->whereHas('region', fn (Builder $q) => $q->where('slug', $filters['region']));
        }

        if (!empty($filters['topic'])) {
            $query->whereHas('topics', fn (Builder $q) => $q->where('slug', $filters['topic']));
        }

        if (!empty($filters['year'])) {
            $query->where('publication_year', $filters['year']);
        }

        match ($filters['sort'] ?? 'latest') {
            'oldest' => $query->orderBy('published_at')->orderBy('created_at'),
            'title' => $query->orderBy('title'),
            'year_asc' => $query->orderBy('publication_year'),
            'year_desc' => $query->orderByDesc('publication_year'),
            'featured' => $query->orderByDesc('is_featured')->latest('published_at'),
            default => $query->latest('published_at')->latest('created_at'),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    public function getRelatedResources(LibraryResource $resource, int $limit = 4): Collection
    {
        $topicIds = $resource->topics->pluck('id')->filter()->values();

        return $this->basePublishedQuery()
            ->whereKeyNot($resource->id)
            ->when($topicIds->isNotEmpty(), function (Builder $query) use ($topicIds) {
                $query->whereHas('topics', fn (Builder $q) => $q->whereIn('topics.id', $topicIds));
            })
            ->limit($limit)
            ->get();
    }

    public function getRelatedLearning(LibraryResource $resource): Collection
    {
        return $resource->relatedLearningItems()
            ->orderBy('sort_order')
            ->limit(4)
            ->get();
    }

    public function getRelatedDiscussions(LibraryResource $resource): Collection
    {
        return collect();
    }
}
