<?php

namespace App\Models\Lms;

use App\Models\Exam\ExamQuestion;
use App\Models\Exam\ExamQuestionOption;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsClassAssessmentAttemptAnswer extends Model
{
    use HasFactory;

    protected $table = 'lms_class_assessment_attempt_answers';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'is_correct',
        'marks_awarded',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'marks_awarded' => 'integer',
    ];

    public function attempt()
    {
        return $this->belongsTo(LmsClassAssessmentAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }

    public function option()
    {
        return $this->belongsTo(ExamQuestionOption::class, 'selected_option_id');
    }
}
