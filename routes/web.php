<?php

use App\Livewire\Onboarding\StepPage;
use App\Livewire\Profile\ProfilePage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/explore', \App\Livewire\Public\ExplorePage::class)->name('explore.index');
Route::get('/explore/{slug}', \App\Livewire\Public\ExploreDetail::class)->name('explore.show');

// Public LMS Routes
Route::get('/modules', \App\Livewire\Public\Lms\ModulesIndex::class)->name('modules.index');
Route::get('/modules/{slug}', \App\Livewire\Public\Lms\ModuleShow::class)->name('modules.show');
Route::get('/modules/{moduleSlug}/lessons/{lessonSlug}', \App\Livewire\Public\Lms\LessonShow::class)->name('lessons.show');

Route::get('/encyclopedia', \App\Livewire\Pages\Encyclopedia\EncyclopediaIndexPage::class)->name('encyclopedia.index');
Route::get('/encyclopedia/anthropologists/{slug}', [App\Http\Controllers\PublicEncyclopediaController::class, 'showAnthropologist'])->name('encyclopedia.anthropologists.show');
Route::get('/encyclopedia/theories/{slug}', [App\Http\Controllers\PublicEncyclopediaController::class, 'showTheory'])->name('encyclopedia.theories.show');
Route::get('/encyclopedia/concepts/{slug}', [App\Http\Controllers\PublicEncyclopediaController::class, 'showConcept'])->name('encyclopedia.concepts.show');

Route::get('/knowledge-map', \App\Livewire\Frontend\KnowledgeMap\KnowledgeMapPage::class)->name('knowledge-map.show');
Route::get('/upsc-anthropology', \App\Livewire\Pages\UpscHubPage::class)->name('upsc.hub');

// Public Community Routes
Route::get('/community', \App\Livewire\Public\Community\CommunityIndexPage::class)->name('community.index');
Route::get('/community/{slug}', \App\Livewire\Public\Community\CommunityDiscussionShowPage::class)->name('community.show');

// OTP Verification Route
Route::middleware(['auth'])->group(function () {
    Route::get('/verify-otp', \App\Livewire\Auth\VerifyOtpPage::class)->name('user.otp.verify');
});

// Authenticated User Routes (OTP & Onboarding Enforced)
Route::middleware(['auth', 'otp.verified', 'onboarding'])->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', ProfilePage::class)->name('profile.edit');
});

