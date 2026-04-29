<?php

namespace App\Livewire\Admin\Exams\Submissions;

use App\Models\Exam\ExamAnswerSubmission;
use Livewire\Component;
use Livewire\WithFileUploads;

class ExamSubmissionDetail extends Component
{
    use WithFileUploads;

    public $submissionId;
    public $feedback_text;
    public $score;
    public $evaluation_attachment;
    public $existing_evaluation_attachment;

    public function mount($id)
    {
        $this->submissionId = $id;
        $submission = ExamAnswerSubmission::findOrFail($id);
        $this->feedback_text = $submission->feedback_text;
        $this->score = $submission->score;
        $this->existing_evaluation_attachment = $submission->evaluation_attachment_path;
    }

    public function saveEvaluation()
    {
        $submission = ExamAnswerSubmission::findOrFail($this->submissionId);
        
        $path = $this->existing_evaluation_attachment;
        if ($this->evaluation_attachment) {
            $path = $this->evaluation_attachment->store('evaluation-attachments', 'public');
        }

        $submission->update([
            'feedback_text' => $this->feedback_text,
            'score' => $this->score,
            'evaluation_attachment_path' => $path,
            'evaluated_at' => now(),
        ]);

        $this->existing_evaluation_attachment = $path;
        $this->evaluation_attachment = null;

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
