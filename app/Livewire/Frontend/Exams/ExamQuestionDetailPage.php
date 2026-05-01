<?php

namespace App\Livewire\Frontend\Exams;

use App\Models\Exam\ExamQuestion;
use App\Models\Exam\ExamAnswerSubmission;
use App\Services\Exam\ExamQuestionFrontendService;
use App\Services\Exam\ExamAnswerSubmissionService;
use Livewire\Component;
use Livewire\WithFileUploads;

class ExamQuestionDetailPage extends Component
{
    use WithFileUploads;

    public $slug;
    public $answer_text = '';
    public $submission_type = 'text'; // 'text' or 'file'
    public $attachment;
    public $attachment_path = null;
    public $time_spent_seconds = 0;
    public $target_time_minutes = 15;
    public $attempts_count = 1;
    public $is_started = false;
    public $show_guidelines = false;
    public $show_model_answer = false;
    public $is_submitted = false;
    public $last_saved_at = null;
    
    // Peer Evaluation
    public $comment_content = '';
    
    // Active submission we are working on or viewing
    public $active_submission_id = null;
    public $is_past_question = false;

    public function startExam()
    {
        if ($this->is_past_question) return;
        $this->is_started = true;
        $this->dispatch('exam-started');
    }

    protected $listeners = ['tick' => 'incrementTimer'];

    public function mount($slug, ExamQuestionFrontendService $questionService, ExamAnswerSubmissionService $submissionService)
    {
        $this->slug = $slug;
        $question = $questionService->findPublishedBySlug($slug);
        $this->is_past_question = $question->question_kind === 'past';
        
        if ($this->is_past_question) {
            $this->show_model_answer = true;
        }

        if (auth()->check()) {
            $submission = $submissionService->getActiveSubmission($question, auth()->user());
            $this->loadSubmissionData($submission);
        }
    }

    public function updatedAttachment()
    {
        $this->validate([
            'attachment' => 'required|max:10240|mimes:jpg,jpeg,png,pdf',
        ]);
    }

    public function loadSubmissionData(ExamAnswerSubmission $submission)
    {
        $this->active_submission_id = $submission->id;
        $this->answer_text = $submission->answer_text ?? '';
        $this->submission_type = $submission->submission_type ?? 'text';
        $this->attachment_path = $submission->attachment_path;
        $this->time_spent_seconds = $submission->time_spent_seconds ?? 0;
        $this->target_time_minutes = $submission->target_time_minutes ?? 15;
        $this->attempts_count = $submission->attempts_count ?? 1;
        $this->is_submitted = $submission->status === 'submitted';
        
        // If it's a draft, it could be "started" but locked if we just saved it.
        // We'll reset is_started to false to force the "Resume" button check.
        $this->is_started = false;

        // Reset UI components
        $this->dispatch('exam-reset', ['answer' => $this->answer_text]);
    }

    public function selectSubmission($id)
    {
        $submission = ExamAnswerSubmission::findOrFail($id);
        $this->loadSubmissionData($submission);
    }

    public function incrementTimer($seconds = 1)
    {
        if (!$this->is_submitted) {
            $this->time_spent_seconds += $seconds;
        }
    }

    public function saveDraft(ExamQuestionFrontendService $questionService, ExamAnswerSubmissionService $submissionService)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $question = $questionService->findPublishedBySlug($this->slug);
        
        if (!$question->isAccessibleBy(auth()->user())) {
            return $this->dispatch('open-upgrade-modal');
        }

        $path = $this->attachment_path;
        if ($this->attachment) {
            $path = $this->attachment->store('exam-attachments', 'public');
            $this->attachment_path = $path;
            $this->attachment = null; // Clear from memory
        }

        $submissionService->saveDraft($question, auth()->user(), [
            'answer_text' => $this->answer_text,
            'submission_type' => $this->submission_type,
            'attachment_path' => $path,
            'time_spent_seconds' => $this->time_spent_seconds,
            'target_time_minutes' => $this->target_time_minutes
        ]);

