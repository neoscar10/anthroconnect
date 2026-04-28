<?php

namespace App\Livewire\Public\Community;

use App\Services\Community\CommunityDiscussionService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class CommunityIndexPage extends Component
{
    use WithPagination;

    public $search = '';
    public $topicId = '';
    public $tagFilters = []; // group_id => tag_slug
    public $tab = 'all'; // all, hot, newest, unsolved
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'topicId' => ['as' => 'topic', 'except' => ''],
        'tagFilters' => ['as' => 'tags', 'except' => []],
        'tab' => ['except' => 'all'],
    ];

    /**
     * Reset pagination when filters change.
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'topicId', 'tab']) || str_starts_with($propertyName, 'tagFilters')) {
            $this->resetPage();
        }
    }

    /**
     * UI Action: Select or toggle a topic.
     */
    public function selectTopic($id)
    {
        $this->topicId = ($this->topicId == $id) ? '' : $id;
        $this->resetPage();
    }

    public function setTag($groupId, $slug)
    {
        if (($this->tagFilters[$groupId] ?? null) === $slug) {
            unset($this->tagFilters[$groupId]);
        } else {
            $this->tagFilters[$groupId] = $slug;
        }
        $this->resetPage();
    }

    /**
     * UI Action: Switch between discovery tabs.
     */
    public function selectTab($tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    /**
     * UI Action: Clear all active filters.
     */
    public function resetFilters()
    {
        $this->reset(['search', 'topicId', 'tagFilters', 'tab']);
        $this->resetPage();
    }

    #[On('membership-activated')]
    public function refresh()
    {
        // Triggers re-render
    }

    public function render(CommunityDiscussionService $service)
    {
        $discussions = $service->getDiscussionFeed([
            'search' => $this->search,
            'topic' => $this->topicId,
            'tag_filters' => $this->tagFilters,
            'tab' => $this->tab,
            'per_page' => $this->perPage,
        ]);

        return view('livewire.public.community.community-index-page', [
            'discussions' => $discussions,
            'tagGroups' => $service->getPublicTagGroups(),
            'browseTopics' => $service->getBrowseTopics(),
            'popularDiscussions' => $service->getPopularDiscussions(),
            'trendingTags' => $service->getTrendingTags(),
            'expertSpotlight' => $service->getExpertSpotlight(),
        ])->layout('layouts.public', ['title' => 'Community | AnthroConnect']);
    }
}
