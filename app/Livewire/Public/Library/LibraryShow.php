<?php

namespace App\Livewire\Public\Library;

use App\Models\LibraryResource;
use App\Services\Library\LibraryFrontendService;
use App\Services\Library\LibraryAccessService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.public')]
class LibraryShow extends Component
{
    public LibraryResource $resource;
    public $access;
    public $relatedResources;
    public $relatedLearningItems;
    public $relatedDiscussions;

    public function mount(string $slug, LibraryFrontendService $libraryService, LibraryAccessService $accessService)
    {
        $this->resource = $libraryService->getResourceBySlug($slug);

        if (!$this->resource) {
            abort(404);
        }

        abort_unless($libraryService->isPublished($this->resource), 404);

        $this->loadData($libraryService, $accessService);
    }

    #[On('membership-activated')]
    public function refresh(LibraryFrontendService $libraryService, LibraryAccessService $accessService)
    {
        $this->loadData($libraryService, $accessService);
    }

    protected function loadData(LibraryFrontendService $libraryService, LibraryAccessService $accessService)
    {
        $user = Auth::user();
        $this->access = $accessService->check($user, $this->resource);

        $this->resource->load([
            'resourceType',
            'region',
            'topics',
            'tags',
        ]);

        $this->relatedResources = $libraryService->getRelatedResources($this->resource, 4);
        $this->relatedLearningItems = $libraryService->getRelatedLearning($this->resource);
        $this->relatedDiscussions = $libraryService->getRelatedDiscussions($this->resource);
    }

    public function render()
    {
        return view('livewire.public.library.library-show')
            ->title($this->resource->title . ' - Research Library');
    }
}
