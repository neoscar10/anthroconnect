<?php

namespace App\Http\Controllers;

use App\Services\Encyclopedia\EncyclopediaFrontendService;
use Illuminate\Http\Request;

class PublicEncyclopediaController extends Controller
{
    protected $encyclopediaService;

    public function __construct(EncyclopediaFrontendService $encyclopediaService)
    {
        $this->encyclopediaService = $encyclopediaService;
    }

    /**
     * Show the anthropologist detail page.
     */
    public function showAnthropologist(string $slug)
    {
        $anthropologist = $this->encyclopediaService->getAnthropologistBySlug($slug);

        if (!$anthropologist) {
            abort(404);
        }

        $relatedThinkers = $this->encyclopediaService->getRelatedThinkers($anthropologist, 4);
        $keyTheory = $this->encyclopediaService->getRecommendedTheory($anthropologist);
        
        // Derive contributions from related concepts first, then topics
        $contributions = $anthropologist->coreConcepts->map(function($concept) {
            return [
                'title' => $concept->title,
                'description' => $concept->short_description ?? 'A fundamental concept explored by this thinker.',
            ];
        })->take(3);

        if ($contributions->count() < 3) {
            $topicContributions = $anthropologist->topics->take(3 - $contributions->count())->map(function($topic) {
                return [
                    'title' => $topic->name,
                    'description' => "Key contributions and theoretical advancements in the field of {$topic->name}.",
                ];
            });
            $contributions = $contributions->concat($topicContributions);
        }

        return view('pages.encyclopedia.anthropologist-detail', compact('anthropologist', 'relatedThinkers', 'keyTheory', 'contributions'));
    }
    public function showTheory(string $slug)
    {
        $theory = $this->encyclopediaService->getTheoryBySlug($slug);

        if (!$theory) {
            abort(404);
        }

        $relatedTheories = $this->encyclopediaService->getRelatedTheories($theory, 3);
        
        return view('pages.encyclopedia.theory-detail', compact('theory', 'relatedTheories'));
    }

    public function showConcept(string $slug)
    {
        $concept = $this->encyclopediaService->getConceptBySlug($slug);

        if (!$concept) {
            abort(404);
        }

        $relatedConcepts = $this->encyclopediaService->getRelatedConcepts($concept, 3);
        
        return view('pages.encyclopedia.concept-detail', compact('concept', 'relatedConcepts'));
    }
}
