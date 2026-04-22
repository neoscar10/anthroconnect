<?php

namespace App\Services\Lms;

use App\Models\Lms\LmsModule;
use App\Models\Lms\LmsLesson;
use App\Models\Lms\LmsLessonProgress;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LmsPublicService
{
    /**
     * Mark a narrative unit as completed for the scholar.
     */
    public function markLessonComplete(User $user, LmsLesson $lesson, int $watchedSeconds = 0)
    {
        return LmsLessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lms_lesson_id' => $lesson->id,
            ],
            [
                'lms_module_id' => $lesson->lms_module_id,
                'completed_at' => now(),
                'watched_seconds' => $watchedSeconds,
                'last_watched_at' => now(),
            ]
        );
    }

    /**
     * Calculate scholarly advancement within a module.
     */
    public function getModuleProgress(User $user, LmsModule $module)
    {
        $totalLessons = $module->lessons()->published()->count();
        
        if ($totalLessons === 0) {
            return [
                'completed_count' => 0,
                'total_count' => 0,
                'percentage' => 0,
            ];
        }

        $completedCount = LmsLessonProgress::where('user_id', $user->id)
            ->where('lms_module_id', $module->id)
            ->whereNotNull('completed_at')
            ->count();

        $percentage = min(100, round(($completedCount / $totalLessons) * 100));

        return [
            'completed_count' => $completedCount,
            'total_count' => $totalLessons,
            'percentage' => $percentage,
        ];
    }

    /**
     * Resolve the next incomplete unit for the scholar to continue their journey.
     */
    public function getContinueLesson(User $user, LmsModule $module)
    {
        $lessons = $module->lessons()->published()->orderBy('sort_order')->get();
        
        $completedLessonIds = LmsLessonProgress::where('user_id', $user->id)
            ->where('lms_module_id', $module->id)
            ->whereNotNull('completed_at')
            ->pluck('lms_lesson_id')
            ->toArray();

        foreach ($lessons as $lesson) {
            if (!in_array($lesson->id, $completedLessonIds)) {
                return $lesson;
            }
        }

        return $lessons->first(); // Return first if all completed or none found
    }

    /**
     * Batch retrieve completion records for a module's lessons.
     */
    public function getModuleLessonCompletionStatuses(User $user, LmsModule $module)
    {
        return LmsLessonProgress::where('user_id', $user->id)
            ->where('lms_module_id', $module->id)
            ->whereNotNull('completed_at')
            ->pluck('lms_lesson_id')
            ->toArray();
    }

    /**
     * Retrieve all active topics that have associated modules.
     */
    public function getActiveModuleTopics()
    {
        return Topic::active()
            ->whereHas('lmsModules', function($query) {
                $query->published();
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Retrieve paginated published modules with filters.
     */
    public function getPublishedModules($filters = [])
    {
        $query = LmsModule::with(['topic', 'creator'])
            ->withCount(['lessons', 'resources'])
            ->published()
            ->orderByDesc('created_at');

        if (!empty($filters['topic_id'])) {
            $query->where('topic_id', $filters['topic_id']);
        }

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        return $query->paginate(9);
    }

    /**
     * Retrieve a single published module by slug.
     */
    public function getModuleBySlug(string $slug): ?LmsModule
    {
        return LmsModule::with(['topic', 'creator', 'lessons', 'resources'])
            ->published()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Retrieve a single published lesson by slug within a parent module.
     */
    public function getLessonBySlug(string $moduleSlug, string $lessonSlug): ?LmsLesson
    {
        $module = LmsModule::published()->where('slug', $moduleSlug)->first();

        if (!$module) {
            return null;
        }

        return LmsLesson::with(['module', 'creator'])
            ->where('lms_module_id', $module->id)
            ->where('slug', $lessonSlug)
            ->where('is_published', true)
            ->first();
    }

    /**
     * Resolve previous and next lessons for navigation.
     */
    public function getLessonNavigation(LmsLesson $lesson)
    {
        $prev = LmsLesson::where('lms_module_id', $lesson->lms_module_id)
            ->where('is_published', true)
            ->where('sort_order', '<', $lesson->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        $next = LmsLesson::where('lms_module_id', $lesson->lms_module_id)
            ->where('is_published', true)
            ->where('sort_order', '>', $lesson->sort_order)
            ->orderBy('sort_order')
            ->first();

        return [
            'prev' => $prev,
            'next' => $next,
        ];
    }

    /**
     * Get related modules based on topic or level.
     */
    public function getRelatedModules(LmsModule $module, int $limit = 3)
    {
        return LmsModule::published()
            ->where('id', '!=', $module->id)
            ->where(function($q) use ($module) {
                $q->where('topic_id', $module->topic_id)
                  ->orWhere('level', $module->level);
            })
            ->withCount(['lessons'])
            ->limit($limit)
            ->get();
    }
}
