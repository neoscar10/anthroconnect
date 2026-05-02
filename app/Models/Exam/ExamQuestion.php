<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\HasTags;
use App\Models\User;

class ExamQuestion extends Model
{
    use HasFactory, SoftDeletes, HasTags;
    
    public const KIND_MODEL = 'model';
    public const KIND_PAST = 'past';

    public const QUESTION_KINDS = [
        self::KIND_MODEL => 'Model Question',
        self::KIND_PAST => 'Past Question',
    ];

    protected $fillable = [
        'lms_module_id',
        'lms_module_class_id',
        'title',
        'question_text',
        'slug',
        'question_type',
        'exam_type',
        'paper',
        'section',
        'year',
        'marks',
        'word_limit',
        'suggested_time_minutes',
        'difficulty',
        'short_context',
        'explanation',
        'answer_guidelines',
        'model_answer',
        'evaluation_rubric',
        'learning_resources',
        'status',
        'access_type',
        'sort_order',
        'is_members_only',
        'is_question_of_day',
        'question_of_day_date',
        'question_kind',
        'created_by',
        'updated_by',
        'published_at',
    ];

    protected $casts = [
        'marks' => 'integer',
        'word_limit' => 'integer',
        'suggested_time_minutes' => 'integer',
        'evaluation_rubric' => 'array',
        'learning_resources' => 'array',
        'published_at' => 'datetime',
        'sort_order' => 'integer',
        'is_members_only' => 'boolean',
        'is_question_of_day' => 'boolean',
        'question_of_day_date' => 'date',
        'lms_module_id' => 'integer',
        'lms_module_class_id' => 'integer',
    ];

    /**
     * Relationship: The user who created the question.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: The user who last updated the question.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function submissions()
    {
        return $this->hasMany(\App\Models\Exam\ExamAnswerSubmission::class, 'exam_question_id');
    }

    public function userSubmission($userId)
    {
        return $this->hasOne(\App\Models\Exam\ExamAnswerSubmission::class, 'exam_question_id')
            ->where('user_id', $userId);
    }

    /**
     * Relationship: Options for MCQ questions.
     */
    public function options()
    {
        return $this->hasMany(ExamQuestionOption::class, 'exam_question_id')->orderBy('sort_order');
    }

    /**
     * Relationship: Parent LMS Module.
     */
    public function lmsModule()
    {
        return $this->belongsTo(\App\Models\Lms\LmsModule::class, 'lms_module_id');
    }

    /**
     * Relationship: Parent LMS Class/Folder.
     */
    public function lmsModuleClass()
    {
        return $this->belongsTo(\App\Models\Lms\LmsModuleClass::class, 'lms_module_class_id');
    }

    public function lmsClassAssessment()
    {
        return $this->belongsTo(\App\Models\Lms\LmsClassAssessment::class, 'lms_class_assessment_id');
    }

    // Scopes

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFilterByStatus($query, $status)
    {
        return $query->when($status, fn($q) => $q->where('status', $status));
    }

    public function scopeFilterByExamType($query, $examType)
    {
        return $query->when($examType, fn($q) => $q->where('exam_type', $examType));
    }

    public function scopeFilterByPaper($query, $paper)
    {
        return $query->when($paper, fn($q) => $q->where('paper', $paper));
    }

    public function scopeFilterByYear($query, $year)
    {
        return $query->when($year, fn($q) => $q->where('year', $year));
    }

    public function scopeSearch($query, $term)
    {
        return $query->when($term, function($q) use ($term) {
            $q->where(function($sq) use ($term) {
                $sq->where('title', 'like', "%{$term}%")
                   ->orWhere('question_text', 'like', "%{$term}%")
                   ->orWhere('slug', 'like', "%{$term}%");
            });
        });
    }

    public function scopeMcq($query)
    {
        return $query->where('question_type', 'mcq');
    }

    public function scopeEssay($query)
    {
        return $query->where('question_type', 'essay');
    }

    public function scopeStandardExam($query)
    {
        return $query->whereNull('lms_module_class_id');
    }

    public function scopeForLmsClass($query, $classId)
    {
        return $query->where('lms_module_class_id', $classId);
    }

    public function getIsFreeAttribute(): bool
    {
        return ! $this->is_members_only;
    }

    public function isAccessibleBy($user = null): bool
    {
        if (! $this->is_members_only) {
            return true;
        }

        if (! $user) {
            return false;
        }

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        // Use the same membership check already used elsewhere in AnthroConnect.
        if (method_exists($user, 'hasActiveMembership')) {
            return $user->hasActiveMembership();
        }

        if (method_exists($user, 'isMember')) {
            return $user->isMember();
        }

        return (bool) ($user->is_member ?? false);
    }

    public function getRestrictionStateFor($user = null): array
    {
        if ($this->isAccessibleBy($user)) {
            return [
                'locked' => false,
                'reason' => null,
                'cta' => null,
            ];
        }

        if (! $user) {
            return [
                'locked' => true,
                'reason' => 'guest',
                'cta' => 'login',
            ];
        }

        return [
            'locked' => true,
            'reason' => 'membership_required',
            'cta' => 'membership',
        ];
    }

    public function isModelQuestion(): bool
    {
        return $this->question_kind === self::KIND_MODEL;
    }

    public function isPastQuestion(): bool
    {
        return $this->question_kind === self::KIND_PAST;
    }

    public function getQuestionKindLabelAttribute(): string
    {
        return self::QUESTION_KINDS[$this->question_kind] ?? 'Model Question';
    }
}