// Onboarding Flow
Route::middleware(['auth', 'otp.verified'])->group(function () {
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
            Route::patch('/{exploreArticle}/toggle-members-only', [App\Http\Controllers\Admin\ExploreController::class, 'toggleMembersOnly'])->name('toggle-members-only');
            Route::post('/reorder', [App\Http\Controllers\Admin\ExploreController::class, 'reorder'])->name('reorder');
        });

        // Unified Tags & Tag Groups Management
        Route::get('/tags', \App\Livewire\Admin\Tags\TagsIndex::class)->name('tags.index');

        // Knowledge Map Management
        Route::prefix('knowledge-map')->name('knowledge-maps.')->group(function () {
            Route::get('/', \App\Livewire\Admin\KnowledgeMap\KnowledgeMapBuilder::class)->name('index');
            Route::get('/builder', \App\Livewire\Admin\KnowledgeMap\KnowledgeMapBuilder::class)->name('builder');
            Route::get('/settings', \App\Livewire\Admin\KnowledgeMap\KnowledgeMapForm::class)->name('edit');
            Route::get('/learning-paths', \App\Livewire\Admin\KnowledgeMap\KnowledgeMapLearningPaths::class)->name('learning-paths');
        });

        // Encyclopedia Management Module
        Route::prefix('encyclopedia')->name('encyclopedia.')->group(function () {
            Route::get('/core-concepts', \App\Livewire\Admin\Encyclopedia\CoreConcepts\Index::class)->name('core-concepts.index');
            Route::get('/major-theories', \App\Livewire\Admin\Encyclopedia\MajorTheories\Index::class)->name('major-theories.index');
            Route::get('/anthropologists', \App\Livewire\Admin\Encyclopedia\Anthropologists\Index::class)->name('anthropologists.index');
        });

        // Simplified LMS Module
        Route::prefix('lms')->name('lms.')->group(function () {
            Route::get('/modules', \App\Livewire\Admin\Lms\Modules\Index::class)->name('modules.index');
            Route::get('/modules/create', \App\Livewire\Admin\Lms\Modules\Editor::class)->name('modules.create');
            Route::get('/modules/{lmsModule}/edit', \App\Livewire\Admin\Lms\Modules\Editor::class)->name('modules.edit');
            Route::get('/resources', \App\Livewire\Admin\Lms\Resources\Index::class)->name('resources.index');
        });

        // Community Management
        Route::prefix('community')->name('community.')->group(function () {
            Route::get('/topics', \App\Livewire\Admin\Community\TopicIndex::class)->name('topics.index');
            Route::get('/discussions', \App\Livewire\Admin\Community\DiscussionIndex::class)->name('discussions.index');
            Route::get('/discussions/{id}', \App\Livewire\Admin\Community\DiscussionDetail::class)->name('discussions.show');
        });

        // Exams / Answer Writing Management
        Route::prefix('exams')->name('exams.')->group(function () {
            Route::get('/questions', \App\Livewire\Admin\Exams\Questions\ExamQuestionIndex::class)->name('questions.index');
            Route::get('/submissions', \App\Livewire\Admin\Exams\Submissions\ExamSubmissionIndex::class)->name('submissions.index');
            Route::get('/submissions/{id}', \App\Livewire\Admin\Exams\Submissions\ExamSubmissionDetail::class)->name('submissions.show');
        });

        // Research Library Management
        Route::prefix('library')->name('library.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\Library\LibraryDashboardController::class, 'index'])->name('dashboard');
            
            Route::prefix('resources')->name('resources.')->group(function () {
                Route::get('/', App\Livewire\Admin\Library\Resources\Index::class)->name('index');
            });

            Route::prefix('resource-types')->name('resource-types.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\Library\LibraryResourceTypeController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\Admin\Library\LibraryResourceTypeController::class, 'store'])->name('store');
                Route::put('/{resourceType}', [App\Http\Controllers\Admin\Library\LibraryResourceTypeController::class, 'update'])->name('update');
                Route::delete('/{resourceType}', [App\Http\Controllers\Admin\Library\LibraryResourceTypeController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('regions')->name('regions.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\Library\LibraryRegionController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\Admin\Library\LibraryRegionController::class, 'store'])->name('store');
                Route::put('/{region}', [App\Http\Controllers\Admin\Library\LibraryRegionController::class, 'update'])->name('update');
                Route::delete('/{region}', [App\Http\Controllers\Admin\Library\LibraryRegionController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('tags')->name('tags.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\Library\LibraryTagController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\Admin\Library\LibraryTagController::class, 'store'])->name('store');
                Route::put('/{tag}', [App\Http\Controllers\Admin\Library\LibraryTagController::class, 'update'])->name('update');
                Route::delete('/{tag}', [App\Http\Controllers\Admin\Library\LibraryTagController::class, 'destroy'])->name('destroy');
            });
        });

        Route::post('/logout', [App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->name('logout');
    });

    // Landing /admin redirect
    Route::get('/', function() {
        return redirect()->route('admin.dashboard');
    });
});

require __DIR__.'/auth.php';

Route::get('/library', \App\Livewire\Public\Library\LibraryIndex::class)->name('library.index');
Route::get('/library/{slug}', \App\Livewire\Public\Library\LibraryShow::class)->name('library.show');
Route::get('/library/{resource:slug}/download', [App\Http\Controllers\Frontend\LibraryController::class, 'download'])->name('library.download');

// Public Exams / Answer Writing Routes
Route::get('/exams', \App\Livewire\Frontend\Exams\ExamQuestionListPage::class)->name('exams.index');
Route::get('/exams/{slug}', \App\Livewire\Frontend\Exams\ExamQuestionDetailPage::class)->name('exams.show');

