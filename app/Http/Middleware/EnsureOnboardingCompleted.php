<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    protected $onboardingService;

    public function __construct(\App\Services\Onboarding\UserOnboardingService $onboardingService)
    {
        $this->onboardingService = $onboardingService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If not logged in, continue (auth middleware will handle it)
        if (!$user) {
            return $next($request);
        }

        // Check if user requires onboarding
        if ($this->onboardingService->requiresOnboarding($user)) {
            // Avoid redirect loops
            if (!$request->is('onboarding*') && !$request->is('logout')) {
                return redirect()->route('onboarding.index');
            }
        }

        return $next($request);
    }
}
