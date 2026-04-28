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

    #[Url(as: 'tags')]
    public $tagFilters = []; // group_id => tag_slug

    #[Url(as: 'sort', except: 'latest')]
    public $sort = 'latest';

    public function updating($name)
    {
        if (in_array($name, ['search', 'type', 'region', 'year', 'sort']) || str_starts_with($name, 'tagFilters')) {
            $this->resetPage();
        }
    }

    #[On('membership-activated')]
    public function refresh()
    {
        // Triggers re-render to reflect new access levels
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

    public function render(LibraryFrontendService $libraryService, LibraryAccessService $accessService)
    {
        $filters = [
            'search' => $this->search,
            'type' => $this->type,
            'region' => $this->region,
            'year' => $this->year,
            'tag_filters' => $this->tagFilters,
            'sort' => $this->sort,
        ];

        $user = Auth::user();

        return view('livewire.public.library.library-index', [
            'featuredResources' => $libraryService->getFeaturedResources(3),
            'latestResources' => $libraryService->getLatestResources(6),
            'recommendedResources' => $libraryService->getRecommendedResources($user, 3),
            'tagGroups' => $libraryService->getPublicTagGroups(),
            'topics' => $libraryService->getBrowseTopics(8),
            'resourceTypes' => $libraryService->getResourceTypes(),
            'regions' => $libraryService->getRegions(),
            'publicationYears' => $libraryService->getPublicationYears(),
            'resources' => $libraryService->searchResources($filters, 12),
            'accessService' => $accessService,
        ])->title('Research Library - AnthroConnect');
    }
}
