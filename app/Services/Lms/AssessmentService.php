<?php

namespace App\Services\Lms;

use App\Models\Lms\LmsClassAssessment;
use App\Models\Lms\LmsClassAssessmentAttempt;
use App\Models\Lms\LmsClassAssessmentAttemptAnswer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssessmentService
{
    public function startAttempt(LmsClassAssessment $assessment, User $user): LmsClassAssessmentAttempt
    {
        return DB::transaction(function () use ($assessment, $user) {
            // Check for in-progress attempts
            $existing = LmsClassAssessmentAttempt::where('user_id', $user->id)
                ->where('lms_class_assessment_id', $assessment->id)
                ->whereNull('submitted_at')
                ->first();

            if ($existing) {
                return $existing;
            }

            $attempt = LmsClassAssessmentAttempt::create([
                'user_id' => $user->id,
                'lms_class_assessment_id' => $assessment->id,
                'total_marks' => $assessment->total_marks,
                'started_at' => now(),
            ]);

            return $attempt;
        });
    }

    public function saveAnswer(LmsClassAssessmentAttempt $attempt, int $questionId, ?int $selectedOptionId): LmsClassAssessmentAttemptAnswer
    {
        $question = $attempt->assessment->questions()->findOrFail($questionId);
        $option = $selectedOptionId ? $question->options()->findOrFail($selectedOptionId) : null;

        $isCorrect = $option ? $option->is_correct : false;
        $marksAwarded = $isCorrect ? $question->marks : 0;

        return LmsClassAssessmentAttemptAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
            ],
            [
                'selected_option_id' => $selectedOptionId,
                'is_correct' => $isCorrect,
                'marks_awarded' => $marksAwarded,
            ]
        );
    }

    public function submitAttempt(LmsClassAssessmentAttempt $attempt): LmsClassAssessmentAttempt
    {
        return DB::transaction(function () use ($attempt) {
            if ($attempt->submitted_at) {
                return $attempt;
            }

            $attempt->load('answers', 'assessment.questions');
            
            $score = $attempt->answers->sum('marks_awarded');
            // Recalculate total marks from the actual questions to ensure accuracy
            $totalMarks = $attempt->assessment->questions->sum('marks') ?: ($attempt->total_marks ?: 1);
            $percentage = ($score / $totalMarks) * 100;
            
            $passingPercentage = $attempt->assessment->passing_marks ?: 0;
            $passed = $percentage >= $passingPercentage;

            $now = now();
            $timeTaken = $now->diffInSeconds($attempt->started_at);
            
            // Cap time taken to the duration if it's a timed test
            if ($attempt->assessment->duration_minutes) {
                $maxSeconds = $attempt->assessment->duration_minutes * 60;
                $timeTaken = min($timeTaken, $maxSeconds);
            }

            $attempt->update([
                'score' => $score,
                'percentage' => $percentage,
                'passed' => $passed,
                'submitted_at' => $now,
                'completed_at' => $now,
                'time_taken_seconds' => $timeTaken,
            ]);

            return $attempt;
        });
    }

    public function canUserTake(LmsClassAssessment $assessment, User $user): bool
    {
        if (!$assessment->is_published) {
            return false;
        }

        if (!$assessment->allow_retake) {
            $hasPassed = LmsClassAssessmentAttempt::where('user_id', $user->id)
                ->where('lms_class_assessment_id', $assessment->id)
                ->where('passed', true)
                ->exists();
            
            if ($hasPassed) {
                return false;
            }
        }

        return true;
    }
}
