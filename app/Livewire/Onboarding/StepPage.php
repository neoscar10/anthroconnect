<?php

namespace App\Livewire\Onboarding;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use App\Services\Onboarding\OnboardingStepService;
use App\Services\Onboarding\UserOnboardingService;
use App\Services\Onboarding\OnboardingResponseService;

class StepPage extends Component
{
    public string $stepSlug = '';
    
    // Normalized Data for UI
    public array $stepData = [];
    public array $progressMeta = [];
    
    // Selection State
    public $selected_options = []; // For Multi-choice
    public ?string $selected_option = null; // For Single-choice
    public array $selected_additional_interests = [];
    public array $selected_regions = [];
    public bool $preparing_for_upsc = false;

    /**
     * Mount the component.
     */
    public function mount(
        string $stepSlug = '',
        OnboardingStepService $stepService = null,
        UserOnboardingService $userOnboardingService = null,
        OnboardingResponseService $responseService = null
    ) {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        // Resolve Step
        $resolvedStep = $stepSlug !== ''
            ? $stepService->getAccessibleUserStepBySlug($user, $stepSlug)
            : $userOnboardingService->getCurrentStepForUser($user);

        if (!$resolvedStep) {
            return redirect()->route('dashboard');
        }

        $this->stepSlug = $resolvedStep->slug;
        $this->stepData = $stepService->getNormalizedStep($resolvedStep);
        $this->progressMeta = $userOnboardingService->getProgressMeta($user, $resolvedStep);

        // Load Existing Response
        $response = $responseService->getResponse($user, $resolvedStep);
        if ($response && $response->response_payload) {
            $payload = $response->response_payload;
            $this->selected_options = $payload['selected_options'] ?? [];
            $this->selected_option = $payload['selected_option'] ?? null;
            $this->selected_additional_interests = $payload['selected_additional_interests'] ?? [];
            $this->selected_regions = $payload['selected_regions'] ?? [];
            $this->preparing_for_upsc = (bool)($payload['preparing_for_upsc'] ?? false);
        }
    }

    /**
     * Toggle a major domain card.
     */
    public function toggleOption(string $key)
    {
        if ($this->stepData['step_type'] === 'card_single' || $this->stepData['step_type'] === 'radio') {
            $this->selected_option = $key;
            return;
        }

        if (in_array($key, $this->selected_options)) {
            $this->selected_options = array_diff($this->selected_options, [$key]);
        } else {
            $this->selected_options[] = $key;
        }
    }

    /**
     * Toggle additional interest.
     */
    public function toggleAdditionalInterest(string $key)
    {
        if (in_array($key, $this->selected_additional_interests)) {
            $this->selected_additional_interests = array_diff($this->selected_additional_interests, [$key]);
        } else {
            $this->selected_additional_interests[] = $key;
        }
    }

    /**
     * Toggle a region tile.
     */
    public function toggleRegion(string $key)
    {
        if (in_array($key, $this->selected_regions)) {
            $this->selected_regions = array_diff($this->selected_regions, [$key]);
        } else {
            $this->selected_regions[] = $key;
        }
    }

    /**
     * Save and move to the next step.
     */
    public function saveAndContinue(
        OnboardingStepService $stepService,
        UserOnboardingService $userOnboardingService,
        OnboardingResponseService $responseService
    ) {
        $user = Auth::user();
        $resolvedStep = $stepService->getAccessibleUserStepBySlug($user, $this->stepSlug);
        
        $payload = [
            'selected_options' => array_values($this->selected_options),
            'selected_option' => $this->selected_option,
            'selected_additional_interests' => array_values($this->selected_additional_interests),
            'selected_regions' => array_values($this->selected_regions),
            'preparing_for_upsc' => $this->preparing_for_upsc,
        ];

        // Basic validation
        if ($this->stepData['step_type'] === 'card_single' && empty($this->selected_option)) {
            $this->addError('selection', 'Please make a selection to continue.');
            return;
        }
        
        if ($this->stepData['step_type'] === 'card_multi' && empty($this->selected_options)) {
            $this->addError('selection', 'Please select at least one option.');
            return;
        }

        $responseService->saveUserStepResponse($user, $resolvedStep, $payload);
        $result = $userOnboardingService->markStepCompleteAndAdvance($user, $resolvedStep);

        if ($result['completed']) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('onboarding.show', ['stepSlug' => $result['next_step']->slug]);
    }

    /**
     * Skip the current step.
     */
    public function skip(
        OnboardingStepService $stepService,
        UserOnboardingService $userOnboardingService
    ) {
        $user = Auth::user();
        $resolvedStep = $stepService->getAccessibleUserStepBySlug($user, $this->stepSlug);
        
        $result = $userOnboardingService->markStepCompleteAndAdvance($user, $resolvedStep);

        if ($result['completed']) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('onboarding.show', ['stepSlug' => $result['next_step']->slug]);
    }

    public function render()
    {
        return view('livewire.onboarding.step-page');
    }
}
