<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LmsModuleClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lms_module_classes';

    protected $fillable = [
        'lms_module_id',
        'title',
        'slug',
        'description',
        'sort_order',
        'is_published',
        'is_assessment_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_assessment_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    public function module()
    {
        return $this->belongsTo(LmsModule::class, 'lms_module_id');
    }

    public function lessons()
    {
        return $this->hasMany(LmsLesson::class, 'lms_module_class_id')->orderBy('sort_order');
    }

    public function resources()
    {
        return $this->hasMany(LmsResource::class, 'lms_module_class_id')->orderBy('sort_order');
    }

    public function mcqQuestions()
    {
        return $this->hasMany(\App\Models\Exam\ExamQuestion::class, 'lms_module_class_id')
            ->orderBy('sort_order');
    }

    public function assessment()
    {
        return $this->hasOne(LmsClassAssessment::class, 'lms_module_class_id');
    }
}
