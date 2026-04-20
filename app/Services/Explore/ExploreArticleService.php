<?php

namespace App\Services\Explore;

use App\Models\ExploreArticle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExploreArticleService
{
    /**
     * Create a new article.
     */
    public function createArticle(array $data): ExploreArticle
    {
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['created_by'] = Auth::id();
        $data['rendered_content_html'] = $this->renderMarkdown($data['markdown_content']);
        $data['reading_time_minutes'] = $this->calculateReadingTime($data['markdown_content']);
        
        if (isset($data['status']) && $data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (isset($data['featured_image']) && $data['featured_image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['featured_image'] = $this->uploadImage($data['featured_image']);
        }

        return ExploreArticle::create($data);
    }

    /**
     * Update an existing article.
     */
    public function updateArticle(ExploreArticle $article, array $data): ExploreArticle
    {
        $data['updated_by'] = Auth::id();
        
        if (isset($data['markdown_content'])) {
            $data['rendered_content_html'] = $this->renderMarkdown($data['markdown_content']);
            $data['reading_time_minutes'] = $this->calculateReadingTime($data['markdown_content']);
        }

        if (isset($data['status']) && $data['status'] === 'published' && empty($article->published_at) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (isset($data['featured_image']) && $data['featured_image'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old image if exists
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $data['featured_image'] = $this->uploadImage($data['featured_image']);
        }

        $article->update($data);
        return $article;
    }

    /**
     * Render markdown to sanitized HTML.
     */
    public function renderMarkdown(string $markdown): string
    {
        // Using Laravel's built-in markdown support
        return Str::markdown($markdown);
    }

    /**
     * Calculate estimated reading time in minutes.
     */
    public function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 200); // Average reading speed 200 wpm
        return (int) max(1, $minutes);
    }

    /**
     * Handle featured image upload.
     */
    protected function uploadImage($file): string
    {
        return $file->store('explore/articles', 'public');
    }

    /**
     * List articles with basic filters for admin.
     */
    public function listArticlesForAdmin(array $filters = [])
    {
        $query = ExploreArticle::with('topic', 'creator')->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('excerpt', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['topic_id'])) {
            $query->where('topic_id', $filters['topic_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_members_only']) && !is_null($filters['is_members_only'])) {
            $query->where('is_members_only', $filters['is_members_only']);
        }

        return $query;
    }
}
