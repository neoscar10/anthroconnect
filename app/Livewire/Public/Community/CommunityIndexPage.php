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
    public $tag = '';
    public $tab = 'all'; // all, hot, newest, unsolved
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'topicId' => ['as' => 'topic', 'except' => ''],
        'tag' => ['except' => ''],
        'tab' => ['except' => 'all'],
    ];

    /**
     * Reset pagination when filters change.
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'topicId', 'tag', 'tab'])) {
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
        $this->reset(['search', 'topicId', 'tag', 'tab']);
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
            'tag' => $this->tag,
            'tab' => $this->tab,
            'per_page' => $this->perPage,
        ]);

        return view('livewire.public.community.community-index-page', [
            'discussions' => $discussions,
            'browseTopics' => $service->getBrowseTopics(),
            'popularDiscussions' => $service->getPopularDiscussions(),
            'trendingTags' => $service->getTrendingTags(),
            'expertSpotlight' => $service->getExpertSpotlight(),
        ])->layout('layouts.public', ['title' => 'Community | AnthroConnect']);
    }
}
