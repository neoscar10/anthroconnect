<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class LmsLesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lms_lessons';

    protected $fillable = [
        'lms_module_id',
        'title',
        'slug',
        'short_description',
        'video_source_type',
        'video_path',
        'video_url',
        'thumbnail',
        'duration_minutes',
        'sort_order',
        'is_preview',
        'is_published',
        'is_members_only',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
        'is_published' => 'boolean',
        'is_members_only' => 'boolean',
        'sort_order' => 'integer',
        'duration_minutes' => 'integer',
        'lms_module_id' => 'integer',
    ];

    public function module()
    {
        return $this->belongsTo(LmsModule::class, 'lms_module_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for published lessons.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Check if user can access this lesson.
     */
    public function canAccess(?User $user): bool
    {
        if (!$this->is_members_only || $this->is_preview) {
            return true;
        }

        return $user && $user->isMember();
    }
}
