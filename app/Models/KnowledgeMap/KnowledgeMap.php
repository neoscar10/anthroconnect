<?php

namespace App\Models\KnowledgeMap;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class KnowledgeMap extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'subtitle', 'description', 'cover_image',
        'status', 'is_featured', 'visibility', 'default_zoom', 'canvas_settings',
        'published_at', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'canvas_settings' => 'json',
        'published_at' => 'datetime',
        'default_zoom' => 'decimal:2'
    ];

    public function nodes(): HasMany
    {
        return $this->hasMany(KnowledgeMapNode::class);
    }

    public function connections(): HasMany
    {
        return $this->hasMany(KnowledgeMapConnection::class);
    }

    public function learningPaths(): HasMany
    {
        return $this->hasMany(KnowledgeMapLearningPath::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVisibleToUser($query, $user = null)
    {
        if (!$user) {
            return $query->where('visibility', 'public');
        }
        
        // If user is admin, they can see all. If member, they can see members_only.
        // Assuming user has isMember() and hasRole('admin') or similar logic.
        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->isMember()) {
            return $query; // Members can see public and members_only
        }

        return $query->where('visibility', 'public');
    }
}
