<?php

namespace App\Services\Exam;

use App\Models\Exam\ExamQuestion;

class ExamQuestionFrontendService
{
    public function paginatePublished(array $filters = [], $user = null, int $perPage = 12)
    {
        return ExamQuestion::query()
            ->published()
            ->with(['tags'])
            ->when(! empty($filters['search']), function ($query) use ($filters) {
                $search = trim($filters['search']);

                $query->where(function ($q) use ($search) {
                    $q->where('question_text', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('section', 'like', "%{$search}%")
                        ->orWhere('exam_type', 'like', "%{$search}%");
                });
            })
            ->when(! empty($filters['exam_type']), fn ($q) => $q->where('exam_type', $filters['exam_type']))
            ->when(! empty($filters['paper']), fn ($q) => $q->where('paper', $filters['paper']))
            ->when(! empty($filters['year']), fn ($q) => $q->where('year', $filters['year']))
            ->when(! empty($filters['difficulty']), fn ($q) => $q->where('difficulty', $filters['difficulty']))
            ->when(! empty($filters['kind']), fn ($q) => $q->where('question_kind', $filters['kind']))
            ->when(! empty($filters['tags']), function ($query) use ($filters) {
                $tagIds = array_filter((array) $filters['tags']);

                $query->whereHas('tags', function ($q) use ($tagIds) {
                    $q->whereIn('tags.id', $tagIds);
                });
            })
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function questionOfTheDay()
    {
        return ExamQuestion::query()
            ->published()
            ->with(['tags'])
            ->where('is_question_of_day', true)
            ->where(function ($query) {
                $query->whereDate('question_of_day_date', now()->toDateString())
                    ->orWhereNull('question_of_day_date');
            })
            ->latest('question_of_day_date')
            ->first();
    }

    public function findPublishedBySlug(string $slug): ExamQuestion
    {
        return ExamQuestion::query()
            ->published()
            ->with(['tags'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function previousQuestion(ExamQuestion $question): ?ExamQuestion
    {
        return ExamQuestion::query()
            ->published()
            ->where('id', '<', $question->id)
            ->latest('id')
            ->first();
    }

    public function nextQuestion(ExamQuestion $question): ?ExamQuestion
    {
        return ExamQuestion::query()
            ->published()
            ->where('id', '>', $question->id)
            ->oldest('id')
            ->first();
    }

    public function relatedQuestions(ExamQuestion $question, int $limit = 4)
    {
        $tagIds = $question->tags->pluck('id')->toArray();

        return ExamQuestion::query()
            ->published()
            ->with('tags')
            ->where('id', '!=', $question->id)
            ->when(count($tagIds), function ($query) use ($tagIds) {
                $query->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $tagIds));
            })
            ->limit($limit)
            ->get();
    }
}
