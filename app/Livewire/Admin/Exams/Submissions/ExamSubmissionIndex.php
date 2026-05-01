<?php

namespace App\Livewire\Admin\Exams\Submissions;

use App\Models\Exam\ExamAnswerSubmission;
use Livewire\Component;
use Livewire\WithPagination;

class ExamSubmissionIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $question_kind = '';
    public $perPage = 10;

    protected $queryString = ['search', 'status', 'question_kind'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteSubmission($id)
    {
        $submission = ExamAnswerSubmission::findOrFail($id);
        $submission->delete();
        $this->dispatch('notify', ['message' => 'Submission deleted successfully.', 'type' => 'success']);
    }

    public function render()
    {
        $submissions = ExamAnswerSubmission::with(['user', 'question'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                })->orWhereHas('question', function ($q) {
                    $q->where('question_text', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                if ($this->status === 'evaluated') {
                    $query->whereNotNull('evaluated_at');
                } elseif ($this->status === 'pending') {
                    $query->where('status', 'submitted')->whereNull('evaluated_at');
                } elseif ($this->status === 'draft') {
                    $query->where('status', 'draft');
                }
            })
            ->when($this->question_kind, function ($query) {
                $query->whereHas('question', function ($q) {
                    $q->where('question_kind', $this->question_kind);
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.exams.submissions.exam-submission-index', [
            'submissions' => $submissions
        ])->layout('layouts.admin');
    }
}
