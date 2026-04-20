<?php

namespace App\Services\Encyclopedia;

use App\Models\Encyclopedia\Anthropologist;
use App\Models\Encyclopedia\CoreConcept;
use App\Models\Encyclopedia\MajorTheory;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

class EncyclopediaFrontendService
{
    /**
     * Get featured anthropologists for the landing page.
     */
    public function getFeaturedAnthropologists(int $limit = 3)
    {
        return Anthropologist::where('status', 'active')
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
        $discipline = $params['discipline'] ?? 'All Disciplines';
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

            if ($discipline !== 'All Disciplines') {
                $query->whereHas('topics', function($q) use ($discipline) {
                    $q->where('name', $discipline);
                });
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

            $results['theories'] = $query->orderBy('title')->get();
        }

        return $results;
    }

    /**
     * Get available disciplines for filtering.
     */
    public function getDisciplines()
    {
        return Topic::whereHas('anthropologists', function($q) {
                $q->where('status', 'active');
            })
            ->active()
            ->orderBy('name')
            ->pluck('name');
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
        return Anthropologist::with(['topics', 'coreConcepts'])
            ->where('status', 'active')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get related thinkers based on shared topics or concepts.
     */
    public function getRelatedThinkers(Anthropologist $person, int $limit = 4)
    {
        $topicIds = $person->topics->pluck('id');
        $conceptIds = $person->coreConcepts->pluck('id');

        $related = Anthropologist::where('status', 'active')
            ->where('id', '!=', $person->id)
            ->where(function($query) use ($topicIds, $conceptIds) {
                if ($topicIds->isNotEmpty()) {
                    $query->whereHas('topics', function($q) use ($topicIds) {
                        $q->whereIn('topics.id', $topicIds);
                    });
                }
                if ($conceptIds->isNotEmpty()) {
                    $query->orWhereHas('coreConcepts', function($q) use ($conceptIds) {
                        $q->whereIn('encyclopedia_core_concepts.id', $conceptIds);
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
        $keywords = $person->topics->pluck('name')->toArray();
        if ($person->discipline_or_specialization) {
            $keywords[] = $person->discipline_or_specialization;
        }

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
