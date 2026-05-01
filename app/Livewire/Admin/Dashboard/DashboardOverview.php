<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\User;
use App\Models\UserMembership;
use App\Models\Lms\LmsModule;
use App\Models\Lms\LmsLesson;
use App\Models\LibraryResource;
use App\Models\Encyclopedia\CoreConcept;
use App\Models\Encyclopedia\MajorTheory;
use App\Models\Encyclopedia\Anthropologist;
use App\Models\Community\CommunityDiscussion;
use App\Models\Exam\ExamAnswerSubmission;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardOverview extends Component
{
    public $stats = [];
    public $chartData = [];
    public $subscriptionData = [];
    public $recentActivities = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadChartData();
        $this->loadSubscriptionData();
        $this->loadRecentActivities();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_users' => User::count(),
            'active_members' => UserMembership::where('status', 'active')->count(),
            'total_content' => LibraryResource::count() + LmsModule::count() + 
                             CoreConcept::count() + MajorTheory::count() + Anthropologist::count(),
            'active_threads' => CommunityDiscussion::count(),
            'pending_exams' => ExamAnswerSubmission::where('status', 'pending')->count(),
        ];
    }

    public function loadChartData()
    {
        // Monthly registrations for current year
        $registrations = User::select(
            DB::raw('count(id) as count'),
            DB::raw('DATE_FORMAT(created_at, "%m") as month')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('count', 'month')
        ->toArray();

        // Initialize all 12 months with 0
        $this->chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $this->chartData[$month] = $registrations[$month] ?? 0;
        }
    }

    public function loadSubscriptionData()
    {
        // Monthly subscriptions for current year
        $subscriptions = UserMembership::select(
            DB::raw('count(id) as count'),
            DB::raw('DATE_FORMAT(started_at, "%m") as month')
        )
        ->whereYear('started_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('count', 'month')
        ->toArray();

        // Initialize all 12 months with 0
        $this->subscriptionData = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $this->subscriptionData[$month] = $subscriptions[$month] ?? 0;
        }
    }

    public function generateReport($type)
    {
        $filename = "archivist_report_" . $type . "_" . date('Y-m-d') . ".csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');
            
            if ($type === 'registration_trends') {
                fputcsv($file, ['Month', 'New Registrations']);
                foreach ($this->chartData as $month => $count) {
                    $monthName = date("F", mktime(0, 0, 0, (int)$month, 10));
                    fputcsv($file, [$monthName, $count]);
                }
            } elseif ($type === 'subscription_trends') {
                fputcsv($file, ['Month', 'New Subscriptions']);
                foreach ($this->subscriptionData as $month => $count) {
                    $monthName = date("F", mktime(0, 0, 0, (int)$month, 10));
                    fputcsv($file, [$monthName, $count]);
                }
            } else {
                fputcsv($file, ['Metric', 'Count']);
                fputcsv($file, ['Total Users', $this->stats['total_users']]);
                fputcsv($file, ['Active Members', $this->stats['active_members']]);
                fputcsv($file, ['Digital Archive Items', $this->stats['total_content']]);
                fputcsv($file, ['Active Threads', $this->stats['active_threads']]);
                fputcsv($file, ['Pending Evaluations', $this->stats['pending_exams']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function loadRecentActivities()
    {
        // Combine recent items from various models
        $users = User::latest()->take(3)->get()->map(fn($u) => [
            'title' => 'New User Registered',
            'meta' => $u->name,
            'category' => 'Authentication',
            'time' => $u->created_at->diffForHumans(),
            'status' => 'NEW'
        ]);

        $resources = LibraryResource::latest()->take(3)->get()->map(fn($r) => [
            'title' => $r->title,
            'meta' => 'Added to Library',
            'category' => 'Research Paper',
            'time' => $r->created_at->diffForHumans(),
            'status' => 'PUBLISHED'
        ]);

        $this->recentActivities = $users->concat($resources)->sortByDesc('time')->take(5)->toArray();
    }

    public function render()
    {
        return view('livewire.admin.dashboard.dashboard-overview')
            ->layout('layouts.admin');
    }
}
