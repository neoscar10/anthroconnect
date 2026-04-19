<?php

namespace App\Services\Onboarding;

use App\Models\User;
use App\Models\OnboardingProgress;
use App\Models\OnboardingStep;

class UserOnboardingService
{
    protected $stepService;

    public function __construct(OnboardingStepService $stepService)
    {
        $this->stepService = $stepService;
    }

    /**
     * Determine if a user requires onboarding.
     */
    public function requiresOnboarding(User $user): bool
    {
        if ($user->hasRole(['Super Admin', 'Admin'])) {
            return false;
        }

        if ($user->onboarding_completed_at !== null) {
            return false;
        }

        return $this->stepService->getActiveSteps()->isNotEmpty();
    }

    /**
     * Get or initialize progress for a user.
     */
    public function getProgress(User $user): OnboardingProgress
    {
        $progress = OnboardingProgress::firstOrCreate(
            ['user_id' => $user->id],
            [
                'is_completed' => false,
                'started_at' => now(),
                'completed_steps_count' => 0
            ]
        );

        if (!$progress->current_step_id) {
            $firstStep = $this->stepService->getFirstStep();
            if ($firstStep) {
                $progress->update(['current_step_id' => $firstStep->id]);
            }
        }

        return $progress;
    }

    /**
     * Mark a step as completed and advance.
     */
    public function advance(User $user, OnboardingStep $currentStep): void
    {
        $progress = $this->getProgress($user);
        $nextStep = $this->stepService->getNextStep($currentStep);
        
        $updateData = [
            'completed_steps_count' => $progress->completed_steps_count + 1,
        ];

        if ($nextStep) {
            $updateData['current_step_id'] = $nextStep->id;
        } else {
            $updateData['is_completed'] = true;
            $updateData['completed_at'] = now();
            $user->update(['onboarding_completed_at' => now()]);
        }

        $progress->update($updateData);
    }

    /**
     * Get progress metadata for the design-accurate UI.
     */
    public function getProgressMeta($user, OnboardingStep $currentStep): array
    {
        $progress = $this->getProgress($user);
        $activeSteps = $this->stepService->getActiveSteps();
        $totalSteps = $activeSteps->count();
        
        $steps = $activeSteps->map(function($step, $index) use ($progress, $currentStep) {
            $isCompleted = $progress->is_completed || ($step->sort_order < $this->stepService->findStep($progress->current_step_id)?->sort_order);
            return [
                'slug' => $step->slug,
                'title' => $step->title,
                'is_completed' => $isCompleted,
                'is_current' => $step->id === $currentStep->id,
            ];
        })->toArray();

        $currentIndex = 1;
        foreach($activeSteps as $index => $step) {
            if ($step->id === $currentStep->id) {
                $currentIndex = $index + 1;
                break;
            }
        }

        return [
            'current_index' => $currentIndex,
            'total_steps' => $totalSteps,
            'percent' => ($totalSteps > 0) ? round(($progress->completed_steps_count / $totalSteps) * 100) : 100,
            'steps' => $steps,
        ];
    }

    /**
     * Get the current step for the user.
     */
    public function getCurrentStepForUser(User $user): ?OnboardingStep
    {
        if (!$this->requiresOnboarding($user)) {
            return null;
        }
        
        $progress = $this->getProgress($user);
        return $this->stepService->findStep($progress->current_step_id);
    }

    /**
     * Complete step and advance (helper for frontend).
     */
    public function markStepCompleteAndAdvance(User $user, OnboardingStep $step): array
    {
        $this->advance($user, $step);
        $progress = $this->getProgress($user);
        
        return [
            'completed' => (bool)$progress->is_completed,
            'next_step' => $this->getCurrentStepForUser($user),
        ];
    }
}
