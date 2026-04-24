<?php

namespace App\Livewire\Public;

use App\Services\Explore\ExplorePublicService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.public')]
class ExploreDetail extends Component
{
    public $article;
    public $relatedArticles;

    public function mount(string $slug, ExplorePublicService $exploreService)
    {
        $this->article = $exploreService->getArticleBySlug($slug);

        if (!$this->article) {
            abort(404);
        }

        // Protection: Redirect if unauthorized
        if (!$this->article->canAccess(auth()->user())) {
            return redirect()->route('explore.index')
                ->with('error', 'This narrative is reserved for the AnthroConnect Scholar community. Please upgrade your membership to unlock full access.');
        }

        $this->loadData($exploreService);
    }

    #[On('membership-activated')]
    public function refresh(ExplorePublicService $exploreService)
    {
        $this->loadData($exploreService);
    }

    protected function loadData(ExplorePublicService $exploreService)
    {
        $this->relatedArticles = $exploreService->getRelatedArticles($this->article, 2);
    }

    public function render()
    {
        return view('livewire.public.explore-detail')
            ->title($this->article->title . ' - Explore Humanity');
    }
}
