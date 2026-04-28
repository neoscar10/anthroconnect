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
        
        // Handle initial sort order for new articles
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = (ExploreArticle::max('sort_order') ?? 0) + 1;
        }

        if (isset($data['status']) && $data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (isset($data['featured_image']) && $data['featured_image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['featured_image'] = $this->uploadImage($data['featured_image']);
        }

        $article = ExploreArticle::create($data);

        if (isset($data['tags'])) {
            $article->syncTags($data['tags']);
        }

        return $article;
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

        if (isset($data['tags'])) {
            $article->syncTags($data['tags']);
        }

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
        $query = ExploreArticle::with('tags', 'creator')->orderBy('sort_order', 'asc');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('excerpt', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['tag_ids']) && is_array($filters['tag_ids'])) {
            foreach ($filters['tag_ids'] as $tagId) {
                if ($tagId) $query->withTag($tagId);
            }
        }

        if (!empty($filters['tag_id'])) {
            $query->withTag($filters['tag_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_members_only']) && !is_null($filters['is_members_only'])) {
            $query->where('is_members_only', $filters['is_members_only']);
        }

        if (isset($filters['is_upsc_relevant']) && !is_null($filters['is_upsc_relevant'])) {
            $query->where('is_upsc_relevant', $filters['is_upsc_relevant']);
        }

        return $query;
    }
}
