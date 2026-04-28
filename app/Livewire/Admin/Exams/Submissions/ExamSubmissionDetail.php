<?php

namespace App\Livewire\Admin\Exams\Submissions;

use App\Models\Exam\ExamAnswerSubmission;
use Livewire\Component;

class ExamSubmissionDetail extends Component
{
    public $submissionId;
    public $feedback_text;
    public $score;

    public function mount($id)
    {
        $this->submissionId = $id;
        $submission = ExamAnswerSubmission::findOrFail($id);
        $this->feedback_text = $submission->feedback_text;
        $this->score = $submission->score;
    }

    public function saveEvaluation()
    {
        $submission = ExamAnswerSubmission::findOrFail($this->submissionId);
        
        $submission->update([
            'feedback_text' => $this->feedback_text,
            'score' => $this->score,
            'evaluated_at' => now(),
        ]);

        $this->dispatch('notify', ['message' => 'Evaluation saved successfully.', 'type' => 'success']);
    }

    public function render()
    {
        $submission = ExamAnswerSubmission::with(['user', 'question'])->findOrFail($this->submissionId);

        return view('livewire.admin.exams.submissions.exam-submission-detail', [
            'submission' => $submission
        ])->layout('layouts.admin');
    }
}
