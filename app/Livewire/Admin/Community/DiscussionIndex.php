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
    public $status_filter = '';
    public $tagFilters = []; // key: group_id, value: tag_id

    // Edit Modal State
    public $isModalOpen = false;
    public $editingId = null;
    public $editTitle = '';
    public $editBody = '';
    public $editStatus = 'published';
    public $selectedTags = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'tagFilters' => ['except' => []],
        'status_filter' => ['except' => ''],
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'status_filter']) || str_starts_with($propertyName, 'tagFilters')) {
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

    public function editDiscussion($id)
    {
        $discussion = CommunityDiscussion::with('tags')->findOrFail($id);
        $this->editingId = $discussion->id;
        $this->editTitle = $discussion->title;
        $this->editBody = $discussion->body;
        $this->editStatus = $discussion->status;
        $this->selectedTags = $discussion->tags->pluck('id')->toArray();
        
        $this->isModalOpen = true;
        $this->dispatch('set-tags', id: 'discussion-tag-selector', tags: $this->selectedTags);
    }

    public function saveDiscussion()
    {
        $this->validate([
            'editTitle' => 'required|string|max:255',
            'editBody' => 'required|string',
            'editStatus' => 'required|in:published,hidden,archived',
        ]);

        $discussion = CommunityDiscussion::findOrFail($this->editingId);
        $discussion->update([
            'title' => $this->editTitle,
            'body' => $this->editBody,
            'status' => $this->editStatus,
        ]);

        $discussion->syncTags($this->selectedTags);

        $this->isModalOpen = false;
        session()->flash('success', 'Discussion updated successfully.');
    }

    public function render(CommunityDiscussionService $service)
    {
        $query = CommunityDiscussion::with(['author', 'tags'])
            ->when($this->search, function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('body', 'like', "%{$this->search}%");
            });

        foreach ($this->tagFilters as $groupId => $tagId) {
            if ($tagId) {
                $query->withTag($tagId);
            }
        }

        $discussions = $query->when($this->status_filter, function($q) {
                $q->where('status', $this->status_filter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $filterableTagGroups = \App\Models\TagGroup::getGroupsWithUsage(CommunityDiscussion::class);

        return view('livewire.admin.community.discussion-index', [
            'discussions' => $discussions,
            'filterableTagGroups' => $filterableTagGroups
        ])->layout('layouts.admin', ['title' => 'Community Discussion Management']);
    }
}
