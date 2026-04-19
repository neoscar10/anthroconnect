<?php

namespace App\Services\Onboarding;

use App\Models\OnboardingStep;
use Illuminate\Support\Collection;

class OnboardingStepService
{
    /**
     * Get all active onboarding steps in sequence.
     */
    public function getActiveSteps(): Collection
    {
        return OnboardingStep::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    /**
     * Get the first onboarding step.
     */
    public function getFirstStep(): ?OnboardingStep
    {
        return $this->getActiveSteps()->first();
    }

    /**
     * Get the next step after a given step.
     */
    public function getNextStep(OnboardingStep $currentStep): ?OnboardingStep
    {
        return OnboardingStep::where('is_active', true)
            ->where('sort_order', '>', $currentStep->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();
    }

    /**
     * Find a step by its ID.
     */
    public function findStep(?int $id): ?OnboardingStep
    {
        return $id ? OnboardingStep::find($id) : null;
    }

    /**
     * Get an accessible step by its slug.
     */
    public function getAccessibleUserStepBySlug($user, string $slug): ?OnboardingStep
    {
        return OnboardingStep::where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Normalize Step Configuration for the "Stitch" Design.
     * This maps Admin Builder fields to the standardized Design Shape.
     */
    public function getNormalizedStep(OnboardingStep $step): array
    {
        $content = $step->content ?? [];
        
        return [
            'id' => $step->id,
            'slug' => $step->slug,
            'title' => $step->title,
            'supporting_text' => $step->description,
            'step_type' => $step->type,
            'is_required' => (bool)($content['is_required'] ?? true),
            'is_skippable' => (bool)($content['is_skippable'] ?? true),
            'show_regions' => !empty($content['regions'] ?? []),
            'show_upsc_toggle' => (bool)$step->upsc_integration,
            'options' => collect($content['categories'] ?? [])->map(fn($c) => [
                'key' => \Illuminate\Support\Str::slug($c['title']),
                'label' => $c['title'],
                'description' => $c['desc'] ?? '',
                'icon' => $c['icon'] ?? 'category',
                'image' => null,
            ])->toArray(),
            'additional_interests' => collect($content['additional_interests'] ?? [])->map(fn($i) => [
                'key' => $i['key'] ?? \Illuminate\Support\Str::slug($i['label']),
                'label' => $i['label'],
            ])->toArray(),
            'regions' => collect($content['regions'] ?? [])->map(fn($r) => [
                'key' => $r['key'] ?? \Illuminate\Support\Str::slug($r['label']),
                'label' => $r['label'],
            ])->toArray(),
            'upsc_label' => $content['upsc_label'] ?? 'Are you preparing for UPSC?',
            'upsc_description' => $content['upsc_description'] ?? 'Enable specialized exam preparation tools and modules.',
            'continue_label' => $content['continue_label'] ?? 'Continue',
            'skip_label' => $content['skip_label'] ?? 'Skip for now',
        ];
    }
}
