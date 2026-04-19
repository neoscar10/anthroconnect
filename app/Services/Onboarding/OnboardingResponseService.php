<?php

namespace App\Services\Onboarding;

use App\Models\User;
use App\Models\OnboardingStep;
use App\Models\OnboardingResponse;

class OnboardingResponseService
{
    /**
     * Get a user's response for a specific step.
     */
    public function getResponse(User $user, OnboardingStep $step): ?OnboardingResponse
    {
        return OnboardingResponse::where('user_id', $user->id)
            ->where('onboarding_step_id', $step->id)
            ->first();
    }

    /**
     * Standardized payload saver for the design-accurate UI.
     */
    public function saveUserStepResponse(User $user, OnboardingStep $step, array $payload, bool $completed = true): void
    {
        OnboardingResponse::updateOrCreate(
            [
                'user_id' => $user->id,
                'onboarding_step_id' => $step->id,
            ],
            [
                'response_payload' => $payload,
                'is_completed' => $completed,
                'completed_at' => $completed ? now() : null,
            ]
        );
    }
}