        $this->last_saved_at = now()->format('H:i:s');
        $this->is_started = false;
        
        $this->dispatch('notify', ['message' => 'Draft saved and session locked.', 'type' => 'success']);
    }

    public function submitAnswer(ExamQuestionFrontendService $questionService, ExamAnswerSubmissionService $submissionService)
    {
        // For text submissions, we require the session to be "started" (timer running)
        // For file submissions, we allow direct submission without starting the timer
        if ($this->submission_type === 'text' && !$this->is_started) {
            return;
        }

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $question = $questionService->findPublishedBySlug($this->slug);
        
        if (!$question->isAccessibleBy(auth()->user())) {
            return $this->dispatch('open-upgrade-modal');
        }

        // Validation for file if type is file
        if ($this->submission_type === 'file' && !$this->attachment && !$this->attachment_path) {
            return $this->dispatch('notify', ['message' => 'Please upload an attachment for file submission.', 'type' => 'error']);
        }

        $path = $this->attachment_path;
        if ($this->attachment) {
            $path = $this->attachment->store('exam-attachments', 'public');
            $this->attachment_path = $path;
        }

        $submissionService->submitAnswer($question, auth()->user(), [
            'answer_text' => $this->answer_text,
            'submission_type' => $this->submission_type,
            'attachment_path' => $path,
            'time_spent_seconds' => $this->time_spent_seconds,
            'target_time_minutes' => $this->target_time_minutes
        ]);

        $this->is_submitted = true;
        $this->is_started = false; // Ensure it's locked
        
        $this->dispatch('notify', ['message' => 'Answer submitted successfully.', 'type' => 'success']);
    }

    public function retakeExam(ExamQuestionFrontendService $questionService, ExamAnswerSubmissionService $submissionService)
    {
        if (!auth()->check()) return;

        $question = $questionService->findPublishedBySlug($this->slug);
        $submission = $submissionService->retake($question, auth()->user());

        $this->loadSubmissionData($submission);
        
        $this->dispatch('notify', ['message' => 'New practice session started. Attempt #' . $this->attempts_count, 'type' => 'success']);
    }

    public function addComment(ExamQuestionFrontendService $service)
    {
        $this->validate([
            'comment_content' => 'required|min:3|max:1000',
        ]);

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $question = $service->findPublishedBySlug($this->slug);

        \App\Models\Exam\ExamQuestionComment::create([
            'user_id' => auth()->id(),
            'exam_question_id' => $question->id,
            'content' => $this->comment_content,
        ]);

        $this->comment_content = '';
        $this->dispatch('notify', ['message' => 'Peer evaluation posted successfully.', 'type' => 'success']);
    }

    public function render(ExamQuestionFrontendService $service)
    {
        $question = $service->findPublishedBySlug($this->slug);
        $restriction = $question->getRestrictionStateFor(auth()->user());
        
        $submission = null;
        $allSubmissions = collect();

        if (auth()->check()) {
            $submission = ExamAnswerSubmission::find($this->active_submission_id);
            $allSubmissions = auth()->user()->examSubmissions()
                ->where('exam_question_id', $question->id)
                ->latest()
                ->get();
        }

        $relatedQuestions = $service->relatedQuestions($question);
        $next = $service->nextQuestion($question);
        $prev = $service->previousQuestion($question);

        $comments = \App\Models\Exam\ExamQuestionComment::where('exam_question_id', $question->id)
            ->with('user')
            ->latest()
            ->get();

        return view('livewire.frontend.exams.exam-question-detail-page', [
            'question' => $question,
            'restriction' => $restriction,
            'submission' => $submission,
            'allSubmissions' => $allSubmissions,
            'relatedQuestions' => $relatedQuestions,
            'next' => $next,
            'prev' => $prev,
            'comments' => $comments,
        ])->layout('layouts.public', ['title' => $question->title ?: 'Practice Question']);
    }
}
