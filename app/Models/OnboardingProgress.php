<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingProgress extends Model
{
    protected $table = 'user_onboarding_progress';

    protected $fillable = [
        'user_id',
        'current_step_id',
        'completed_steps_count',
        'is_completed',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currentStep()
    {
        return $this->belongsTo(OnboardingStep::class, 'current_step_id');
    }
}
