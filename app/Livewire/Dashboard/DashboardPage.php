<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Services\Membership\MembershipService;
use App\Services\Membership\MembershipPurchaseService;
use App\Models\MembershipSetting;
use App\Models\UserMembership;
use Exception;

class DashboardPage extends Component
{
    public array $continueLearning = [];
    public array $recommendedItems = [];
    public array $discussions = [];
    public array $quickActions = [];
    public array $recentActivities = [];
    public array $upscResources = [];

    // Membership State
    public ?MembershipSetting $globalSetting = null;
    public ?UserMembership $userMembership = null;
    public bool $isMember = false;

    public function mount(): void
    {
        $this->loadMembershipData();

        $this->continueLearning = [
            [
                'tag' => 'Social Anthropology',
                'progress' => 65,
                'title' => 'Kinship Systems in Anthropology',
                'description' => 'Deep dive into lineage, descent, and marriage patterns across global cultures.',
            ],
            [
                'tag' => 'Religion & Belief',
                'progress' => 30,
                'title' => 'Cultural Rituals and Belief Systems',
                'description' => 'Understanding the structural role of rituals in maintaining social cohesion.',
            ],
        ];

        $this->recommendedItems = [
            [
                'title' => 'Cultural Anthropology Foundations',
                'description' => 'Core theories and ethnographies.',
                'meta_left' => '12h content',
                'meta_right' => '4.9',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBdfXXOcI7ZqcSBTDF-sVH5LXKTuymNxoQbAaO7mUyph7wG5C6aVztg5Y0futoadkcBOdMt3T4yG8YeQQWe1_iCYDLksa-jzv27-2YLeF2Px8PvGNO52NpF06uOdZoqmbk6mc2vfjyD9OD8Bb6qQJ4dwP0esDGRR0mI_AEmLw3xbPgc0TQ_WreHv6Ard3AHrq8pBUtjFGs0C4RC_LIG9g4UcBQbgDfITfueWch3ubqKGOYaLl7e6ciNHDG8ZyGnb10Fz6ewC4oickc',
                'meta_left_icon' => 'timer',
                'meta_right_icon' => 'star',
            ],
            [
                'title' => 'Indian Anthropology for UPSC',
                'description' => 'Civilizational perspective & tribal issues.',
                'meta_left' => '45h course',
                'meta_right' => 'UPSC Expert',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAbK8qE5BOFE5q5iEPhL_0waFjGWmING_hEhRy1uSshCXZjup8szESRvnGr8Kj2tYhTd37PgQkjKAGzpA2nXQdt3FDLaMjSsX9mfyoMu_hP1El69dkj7oz3_w56aaExnm58hWCE_QSV_aXBqpWZys2COHXTSUjXMSrCobLe0eLkmaExZC9Us4QFkEkNtudGB9BOCJZycsVaa-_cG_7ZA-39NMdOMTW2X2Msq79plg_n60ki7xvEWfIIK3PILCML6djpfxp490r7_VY',
                'meta_left_icon' => 'timer',
                'meta_right_icon' => 'verified',
            ],
        ];

        $this->discussions = [
            [
                'title' => "The role of fieldwork in the digital age: Is 'remote ethnography' valid?",
                'replies' => '24 replies',
                'avatars' => ['AM', 'KP', '+5'],
                'latest' => 'Latest reply 2h ago',
            ],
            [
                'title' => 'Comparing structural-functionalism and post-modernist views on kinship.',
                'replies' => '18 replies',
                'avatars' => ['RT', 'SL'],
                'latest' => 'Latest reply 5h ago',
            ],
        ];

        $this->quickActions = [
            ['icon' => 'menu_book', 'label' => 'Reading List'],
            ['icon' => 'edit_note', 'label' => 'Saved Notes'],
            ['icon' => 'collections_bookmark', 'label' => 'Research Library'],
        ];

        $this->recentActivities = [
            ['icon' => 'visibility', 'text' => 'You viewed "The Kula Ring"', 'time' => '10 minutes ago'],
            ['icon' => 'comment', 'text' => 'Replied to "Tribal Policy"', 'time' => '2 hours ago'],
            ['icon' => 'download', 'text' => 'Downloaded Paper-I Archive', 'time' => 'Yesterday'],
        ];

        $this->upscResources = [
            [
                'title' => 'Previous Year Questions',
                'description' => 'Topic-wise segregation from 2013-2023.',
                'cta' => 'Access Files →',
                'border' => 'border-primary',
            ],
            [
                'title' => 'Case Study Bank',
                'description' => 'Indian tribal issues and government reports.',
                'cta' => 'Explore →',
                'border' => 'border-olive',
            ],
            [
                'title' => 'Answer Writing Lab',
                'description' => 'Get your daily answers peer-reviewed.',
                'cta' => 'Start Practice →',
                'border' => 'border-sand',
            ],
        ];
    }

    protected function loadMembershipData(): void
    {
        $membershipService = app(MembershipService::class);
        $this->globalSetting = $membershipService->getCurrentSettings();
        $this->isMember = Auth::user()?->isMember() ?? false;
        
        if ($this->isMember) {
            $this->userMembership = Auth::user()->membership;
        }
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
