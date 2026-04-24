<?php

namespace App\Livewire\Public\Library;

use App\Services\Library\LibraryFrontendService;
use App\Services\Library\LibraryAccessService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.public')]
class LibraryIndex extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public $search = '';

    #[Url(as: 'type', except: '')]
    public $type = '';

    #[Url(as: 'region', except: '')]
    public $region = '';

    #[Url(as: 'year', except: '')]
    public $year = '';

    #[Url(as: 'topic', except: '')]
    public $topic = '';

    #[Url(as: 'sort', except: 'latest')]
    public $sort = 'latest';

    public function updating($name)
    {
        if (in_array($name, ['search', 'type', 'region', 'year', 'topic', 'sort'])) {
            $this->resetPage();
        }
    }

    #[On('membership-activated')]
    public function refresh()
    {
        // Triggers re-render to reflect new access levels
    }

    public function setTopic($slug)
    {
        $this->topic = $slug;
        $this->resetPage();
    }

    public function render(LibraryFrontendService $libraryService, LibraryAccessService $accessService)
    {
        $filters = [
            'search' => $this->search,
            'type' => $this->type,
            'region' => $this->region,
            'year' => $this->year,
            'topic' => $this->topic,
            'sort' => $this->sort,
        ];

        $user = Auth::user();

        return view('livewire.public.library.library-index', [
            'featuredResources' => $libraryService->getFeaturedResources(3),
            'latestResources' => $libraryService->getLatestResources(6),
            'recommendedResources' => $libraryService->getRecommendedResources($user, 3),
            'topics' => $libraryService->getBrowseTopics(8),
            'resourceTypes' => $libraryService->getResourceTypes(),
            'regions' => $libraryService->getRegions(),
            'publicationYears' => $libraryService->getPublicationYears(),
            'resources' => $libraryService->searchResources($filters, 12),
            'accessService' => $accessService,
        ])->title('Research Library - AnthroConnect');
    }
}
