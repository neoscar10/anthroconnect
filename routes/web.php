<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Onboarding\StepPage;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/explore', \App\Livewire\Public\ExplorePage::class)->name('explore.index');
Route::get('/encyclopedia', \App\Livewire\Pages\Encyclopedia\EncyclopediaIndexPage::class)->name('encyclopedia.index');

// Authenticated User Routes (Onboarding Enforced)
Route::middleware(['auth', 'onboarding'])->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Onboarding Flow
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding/{stepSlug?}', function ($stepSlug = null) {
        $onboardingService = app(\App\Services\Onboarding\UserOnboardingService::class);
        if (!$onboardingService->requiresOnboarding(auth()->user())) {
            return redirect()->route('dashboard');
        }
        return view('pages.onboarding', compact('stepSlug'));
    })->name('onboarding.show');

    // Alias for index
    Route::get('/onboarding', function () {
        $onboardingService = app(\App\Services\Onboarding\UserOnboardingService::class);
        if (!$onboardingService->requiresOnboarding(auth()->user())) {
            return redirect()->route('dashboard');
        }
        return view('pages.onboarding');
    })->name('onboarding.index');
});

// Admin Area
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest Admin Routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'login']);
    });

    // Protected Admin Routes
    Route::middleware(['auth', 'role:Super Admin|Admin'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
        
        // Onboarding Management
        Route::prefix('onboarding')->name('onboarding.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\OnboardingController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\OnboardingController::class, 'store'])->name('store');
            Route::post('/reorder', [App\Http\Controllers\Admin\OnboardingController::class, 'reorder'])->name('reorder');
            Route::patch('/{onboardingStep}', [App\Http\Controllers\Admin\OnboardingController::class, 'update'])->name('update');
            Route::delete('/{onboardingStep}', [App\Http\Controllers\Admin\OnboardingController::class, 'destroy'])->name('destroy');
        });

        // Membership Management
        Route::prefix('membership')->name('membership.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MembershipController::class, 'index'])->name('index');
            Route::post('/configure', [App\Http\Controllers\Admin\MembershipController::class, 'configure'])->name('configure');
        });

        // Explore Content Management
        Route::prefix('explore')->name('explore.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ExploreController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\ExploreController::class, 'store'])->name('store');
            Route::put('/{exploreArticle}', [App\Http\Controllers\Admin\ExploreController::class, 'update'])->name('update');
            Route::delete('/{exploreArticle}', [App\Http\Controllers\Admin\ExploreController::class, 'destroy'])->name('destroy');
            Route::patch('/{exploreArticle}/toggle-featured', [App\Http\Controllers\Admin\ExploreController::class, 'toggleFeatured'])->name('toggle-featured');
        });

        // Global Topics Management
        Route::prefix('topics')->name('topics.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\TopicController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\TopicController::class, 'store'])->name('store');
            Route::put('/{topic}', [App\Http\Controllers\Admin\TopicController::class, 'update'])->name('update');
            Route::delete('/{topic}', [App\Http\Controllers\Admin\TopicController::class, 'destroy'])->name('destroy');
            Route::patch('/{topic}/toggle-status', [App\Http\Controllers\Admin\TopicController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Encyclopedia Management Module
        Route::prefix('encyclopedia')->name('encyclopedia.')->group(function () {
            Route::get('/core-concepts', \App\Livewire\Admin\Encyclopedia\CoreConcepts\Index::class)->name('core-concepts.index');
            Route::get('/major-theories', \App\Livewire\Admin\Encyclopedia\MajorTheories\Index::class)->name('major-theories.index');
            Route::get('/anthropologists', \App\Livewire\Admin\Encyclopedia\Anthropologists\Index::class)->name('anthropologists.index');
        });

        Route::post('/logout', [App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->name('logout');
    });

    // Landing /admin redirect
    Route::get('/', function() {
        return redirect()->route('admin.dashboard');
    });
});

require __DIR__.'/auth.php';
