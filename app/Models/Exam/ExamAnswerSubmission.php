<?php

namespace App\Models\Exam;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamAnswerSubmission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'exam_question_id',
        'user_id',
        'answer_text',
        'submission_type',
        'attachment_path',
        'word_count',
        'character_count',
        'time_spent_seconds',
        'target_time_minutes',
        'attempts_count',
        'status',
        'feedback_text',
        'evaluation_attachment_path',
        'score',
        'submitted_at',
        'evaluated_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'evaluated_at' => 'datetime',
    ];

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'exam_question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsSubmittedAttribute(): bool
    {
        return $this->status === 'submitted';
    }
}
