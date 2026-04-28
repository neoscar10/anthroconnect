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

    #[Url(as: 'tag_id', except: '')]
    public $tagId = '';

    #[On('membership-activated')]
    public function refresh()
    {
        // Triggers re-render
    }

    public function setTag($id)
    {
        $this->tagId = $id;
        $this->resetPage(); // Reset pagination when filter changes
    }

    public function render(ExplorePublicService $exploreService)
    {
        $filters = ['tag_id' => $this->tagId];

        $tagGroups = $exploreService->getPublicTagGroups();
        $featuredArticles = $exploreService->getFeaturedArticles($this->tagId);
        
        $featuredIds = $featuredArticles->pluck('id')->toArray();
        $articles = $exploreService->getPublishedArticles(array_merge($filters, ['exclude_ids' => $featuredIds]));

        return view('livewire.public.explore-page', compact('tagGroups', 'featuredArticles', 'articles'))
            ->title('Explore Humanity');
    }
}
