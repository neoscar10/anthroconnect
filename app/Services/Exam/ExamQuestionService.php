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
