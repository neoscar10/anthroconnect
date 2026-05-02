<?php

namespace App\Livewire\Public\Lms;

use App\Models\Lms\LmsClassAssessmentAttempt;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class AssessmentResults extends Component
{
    public LmsClassAssessmentAttempt $attempt;

    public function mount(LmsClassAssessmentAttempt $attempt)
    {
        $this->attempt = $attempt->load(['assessment.questions.options', 'answers.question.options', 'answers.option']);
        
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$attempt->submitted_at) {
            return redirect()->route('assessment.take', $attempt->lms_class_assessment_id);
        }
    }

    public function render()
    {
        return view('livewire.public.lms.assessment-results')
            ->title('Results: ' . $this->attempt->assessment->title);
    }
}
