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
}
