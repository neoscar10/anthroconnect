<?php

namespace App\Livewire\Public;

use App\Services\Explore\ExplorePublicService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class ExplorePage extends Component
{
    use WithPagination;

    #[Url(as: 'topic_id', except: '')]
    public $topicId = '';

    public function setTopic($id)
    {
        $this->topicId = $id;
        $this->resetPage(); // Reset pagination when filter changes
    }

    public function render(ExplorePublicService $exploreService)
    {
        $filters = ['topic_id' => $this->topicId];

        $topics = $exploreService->getPublicTopics();
        $featuredArticle = $exploreService->getFeaturedArticle($this->topicId);
        
        $articles = $exploreService->getPublishedArticles($filters);

        return view('livewire.public.explore-page', compact('topics', 'featuredArticle', 'articles'))
            ->title('Explore Humanity');
    }
}
