<?php

namespace App\Livewire\Admin\Community;

use App\Models\Community\CommunityDiscussion;
use App\Models\Community\CommunityTopic;
use App\Services\Community\CommunityDiscussionService;
use Livewire\Component;
use Livewire\WithPagination;

class DiscussionIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $topic_filter = '';
    public $status_filter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'topic_filter' => ['except' => ''],
        'status_filter' => ['except' => ''],
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'topic_filter', 'status_filter'])) {
            $this->resetPage();
        }
    }

    /**
     * Quick actions for discussion visibility and highlights.
     */
    public function toggleFeatured($id)
    {
        $discussion = CommunityDiscussion::findOrFail($id);
        $discussion->update(['is_featured' => !$discussion->is_featured]);
    }

    public function toggleExpert($id)
    {
        $discussion = CommunityDiscussion::findOrFail($id);
        $discussion->update(['is_expert_spotlight' => !$discussion->is_expert_spotlight]);
    }

    public function toggleTrending($id)
    {
        $discussion = CommunityDiscussion::findOrFail($id);
        $discussion->update(['is_trending' => !$discussion->is_trending]);
    }

    public function updateStatus($id, $status)
    {
        $discussion = CommunityDiscussion::findOrFail($id);
        $discussion->update(['status' => $status]);
    }

    public function deleteDiscussion($id)
    {
        $discussion = CommunityDiscussion::findOrFail($id);
        $discussion->delete();
        session()->flash('success', 'Discussion moved to trash.');
    }

    public function render(CommunityDiscussionService $service)
    {
        $discussions = CommunityDiscussion::with(['author', 'topic'])
            ->when($this->search, function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('body', 'like', "%{$this->search}%");
            })
            ->when($this->topic_filter, function($q) {
                $q->where('community_topic_id', $this->topic_filter);
            })
            ->when($this->status_filter, function($q) {
                $q->where('status', $this->status_filter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $topics = CommunityTopic::orderBy('name')->get();

        return view('livewire.admin.community.discussion-index', [
            'discussions' => $discussions,
            'topics' => $topics
        ])->layout('layouts.admin', ['title' => 'Community Discussion Management']);
    }
}
