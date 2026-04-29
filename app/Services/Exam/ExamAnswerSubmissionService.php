<?php

namespace App\Services\Exam;

use App\Models\Exam\ExamQuestion;
use App\Models\Exam\ExamAnswerSubmission;
use App\Models\User;

class ExamAnswerSubmissionService
{
    /**
     * Get the current active submission for a user on a question.
     * Returns the latest record (draft or submitted).
     */
    public function getActiveSubmission(ExamQuestion $question, User $user): ExamAnswerSubmission
    {
        $latest = ExamAnswerSubmission::where('exam_question_id', $question->id)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$latest) {
            return ExamAnswerSubmission::create([
                'exam_question_id' => $question->id,
                'user_id' => $user->id,
                'status' => 'draft',
                'answer_text' => '',
                'attempts_count' => 1,
            ]);
        }

        return $latest;
    }

    public function saveDraft(ExamQuestion $question, User $user, array $data): ExamAnswerSubmission
    {
        $submission = $this->getActiveSubmission($question, $user);

        if ($submission->status === 'submitted') {
            return $submission;
        }

        $answerText = $data['answer_text'] ?? '';
        
        $updateData = [
            'submission_type' => $data['submission_type'] ?? 'text',
            'attachment_path' => $data['attachment_path'] ?? $submission->attachment_path,
            'answer_text' => $answerText,
            'word_count' => $this->calculateWordCount($answerText),
            'character_count' => mb_strlen(strip_tags($answerText)),
            'time_spent_seconds' => $data['time_spent_seconds'] ?? 0,
            'target_time_minutes' => $data['target_time_minutes'] ?? 15,
            'status' => 'draft',
        ];

        $submission->update($updateData);

        return $submission;
    }

    public function submitAnswer(ExamQuestion $question, User $user, array $data): ExamAnswerSubmission
    {
        $submission = $this->getActiveSubmission($question, $user);

        if ($submission->status === 'submitted') {
            return $submission;
        }

        $answerText = $data['answer_text'] ?? '';

        $submission->update([
            'submission_type' => $data['submission_type'] ?? 'text',
            'attachment_path' => $data['attachment_path'] ?? $submission->attachment_path,
            'answer_text' => $answerText,
            'word_count' => $this->calculateWordCount($answerText),
            'character_count' => mb_strlen(strip_tags($answerText)),
            'time_spent_seconds' => $data['time_spent_seconds'] ?? 0,
            'target_time_minutes' => $data['target_time_minutes'] ?? 15,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return $submission;
    }

    /**
     * Create a completely new submission record for a retake.
     */
    public function retake(ExamQuestion $question, User $user): ExamAnswerSubmission
    {
        $latest = ExamAnswerSubmission::where('exam_question_id', $question->id)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $nextAttempt = ($latest ? $latest->attempts_count : 0) + 1;

        return ExamAnswerSubmission::create([
            'exam_question_id' => $question->id,
            'user_id' => $user->id,
            'status' => 'draft',
            'answer_text' => '',
            'attempts_count' => $nextAttempt,
            'time_spent_seconds' => 0,
            'target_time_minutes' => $latest ? $latest->target_time_minutes : 15,
        ]);
    }

    public function calculateWordCount(string $text): int
    {
        $clean = trim(strip_tags($text));

        if ($clean === '') {
            return 0;
        }

        return str_word_count($clean);
    }
}
