<?php

namespace App\Services\Lms;

use App\Models\Lms\LmsModule;
use App\Models\Lms\LmsLesson;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

class LmsPublicService
{
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
