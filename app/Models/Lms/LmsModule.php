<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Topic;
use App\Models\User;

use App\Models\Concerns\HasTags;

class LmsModule extends Model
{
    use HasFactory, SoftDeletes, HasTags;

    protected $table = 'lms_modules';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'overview',
        'cover_image',
        'level',
        'topic_id',
        'is_published',
        'is_upsc_relevant',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_upsc_relevant' => 'boolean',
        'topic_id' => 'integer',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    public function lessons()
    {
        return $this->hasMany(LmsLesson::class, 'lms_module_id')->orderBy('sort_order');
    }

    public function resources()
    {
        return $this->hasMany(LmsResource::class, 'lms_module_id')->orderBy('sort_order');
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
     * Scope for published modules.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Check if user can access the module (primarily used for gating lessons).
     */
    public function canAccess(?User $user): bool
    {
        // Currently modules are publicly browseable but individual lessons are restricted
        return true;
    }
}
