<?php

namespace App\Livewire\Public\Lms;

use App\Models\Lms\LmsClassAssessment;
use App\Models\Lms\LmsClassAssessmentAttempt;
use App\Services\Lms\AssessmentService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class AssessmentTake extends Component
{
    public LmsClassAssessment $assessment;
    public ?LmsClassAssessmentAttempt $attempt = null;
    
    public $state = 'intro'; // intro, taking, submitted
    public $currentQuestionIndex = 0;
    public $answers = [];
    public $timeLeft = 0;
    public $timeElapsed = 0;

    public function mount(LmsClassAssessment $assessment, AssessmentService $service)
    {
        $this->assessment = $assessment->load('questions.options');
        
        if (!$assessment->is_published) {
            abort(403, 'This assessment is not yet published.');
        }

        // Check for existing attempt
        $this->attempt = LmsClassAssessmentAttempt::where('user_id', auth()->id())
            ->where('lms_class_assessment_id', $assessment->id)
            ->whereNull('submitted_at')
            ->first();

        if ($this->attempt) {
            $this->startTaking();
        }
    }

    public function startTaking()
    {
        $service = app(AssessmentService::class);
        $this->attempt = $service->startAttempt($this->assessment, auth()->user());
        
        $this->state = 'taking';
        $this->loadAnswers();
        $this->calculateTime();
    }

    protected function loadAnswers()
    {
        $this->answers = $this->attempt->answers()
            ->pluck('selected_option_id', 'question_id')
            ->toArray();
    }

    protected function calculateTime()
    {
        if (!$this->assessment->duration_minutes) {
            $this->timeLeft = -1; // Unlimited
            return;
        }

        $endTime = $this->attempt->started_at->addMinutes($this->assessment->duration_minutes);
        $this->timeLeft = now()->diffInSeconds($endTime, false);

        if ($this->timeLeft <= 0) {
            $this->submit();
        }
    }

    public function selectOption($questionId, $optionId)
    {
        if ($this->state !== 'taking') return;

        $this->answers[$questionId] = $optionId;
        
        $service = app(AssessmentService::class);
        $service->saveAnswer($this->attempt, $questionId, $optionId);
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < $this->assessment->questions->count() - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function prevQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion($index)
    {
        $this->currentQuestionIndex = $index;
    }

    public function submit()
    {
        if ($this->state !== 'taking') return;

        $service = app(AssessmentService::class);
        $service->submitAttempt($this->attempt);
        
        $this->state = 'submitted';
        
        return redirect()->route('assessment.results', $this->attempt->id);
    }

    public function render()
    {
        return view('livewire.public.lms.assessment-take')
            ->title($this->assessment->title . ' - Assessment');
    }
}
