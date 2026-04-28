<?php

namespace App\Services\Encyclopedia;

use App\Models\Encyclopedia\Anthropologist;
use App\Models\Encyclopedia\CoreConcept;
use App\Models\Encyclopedia\MajorTheory;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

class EncyclopediaFrontendService
{
    public function getPublicTagGroups()
    {
        // Combined usage for Encyclopedia (Anthropologists, CoreConcepts, MajorTheories)
        $groupIds = \Illuminate\Support\Facades\DB::table('taggables')
            ->join('tags', 'taggables.tag_id', '=', 'tags.id')
            ->whereIn('taggables.taggable_type', [
                Anthropologist::class,
                CoreConcept::class,
                MajorTheory::class
            ])
            ->distinct()
            ->pluck('tags.tag_group_id');

        return \App\Models\TagGroup::whereIn('id', $groupIds)
            ->active()
            ->with(['activeTags' => fn($q) => $q->orderBy('name')])
            ->orderBy('display_order')
            ->get();
    }

    /**
     * Get featured anthropologists for the landing page.
     */
    public function getFeaturedAnthropologists(int $limit = 3)
    {
        return Anthropologist::with(['tags'])
            ->where('status', 'active')
            ->orderBy('is_featured', 'desc')
            ->orderBy('full_name', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get core concepts for the landing page.
     */
    public function getCoreConcepts(int $limit = 4)
    {
        return CoreConcept::where('status', 'active')
            ->orderBy('title', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get major theories for the landing page.
     */
    public function getMajorTheories(int $limit = 3)
    {
        return MajorTheory::where('status', 'active')
            ->orderBy('title', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search and filter encyclopedia content.
     */
    public function search(array $params = [])
    {
        $search = $params['search'] ?? '';
        $category = $params['category'] ?? 'All Categories';
        $tags = $params['tags'] ?? [];
        $region = $params['region'] ?? 'Global';

        $results = [
            'anthropologists' => collect(),
            'concepts' => collect(),
            'theories' => collect(),
        ];

        // 1. Anthropologists
        if ($category === 'All Categories' || $category === 'Anthropologists') {
            $query = Anthropologist::where('status', 'active');
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('summary', 'like', "%{$search}%");
                });
            }

            if (!empty($params['tag_filters']) && is_array($params['tag_filters'])) {
                foreach ($params['tag_filters'] as $groupId => $slug) {
                    if ($slug) $query->withTag($slug);
                }
            }

            if ($region !== 'Global') {
                $query->where('nationality', 'like', "%{$region}%");
            }

            $results['anthropologists'] = $query->orderBy('full_name')->get();
        }

        // 2. Concepts
        if ($category === 'All Categories' || $category === 'Concepts') {
            $query = CoreConcept::where('status', 'active');
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('short_description', 'like', "%{$search}%");
                });
            }

            if (!empty($params['tag_filters']) && is_array($params['tag_filters'])) {
                foreach ($params['tag_filters'] as $groupId => $slug) {
                    if ($slug) $query->withTag($slug);
                }
            }

            $results['concepts'] = $query->orderBy('title')->get();
        }

        // 3. Theories
        if ($category === 'All Categories' || $category === 'Theories') {
            $query = MajorTheory::where('status', 'active');
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('short_description', 'like', "%{$search}%")
                      ->orWhere('key_thinkers_text', 'like', "%{$search}%");
                });
            }

            if (!empty($params['tag_filters']) && is_array($params['tag_filters'])) {
                foreach ($params['tag_filters'] as $groupId => $slug) {
                    if ($slug) $query->withTag($slug);
                }
            }

            $results['theories'] = $query->orderBy('title')->get();
        }

        return $results;
    }

    /**
     * Get available disciplines for filtering.
     */
    public function getDisciplines()
    {
        return \App\Models\TagGroup::where('slug', 'topics')->first()?->tags()->active()->orderBy('name')->pluck('name') ?? collect();
    }

    /**
     * Get available regions/nationalities for filtering.
     */
    public function getRegions()
    {
        return Anthropologist::whereNotNull('nationality')
            ->distinct()
            ->pluck('nationality')
            ->filter()
            ->values();
    }
    /**
     * Get a single anthropologist by slug with relationships.
     */
    public function getAnthropologistBySlug(string $slug): ?Anthropologist
    {
        return Anthropologist::with(['tags'])
            ->where('status', 'active')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get a single theory by slug.
     */
    public function getTheoryBySlug(string $slug): ?MajorTheory
    {
        return MajorTheory::where('status', 'active')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get a single concept by slug.
     */
    public function getConceptBySlug(string $slug): ?CoreConcept
    {
        return CoreConcept::with('anthropologists')
            ->where('status', 'active')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get related theories.
     */
    public function getRelatedTheories(MajorTheory $theory, int $limit = 4)
    {
        return MajorTheory::where('status', 'active')
            ->where('id', '!=', $theory->id)
            ->limit($limit)
            ->get();
    }

    /**
     * Get related concepts.
     */
    public function getRelatedConcepts(CoreConcept $concept, int $limit = 4)
    {
        return CoreConcept::where('status', 'active')
            ->where('id', '!=', $concept->id)
            ->limit($limit)
            ->get();
    }

    /**
     * Get related thinkers based on shared topics or concepts.
     */
    public function getRelatedThinkers(Anthropologist $person, int $limit = 4)
    {
        $tagIds = $person->tags->pluck('id')->toArray();

        $related = Anthropologist::where('status', 'active')
            ->where('id', '!=', $person->id)
            ->where(function($query) use ($tagIds) {
                if (!empty($tagIds)) {
                    $query->whereHas('tags', function($q) use ($tagIds) {
                        $q->whereIn('tags.id', $tagIds);
                    });
                }
            })
            ->limit($limit)
            ->get();

        // Fallback to featured anthropologists if we don't have enough related ones
        if ($related->count() < $limit) {
            $takenIds = $related->pluck('id')->push($person->id);
            
            $backfill = Anthropologist::where('status', 'active')
                ->whereNotIn('id', $takenIds)
                ->orderBy('is_featured', 'desc')
                ->limit($limit - $related->count())
                ->get();

            $related = $related->concat($backfill);
        }

        return $related;
    }

    /**
     * Infer a matching major theory based on topics or specialization keywords.
     */
    public function getRecommendedTheory(Anthropologist $person): ?MajorTheory
    {
        $keywords = $person->tags->pluck('name')->toArray();

        if (empty($keywords)) return null;

        $query = MajorTheory::where('status', 'active');
        
        $query->where(function($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $q->orWhere('title', 'like', "%{$keyword}%")
                  ->orWhere('short_description', 'like', "%{$keyword}%");
            }
        });

        return $query->first();
    }
}
