<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingResponse extends Model
{
    protected $table = 'user_onboarding_step_responses';

    protected $fillable = [
        'user_id',
        'onboarding_step_id',
        'response_payload',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'response_payload' => 'array',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function step()
    {
        return $this->belongsTo(OnboardingStep::class, 'onboarding_step_id');
    }
}
