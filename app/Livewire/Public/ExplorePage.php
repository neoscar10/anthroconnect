<?php

namespace App\Livewire\Public;

use App\Services\Explore\ExplorePublicService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.public')]
class ExplorePage extends Component
{
    use WithPagination;

    #[Url(as: 'topic_id', except: '')]
    public $topicId = '';

    #[On('membership-activated')]
    public function refresh()
    {
        // Triggers re-render
    }

    public function setTopic($id)
    {
        $this->topicId = $id;
        $this->resetPage(); // Reset pagination when filter changes
    }

    public function render(ExplorePublicService $exploreService)
    {
        $filters = ['topic_id' => $this->topicId];

        $topics = $exploreService->getPublicTopics();
        $featuredArticles = $exploreService->getFeaturedArticles($this->topicId);
        
        $featuredIds = $featuredArticles->pluck('id')->toArray();
        $articles = $exploreService->getPublishedArticles(array_merge($filters, ['exclude_ids' => $featuredIds]));

        return view('livewire.public.explore-page', compact('topics', 'featuredArticles', 'articles'))
            ->title('Explore Humanity');
    }
}
