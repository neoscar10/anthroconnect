<?php

namespace App\Livewire\Public\Lms;

use App\Models\Lms\LmsLesson;
use App\Models\Lms\LmsModule;
use App\Services\Lms\LmsPublicService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.public')]
class LessonShow extends Component
{
    public LmsLesson $lesson;
    public LmsModule $module;
    public $prevLesson;
    public $nextLesson;
    public $lessons;
    public $completedLessonIds = [];
    public $isCompleted = false;

    public function mount(string $moduleSlug, string $lessonSlug, LmsPublicService $lmsService)
    {
        $this->lesson = $lmsService->getLessonBySlug($moduleSlug, $lessonSlug);

        if (!$this->lesson) {
            abort(404);
        }

        $this->module = $this->lesson->module;

        // Protection: Redirect back to module if unauthorized
        if (!$this->lesson->canAccess(Auth::user())) {
            return redirect()->route('modules.show', $this->module->slug)
                ->with('error', 'This lecture is reserved for our scholarly community. Please upgrade your membership to gain full access.');
        }

        $nav = $lmsService->getLessonNavigation($this->lesson);
        $this->prevLesson = $nav['prev'];
        $this->nextLesson = $nav['next'];
        
        // Load curriculum for the sidebar
        $this->lessons = $this->module->lessons;

        // Load progress for authenticated scholars
        if (Auth::check()) {
            $this->completedLessonIds = $lmsService->getModuleLessonCompletionStatuses(Auth::user(), $this->module);
            $this->isCompleted = in_array($this->lesson->id, $this->completedLessonIds);
        }
    }

    /**
     * Handle manual completion click.
     */
    public function markComplete(LmsPublicService $lmsService)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($this->isCompleted) return;

        $lmsService->markLessonComplete(Auth::user(), $this->lesson);
        $this->isCompleted = true;
        
        // Refresh component state
        $this->completedLessonIds = $lmsService->getModuleLessonCompletionStatuses(Auth::user(), $this->module);
    }

    /**
     * Handle automatic completion from video player.
     */
    public function autoComplete(int $seconds, LmsPublicService $lmsService)
    {
        if (!Auth::check()) return;
        if ($this->isCompleted) return;

        $lmsService->markLessonComplete(Auth::user(), $this->lesson, $seconds);
        $this->isCompleted = true;
        
        // Refresh component state
        $this->completedLessonIds = $lmsService->getModuleLessonCompletionStatuses(Auth::user(), $this->module);
    }

    public function render()
    {
        return view('livewire.public.lms.lesson-show')
            ->title($this->lesson->title . ' - ' . $this->module->title);
    }
}
