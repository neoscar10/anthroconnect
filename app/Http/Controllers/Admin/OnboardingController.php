<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingStep;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    /**
     * Display the onboarding flows management dashboard.
     */
    public function index(Request $request)
    {
        $steps = OnboardingStep::orderBy('sort_order')->get();
        
        $activeStep = null;
        if ($request->has('step')) {
            $activeStep = OnboardingStep::find($request->step);
        } elseif ($steps->isNotEmpty()) {
            $activeStep = $steps->first();
        }

        return view('admin.onboarding.index', compact('steps', 'activeStep'));
    }

    /**
     * Store a new onboarding step.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:card_multi,card_single,radio,multi_select',
        ]);

        $maxOrder = OnboardingStep::max('sort_order') ?? 0;

        $step = OnboardingStep::create([
            'title' => $request->title,
            'slug' => \Illuminate\Support\Str::slug($request->title ?: 'new-step-' . now()->timestamp),
            'type' => $request->type,
            'description' => '',
            'sort_order' => $maxOrder + 1,
            'is_active' => false,
            'content' => ['categories' => [], 'regions' => []],
        ]);

        return redirect()->route('admin.onboarding.index', ['step' => $step->id])
            ->with('success', 'New onboarding step initialized.');
    }

    /**
     * Update an onboarding step.
     */
    public function update(Request $request, OnboardingStep $onboardingStep)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:onboarding_steps,slug,' . $onboardingStep->id,
            'description' => 'nullable|string',
            'type' => 'required|string',
            'content' => 'nullable',
            'is_active' => 'boolean',
            'upsc_integration' => 'boolean',
        ]);

        $content = $request->content;
        if (is_string($content)) {
            $content = json_decode($content, true);
        }

        $onboardingStep->update([
            'title' => $request->title,
            'slug' => $request->slug,
            'description' => $request->description,
            'type' => $request->type,
            'content' => $content,
            'is_active' => $request->has('is_active'),
            'upsc_integration' => $request->has('upsc_integration'),
        ]);

        return redirect()->route('admin.onboarding.index', ['step' => $onboardingStep->id])
            ->with('success', 'Step updated successfully.');
    }

    /**
     * Reorder steps.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:onboarding_steps,id',
        ]);

        foreach ($request->ids as $index => $id) {
            OnboardingStep::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Remove an onboarding step.
     */
    public function destroy(OnboardingStep $onboardingStep)
    {
        $onboardingStep->delete();
        
        // Re-sequence
        $steps = OnboardingStep::orderBy('sort_order')->get();
        foreach ($steps as $index => $step) {
            $step->update(['sort_order' => $index + 1]);
        }

        return redirect()->route('admin.onboarding.index')
            ->with('success', 'Step deleted successfully.');
    }
}
