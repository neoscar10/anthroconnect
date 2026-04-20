<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ExploreArticle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'topic_id',
        'title',
        'slug',
        'excerpt',
        'markdown_content',
        'rendered_content_html',
        'featured_image',
        'status',
        'is_featured',
        'is_members_only',
        'reading_time_minutes',
        'published_at',
        'created_by',
        'updated_by',
        // SEO fields are kept in migration/model to avoid DB instability 
        // but will be ignored in the UI and business logic
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_members_only' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Check if a user can access this article.
     */
    public function canAccess(?User $user): bool
    {
        if (!$this->is_members_only) {
            return true;
        }

        return $user && $user->isMember();
    }

    /**
     * Relationship with Topic (Global).
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    /**
     * Relationship with Creator.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with Last Updater.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope for featured articles.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
