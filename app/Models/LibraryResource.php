<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LibraryResource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'subtitle',
        'abstract',
        'description',
        'author_display',
        'publication_year',
        'publisher',
        'resource_type_id',
        'region_id',
        'language',
        'isbn',
        'doi',
        'edition',
        'pages_count',
        'file_path',
        'cover_image_path',
        'preview_file_path',
        'external_url',
        'source_label',
        'citation_text_apa',
        'citation_text_mla',
        'citation_text_chicago',
        'access_type',
        'allow_download',
        'allow_online_read',
        'is_featured',
        'is_latest',
        'is_recommended',
        'is_editors_pick',
        'show_in_more_resources',
        'sort_order',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'cover_source',
        'cover_external_url',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'pages_count' => 'integer',
        'allow_download' => 'boolean',
        'allow_online_read' => 'boolean',
        'is_featured' => 'boolean',
        'is_latest' => 'boolean',
        'is_recommended' => 'boolean',
        'is_editors_pick' => 'boolean',
        'show_in_more_resources' => 'boolean',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    // Relationships
    public function resourceType()
    {
        return $this->belongsTo(LibraryResourceType::class, 'resource_type_id');
    }



    public function region()
    {
        return $this->belongsTo(LibraryRegion::class, 'region_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'library_resource_topic');
    }

    public function tags()
    {
        return $this->belongsToMany(LibraryTag::class, 'library_resource_tag');
    }

    public function relatedLearningItems()
    {
        return $this->hasMany(LibraryResourceRelatedLearning::class, 'library_resource_id');
    }

    public function relatedResources()
    {
        return $this->belongsToMany(LibraryResource::class, 'library_resource_related_resources', 'library_resource_id', 'related_resource_id')
            ->withPivot('relation_type', 'sort_order')
            ->withTimestamps();
    }

    // Accessors
    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image_path ? Storage::url($this->cover_image_path) : null;
    }

    public function getCoverUrlAttribute(): string
    {
        if (!empty($this->cover_external_url)) {
            return $this->cover_external_url;
        }

        if (!empty($this->cover_image_path)) {
            return '/storage/' . $this->cover_image_path;
        }

        return asset('images/placeholders/library-cover.jpg');
    }

    public function getFileUrlAttribute()
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public function getPreviewFileUrlAttribute()
    {
        return $this->preview_file_path ? Storage::url($this->preview_file_path) : $this->file_url;
    }

    public function getPreviewUrlAttribute(): ?string
    {
        if (!empty($this->preview_file_path)) {
            return '/storage/' . $this->preview_file_path;
        }

        if (!empty($this->file_path) && (bool) $this->allow_online_read) {
            return '/storage/' . $this->file_path;
        }

        if (!empty($this->external_url)) {
            return $this->external_url;
        }

        return null;
    }

    public function getDownloadUrlAttribute(): ?string
    {
        if (!empty($this->file_path) && (bool) $this->allow_download) {
            return route('library.download', $this);
        }

        return null;
    }

    public function getCitationApaFallbackAttribute()
    {
        if ($this->citation_text_apa) return $this->citation_text_apa;
        return "{$this->author_display}. ({$this->publication_year}). {$this->title}. {$this->publisher}.";
    }

    public function getApaCitationAttribute(): string
    {
        if (!empty($this->citation_text_apa)) {
            return $this->citation_text_apa;
        }

        $author = $this->author_display ?: 'Unknown Author';
        $year = $this->publication_year ?: 'n.d.';
        $title = $this->title ?: 'Untitled';
        $publisher = $this->publisher ?: 'AnthroConnect';

        return "{$author}. ({$year}). {$title}. {$publisher}.";
    }

    public function getExcerptAttribute(): string
    {
        return (string) str($this->abstract ?: $this->description ?: 'No abstract available yet.')->stripTags()->limit(160);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
