<?php

namespace App\Models\Lms;

use App\Models\Exam\ExamQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsClassAssessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lms_class_assessments';

    protected $fillable = [
        'lms_module_id',
        'lms_module_class_id',
        'title',
        'description',
        'instructions',
        'duration_minutes',
        'total_marks',
        'passing_marks',
        'allow_retake',
        'show_results_immediately',
        'show_correct_answers',
        'randomize_questions',
        'randomize_options',
        'is_published',
        'published_at',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'allow_retake' => 'boolean',
        'show_results_immediately' => 'boolean',
        'show_correct_answers' => 'boolean',
        'randomize_questions' => 'boolean',
        'randomize_options' => 'boolean',
        'published_at' => 'datetime',
        'total_marks' => 'integer',
        'passing_marks' => 'integer',
        'duration_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    public function module()
    {
        return $this->belongsTo(LmsModule::class, 'lms_module_id');
    }

    public function class()
    {
        return $this->belongsTo(LmsModuleClass::class, 'lms_module_class_id');
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class, 'lms_class_assessment_id')
            ->orderBy('sort_order');
    }

    public function attempts()
    {
        return $this->hasMany(LmsClassAssessmentAttempt::class, 'lms_class_assessment_id');
    }
    
    public function userAttempts($userId)
    {
        return $this->attempts()->where('user_id', $userId)->orderByDesc('created_at');
    }
}
