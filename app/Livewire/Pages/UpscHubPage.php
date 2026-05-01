<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\ExploreArticle;
use App\Models\Lms\LmsModule;
use App\Models\Encyclopedia\Anthropologist;
use App\Models\Encyclopedia\CoreConcept;
use App\Models\Encyclopedia\MajorTheory;
use App\Models\LibraryResource;
use App\Models\Exam\ExamQuestion;
use App\Models\Lms\LmsLessonProgress;
use App\Models\Community\CommunityDiscussion;

class UpscHubPage extends Component
{
    public string $search = '';
    public string $section = 'all';

    /**
     * Apply search filters to the query.
     */
    private function applySearch($query)
    {
        if (trim($this->search) === '') {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('title', 'like', "%{$this->search}%")
              ->orWhere('name', 'like', "%{$this->search}%")
              ->orWhere('excerpt', 'like', "%{$this->search}%")
              ->orWhere('summary', 'like', "%{$this->search}%")
              ->orWhere('description', 'like', "%{$this->search}%");
        });
    }

    public function render()
    {
        // Calculate UPSC Progress
        $upscProgress = 0;
        $totalLessons = 0;
        $completedLessonsCount = 0;
        $nextRecommendedLesson = null;
        $recommendationText = "Start Your Journey";

        if (auth()->check()) {
            $user = auth()->user();
            
            // Get all UPSC relevant modules
            $upscModules = LmsModule::where('is_upsc_relevant', true)
                ->where('is_published', true)
                ->with(['lessons' => function($q) {
                    $q->where('is_published', true)->orderBy('sort_order');
                }])
                ->get();

            foreach ($upscModules as $module) {
                $totalLessons += $module->lessons->count();
            }

            if ($totalLessons > 0) {
                $completedLessonsCount = LmsLessonProgress::where('user_id', $user->id)
                    ->whereIn('lms_module_id', $upscModules->pluck('id'))
                    ->whereNotNull('completed_at')
                    ->count();
                
                $upscProgress = round(($completedLessonsCount / $totalLessons) * 100);
            }

            // Recommendation Logic
            // 1. Check for any lesson currently "In Progress" (watched but not completed)
            $inProgress = LmsLessonProgress::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->where('watched_seconds', '>', 0)
                ->latest('last_watched_at')
                ->first();

            if ($inProgress && $inProgress->lesson) {
                $nextRecommendedLesson = $inProgress->lesson;
                $recommendationText = "Continue: " . $nextRecommendedLesson->title;
            } else {
                // 2. Find the first uncompleted lesson in order
                foreach ($upscModules as $module) {
                    $next = $module->lessons->first(function($lesson) use ($user) {
                        return !LmsLessonProgress::where('user_id', $user->id)
                            ->where('lms_lesson_id', $lesson->id)
                            ->whereNotNull('completed_at')
                            ->exists();
                    });

                    if ($next) {
                        $nextRecommendedLesson = $next;
                        $recommendationText = "Next Step: " . $nextRecommendedLesson->title;
                        break;
                    }
                }
            }
        } else {
            // Guest recommendation: First published UPSC module
            $firstModule = LmsModule::where('is_upsc_relevant', true)
                ->where('is_published', true)
                ->first();
            if ($firstModule) {
                $recommendationText = "Explore Foundation: " . $firstModule->title;
                // For guest, link to module index if lesson route is sensitive, 
                // but we can use the first lesson if it's a preview.
                $nextRecommendedLesson = $firstModule->lessons()->where('is_published', true)->first();
            }
        }

        // Fetch UPSC relevant modules for display
        $modules = $this->applySearch(
            LmsModule::where('is_upsc_relevant', true)
                ->where('is_published', true)
                ->latest()
        )->take(6)->get();

        // Fetch UPSC relevant explore items (articles)
        $exploreItems = $this->applySearch(
            ExploreArticle::where('is_upsc_relevant', true)
                ->published()
                ->latest()
        )->take(6)->get();

        // Fetch UPSC relevant anthropologists
        $anthropologists = $this->applySearch(
            Anthropologist::where('is_upsc_relevant', true)
                ->latest()
        )->take(4)->get();

        // Fetch UPSC relevant core concepts
        $concepts = $this->applySearch(
            CoreConcept::where('is_upsc_relevant', true)
                ->latest()
        )->take(6)->get();

        // Fetch UPSC relevant major theories
        $theories = $this->applySearch(
            MajorTheory::where('is_upsc_relevant', true)
                ->latest()
        )->take(6)->get();

        // Fetch UPSC relevant library resources
        $resources = $this->applySearch(
            LibraryResource::where('is_upsc_relevant', true)
                ->latest()
        )->take(5)->get();

        // Fetch Practice Questions (Model Kind)
        $practiceQuestions = ExamQuestion::where('question_kind', 'model')
            ->published()
            ->latest()
            ->take(3)
            ->get();

        // Fetch Past Questions (Recent PYQs)
        $pastQuestions = ExamQuestion::where('question_kind', 'past')
            ->published()
            ->latest()
            ->take(3)
            ->get();

        // Fetch Recent Discussions
        $recentDiscussions = CommunityDiscussion::with(['author'])
            ->latest()
            ->take(2)
            ->get();

        return view('livewire.pages.upsc-hub-page', [
            'modules' => $modules,
            'exploreItems' => $exploreItems,
            'anthropologists' => $anthropologists,
            'concepts' => $concepts,
            'theories' => $theories,
            'resources' => $resources,
            'practiceQuestions' => $practiceQuestions,
            'pastQuestions' => $pastQuestions,
            'recentDiscussions' => $recentDiscussions,
            'upscProgress' => $upscProgress,
            'recommendationText' => $recommendationText,
            'nextRecommendedLesson' => $nextRecommendedLesson,
            'isGuest' => !auth()->check()
        ])->layout('layouts.public');
    }
}
