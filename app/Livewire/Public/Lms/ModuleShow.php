<?php

namespace App\Livewire\Public\Lms;

use App\Models\Lms\LmsModule;
use App\Models\Lms\LmsLesson;
use App\Services\Lms\LmsPublicService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.public')]
class ModuleShow extends Component
{
    public LmsModule $module;
    public $lessons;
    public $resources;
    public $relatedModules;
    public $completedLessonIds = [];
    public $progress = [
        'completed_count' => 0,
        'total_count' => 0,
        'percentage' => 0,
    ];

    public function mount(string $slug, LmsPublicService $lmsService)
    {
        $this->module = $lmsService->getModuleBySlug($slug);

        if (!$this->module) {
            abort(404);
        }

        $this->lessons = $this->module->lessons;
        $this->resources = $this->module->resources;
        $this->relatedModules = $lmsService->getRelatedModules($this->module, 2);

        // Load progress for authenticated scholars
        if (Auth::check()) {
            $this->progress = $lmsService->getModuleProgress(Auth::user(), $this->module);
            $this->completedLessonIds = $lmsService->getModuleLessonCompletionStatuses(Auth::user(), $this->module);
        }
    }

    /**
     * Resolve the best entry point for the scholar to continue their journey.
     */
    public function continueJourney(LmsPublicService $lmsService)
    {
        if (!Auth::check()) {
            return $this->openLesson($this->lessons->first()->slug);
        }

        $lesson = $lmsService->getContinueLesson(Auth::user(), $this->module);
        return $this->openLesson($lesson->slug);
    }

    /**
     * Reusable gating logic for lesson access.
     */
    public function openLesson(string $lessonSlug)
    {
        $lesson = $this->lessons->where('slug', $lessonSlug)->first();

        if (!$lesson) return;

        if ($lesson->canAccess(Auth::user())) {
            return redirect()->route('lessons.show', [
                'moduleSlug' => $this->module->slug,
                'lessonSlug' => $lesson->slug
            ]);
        }

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Trigger upgrade modal for authenticated non-members
        $this->dispatch('open-upgrade-modal');
    }

    /**
     * Reusable gating logic for resource access.
     */
    public function downloadResource(int $resourceId)
    {
        $resource = $this->resources->where('id', $resourceId)->first();

        if (!$resource) return;

        if ($resource->canAccess(Auth::user())) {
            return redirect(Storage::url($resource->file_path));
        }

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Trigger upgrade modal for authenticated non-members
        $this->dispatch('open-upgrade-modal');
    }

    public function render()
    {
        return view('livewire.public.lms.module-show')
            ->title($this->module->title . ' - AnthroConnect');
    }
}
