<?php

namespace App\Models\Lms;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsClassAssessmentAttempt extends Model
{
    use HasFactory;

    protected $table = 'lms_class_assessment_attempts';

    protected $fillable = [
        'user_id',
        'lms_class_assessment_id',
        'score',
        'total_marks',
        'percentage',
        'passed',
        'started_at',
        'submitted_at',
        'completed_at',
        'time_taken_seconds',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'passed' => 'boolean',
        'percentage' => 'decimal:2',
        'score' => 'integer',
        'total_marks' => 'integer',
        'time_taken_seconds' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assessment()
    {
        return $this->belongsTo(LmsClassAssessment::class, 'lms_class_assessment_id');
    }

    public function answers()
    {
        return $this->hasMany(LmsClassAssessmentAttemptAnswer::class, 'attempt_id');
    }

    public function getSummaryAttribute()
    {
        if (!$this->submitted_at) return "Session in Progress";
        
        if ($this->percentage >= 95) return "Divine Mastery";
        if ($this->percentage >= 85) return "Scholarly Excellence";
        if ($this->percentage >= 70) return "Conceptual Proficiency";
        if ($this->percentage >= 50) return "Unit Comprehension";
        if ($this->passed) return "Base Mastery Achieved";
        
        return "Intellectual Revision Required";
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->time_taken_seconds) return '0s';
        
        $minutes = floor($this->time_taken_seconds / 60);
        $seconds = $this->time_taken_seconds % 60;
        
        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }
        
        return "{$seconds}s";
    }
}
