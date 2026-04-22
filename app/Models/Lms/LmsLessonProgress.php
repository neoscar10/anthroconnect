<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LmsLessonProgress extends Model
{
    use HasFactory;

    protected $table = 'lms_lesson_progress';

    protected $fillable = [
        'user_id',
        'lms_module_id',
        'lms_lesson_id',
        'completed_at',
        'watched_seconds',
        'last_watched_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'last_watched_at' => 'datetime',
        'watched_seconds' => 'integer',
        'user_id' => 'integer',
        'lms_module_id' => 'integer',
        'lms_lesson_id' => 'integer',
    ];

    /**
     * Relationship to the scholar.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to the scholarly module.
     */
    public function module()
    {
        return $this->belongsTo(LmsModule::class, 'lms_module_id');
    }

    /**
     * Relationship to the narrative unit.
     */
    public function lesson()
    {
        return $this->belongsTo(LmsLesson::class, 'lms_lesson_id');
    }

    /**
     * Helper to check if progress represents a completion.
     */
    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }
}
