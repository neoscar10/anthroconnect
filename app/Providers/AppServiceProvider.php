<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('layouts.admin', function ($view) {
            $view->with('pendingSubmissionsCount', \App\Models\Exam\ExamAnswerSubmission::where('status', 'submitted')->whereNull('evaluated_at')->count());
        });
    }
}
