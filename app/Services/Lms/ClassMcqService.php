<?php

namespace App\Services\Lms;

use App\Models\Exam\ExamQuestion;
use App\Models\Exam\ExamQuestionOption;
use App\Models\Lms\LmsModule;
use App\Models\Lms\LmsModuleClass;
use App\Models\Lms\LmsClassAssessment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClassMcqService
{
    /**
     * Create a new MCQ for an LMS Class Assessment.
     */
    public function createForLmsClass(LmsClassAssessment $assessment, array $data, ?User $user = null): ExamQuestion
    {
        return DB::transaction(function () use ($assessment, $data, $user) {
            $questionData = $this->prepareQuestionData($data, $user);
            $questionData['lms_module_id'] = $assessment->lms_module_id;
            $questionData['lms_module_class_id'] = $assessment->lms_module_class_id;
            $questionData['lms_class_assessment_id'] = $assessment->id;
            $questionData['question_type'] = 'mcq';
            
            // Set sort order to end of list
            $questionData['sort_order'] = $assessment->questions()->max('sort_order') + 1;

            $question = ExamQuestion::create($questionData);
            
            $this->syncOptions($question, $data['options'] ?? [], $data['correct_option_index'] ?? 0);

            // Update total marks on assessment
            $this->updateAssessmentTotalMarks($assessment);

            return $question;
        });
    }

    /**
     * Update an existing LMS Class MCQ.
     */
    public function updateLmsClassQuestion(ExamQuestion $question, array $data, ?User $user = null): ExamQuestion
    {
        return DB::transaction(function () use ($question, $data, $user) {
            $questionData = $this->prepareQuestionData($data, $user);
            
            $question->update($questionData);
            
            if (isset($data['options'])) {
                $this->syncOptions($question, $data['options'], $data['correct_option_index'] ?? 0);
            }

            if ($question->lmsClassAssessment) {
                $this->updateAssessmentTotalMarks($question->lmsClassAssessment);
            }

            return $question;
        });
    }

    /**
     * Sync options for a question.
     */
    protected function syncOptions(ExamQuestion $question, array $options, int $correctIndex): void
    {
        // Delete old options
        $question->options()->delete();

        foreach ($options as $index => $optionData) {
            if (empty($optionData['text'])) continue;

            $question->options()->create([
                'option_text' => $optionData['text'],
                'is_correct' => (int)$index === (int)$correctIndex,
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * Prepare common question data.
     */
    protected function prepareQuestionData(array $data, ?User $user): array
    {
        $processed = [
            'question_text' => $data['question_text'],
            'explanation' => $data['explanation'] ?? null,
            'marks' => $data['marks'] ?? 1,
            'status' => $data['status'] ?? 'draft',
            'updated_by' => $user?->id,
        ];

        if (empty($data['id'])) {
            $processed['created_by'] = $user?->id;
            $processed['slug'] = $this->generateUniqueSlug($data['question_text']);
        }

        if ($processed['status'] === 'published') {
            $processed['published_at'] = now();
        }

        return $processed;
    }

    /**
     * Delete a question.
     */
    public function deleteLmsClassQuestion(ExamQuestion $question): void
    {
        DB::transaction(function () use ($question) {
            $assessment = $question->lmsClassAssessment;
            $question->options()->delete();
            $question->delete();

            if ($assessment) {
                $this->updateAssessmentTotalMarks($assessment);
            }
        });
    }

    /**
     * Duplicate a question.
     */
    public function duplicateQuestion(ExamQuestion $question, ?User $user = null): ExamQuestion
    {
        return DB::transaction(function () use ($question, $user) {
            $newQuestion = $question->replicate();
            $newQuestion->status = 'published';
            $newQuestion->published_at = now();
            $newQuestion->created_by = $user?->id;
            $newQuestion->updated_by = $user?->id;
            $newQuestion->slug = $this->generateUniqueSlug($question->question_text . ' copy');
            $newQuestion->sort_order = $question->lmsClassAssessment ? $question->lmsClassAssessment->questions()->max('sort_order') + 1 : 0;
            $newQuestion->save();

            foreach ($question->options as $option) {
                $newOption = $option->replicate();
                $newOption->exam_question_id = $newQuestion->id;
                $newOption->save();
            }

            if ($newQuestion->lmsClassAssessment) {
                $this->updateAssessmentTotalMarks($newQuestion->lmsClassAssessment);
            }

            return $newQuestion;
        });
    }

    /**
     * Reorder questions.
     */
    public function reorderQuestions(LmsClassAssessment $assessment, array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                ExamQuestion::where('id', $id)->update(['sort_order' => $index]);
            }
        });
    }

    /**
     * Update total marks for an assessment.
     */
    public function updateAssessmentTotalMarks(LmsClassAssessment $assessment): void
    {
        $total = $assessment->questions()->sum('marks');
        $assessment->update(['total_marks' => $total]);
    }

    /**
     * Generate unique slug.
     */
    protected function generateUniqueSlug(string $text): string
    {
        $baseSlug = Str::limit(Str::slug($text), 100, '');
        $slug = $baseSlug;
        $count = 1;

        while (ExamQuestion::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
