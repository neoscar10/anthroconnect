<?php

namespace App\Services\Exam;

use App\Models\Exam\ExamQuestion;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ExamQuestionService
{
    /**
     * List questions with filters and pagination.
     */
    public function paginate(array $filters = [], int $perPage = 15)
    {
        $query = ExamQuestion::query()
            ->standardExam()
            ->with(['tags', 'creator'])
            ->search($filters['search'] ?? null)
            ->filterByStatus($filters['status'] ?? null)
            ->filterByExamType($filters['exam_type'] ?? null)
            ->filterByPaper($filters['paper'] ?? null)
            ->filterByYear($filters['year'] ?? null);

        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (!empty($filters['tag_ids'])) {
            foreach ($filters['tag_ids'] as $tagId) {
                if ($tagId) {
                    $query->withTag($tagId);
                }
            }
        }

        if (!empty($filters['question_kind'])) {
            $query->where('question_kind', $filters['question_kind']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Create a new question.
     */
    public function create(array $data, ?User $user = null): ExamQuestion
    {
        return DB::transaction(function () use ($data, $user) {
            $data['slug'] = $this->generateUniqueSlug($data['title'] ?? $data['question_text']);
            $data['created_by'] = $user?->id;
            $data['updated_by'] = $user?->id;
            $data['question_kind'] = $data['question_kind'] ?? ExamQuestion::KIND_MODEL;

            if (($data['status'] ?? 'published') === 'published' && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $question = ExamQuestion::create($data);

            if (!empty($data['tag_ids'])) {
                $question->syncTags($data['tag_ids']);
            }

            return $question;
        });
    }

    /**
     * Update an existing question.
     */
    public function update(ExamQuestion $question, array $data, ?User $user = null): ExamQuestion
    {
        return DB::transaction(function () use ($question, $data, $user) {
            $data['updated_by'] = $user?->id;

            if (($data['status'] ?? $question->status) === 'published' && empty($question->published_at)) {
                $data['published_at'] = now();
            }

            $question->update($data);

            if (isset($data['tag_ids'])) {
                $question->syncTags($data['tag_ids']);
            }

            return $question;
        });
    }

    /**
     * Delete / Soft-delete a question.
     */
    public function delete(ExamQuestion $question): void
    {
        $question->delete();
    }

    /**
     * Publish a question.
     */
    public function publish(ExamQuestion $question, ?User $user = null): ExamQuestion
    {
        return $this->update($question, [
            'status' => 'published',
            'published_at' => now()
        ], $user);
    }

    /**
     * Archive a question.
     */
    public function archive(ExamQuestion $question, ?User $user = null): ExamQuestion
    {
        return $this->update($question, [
            'status' => 'archived'
        ], $user);
    }

    /**
     * Duplicate a question.
     */
    public function duplicate(ExamQuestion $question, ?User $user = null): ExamQuestion
    {
        return DB::transaction(function () use ($question, $user) {
            $newData = $question->toArray();
            
            // Clean up for new record
            unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at']);
            
            $newData['title'] = $question->title . ' (Copy)';
            $newData['slug'] = $this->generateUniqueSlug($newData['title']);
            $newData['status'] = 'draft';
            $newData['is_question_of_day'] = false;
            $newData['question_of_day_date'] = null;
            $newData['published_at'] = null;
            $newData['created_by'] = $user?->id;
            $newData['updated_by'] = $user?->id;
            
            // Question kind is preserved from $question->toArray()
            
            $newQuestion = ExamQuestion::create($newData);
            
            // Sync tags
            if ($question->tags) {
                $newQuestion->syncTags($question->tags->pluck('id')->toArray());
            }
            
            return $newQuestion;
        });
    }

    /**
     * Generate a unique slug.
     */
    protected function generateUniqueSlug(string $text, ?int $ignoreId = null): string
    {
        $baseSlug = Str::limit(Str::slug($text), 100, '');
        $slug = $baseSlug;
        $count = 1;

        while (ExamQuestion::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
