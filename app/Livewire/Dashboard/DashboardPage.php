<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\Membership\MembershipService;
use App\Services\Membership\MembershipPurchaseService;
use App\Models\MembershipSetting;
use App\Models\UserMembership;
use Exception;

use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class DashboardPage extends Component
{
    use WithFileUploads;

    public $new_avatar;
    public array $continueLearning = [];
    public array $recommendedItems = [];
    public array $discussions = [];
    public array $quickActions = [];
    public array $recentActivities = [];
    public array $upscResources = [];
    public array $userInterests = [];
    public ?\App\Models\ExploreArticle $featuredExploreArticle = null;
    
    // Profile Stats
    public int $interestsCount = 0;
    public int $modulesCompletedCount = 0;
    public int $contributionsCount = 0;
    public int $discoveredNodes = 42; // Placeholder for now
    public int $majorBranches = 5;    // Placeholder for now

    // Membership State
    public ?MembershipSetting $globalSetting = null;
    public ?UserMembership $userMembership = null;
    public bool $isMember = false;

    public function mount(): void
    {
        $this->loadMembershipData();
        $this->loadProfileStats();
    }

    #[On('membership-activated')]
    public function refresh(): void
    {
        $this->loadMembershipData();
    }

    public function updatedNewAvatar()
    {
        $this->validate([
            'new_avatar' => ['image', 'max:2048'],
        ]);

        $user = \App\Models\User::find(Auth::id());

        // Delete old avatar if exists
        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $this->new_avatar->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        $this->new_avatar = null;
        
        session()->flash('status', 'profile-updated');
    }

    protected function loadMembershipData(): void
    {
        $membershipService = app(MembershipService::class);
        $this->globalSetting = $membershipService->getCurrentSettings();
        $this->isMember = Auth::user()?->isMember() ?? false;
        
        if ($this->isMember) {
            $this->userMembership = Auth::user()->membership;
        }

        $this->loadDynamicContent();
    }

    protected function loadDynamicContent(): void
    {
        $user = Auth::user();
        if (!$user) return;

        // 1. Continue Learning (Real LMS Progress)
        $progress = $user->lessonProgress()
            ->with('module.lessons')
            ->get()
            ->groupBy('lms_module_id');

        $this->continueLearning = $progress->map(function ($items, $moduleId) {
            $module = $items->first()->module;
            $totalLessons = $module->lessons->count();
            $completedLessons = $items->whereNotNull('completed_at')->count();
            $percent = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

            return [
                'tag' => $module->category ?? 'General',
                'progress' => $percent,
                'title' => $module->title,
                'description' => str($module->description)->stripTags()->limit(100),
                'slug' => $module->slug,
                'lesson_slug' => $items->sortByDesc('updated_at')->first()->lesson->slug ?? null,
            ];
        })->sortByDesc('progress')->take(2)->values()->toArray();

        // 2. Recommended for You (Real Library Resources)
        $this->recommendedItems = \App\Models\LibraryResource::published()
            ->where('is_recommended', true)
            ->latest()
            ->take(2)
            ->get()
            ->map(fn ($r) => [
                'title' => $r->title,
                'description' => $r->author_display ?: 'Scholarly Resource',
                'meta_left' => $r->resourceType->name ?? 'Book',
                'meta_right' => $r->publication_year ?: 'Latest',
                'image' => $r->cover_url,
                'meta_left_icon' => 'menu_book',
                'meta_right_icon' => 'calendar_today',
                'slug' => $r->slug,
            ])->toArray();

        // 3. Explore Humanity (Featured Article)
        $this->featuredExploreArticle = \App\Models\ExploreArticle::published()->featured()->latest()->first();
    }

    protected function loadProfileStats(): void
    {
        $user = Auth::user();
        if (!$user) return;

        // Calculate Contributions (Discussions + Replies + Exam Submissions)
        $this->contributionsCount = $user->communityDiscussions()->count() + 
                                  $user->communityReplies()->count() + 
                                  $user->examSubmissions()->count();

        // Modules Completed
        $this->modulesCompletedCount = $user->lessonProgress()->whereNotNull('completed_at')->count();

        // Interests (Placeholder tags for now)
        $this->userInterests = ['Cultural Anthropology', 'Kinship Systems', 'Indian Tribes', 'Structuralism', 'Linguistics'];
        $this->interestsCount = count($this->userInterests);
    }

    /**
     * Open the checkout modal via global event.
     */
    public function openCheckout(): void
    {
        if ($this->isMember) return;
        $this->dispatch('open-upgrade-modal');
    }

    public function getUserFirstNameProperty(): string
    {
        $name = trim((string) (Auth::user()?->name ?? 'Scholar'));
        return explode(' ', $name)[0] ?? 'Scholar';
    }

    public function getUserInitialsProperty(): string
    {
        $name = trim((string) (Auth::user()?->name ?? 'Scholar'));
        $parts = preg_split('/\s+/', $name) ?: [];
        $initials = collect($parts)->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
        return $initials !== '' ? $initials : 'SC';
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-page');
    }
}
