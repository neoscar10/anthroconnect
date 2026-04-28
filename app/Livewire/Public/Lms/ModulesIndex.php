<?php

namespace App\Livewire\Public\Lms;

use App\Services\Lms\LmsPublicService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.public')]
class ModulesIndex extends Component
{
    use WithPagination;

    #[Url(as: 'tags')]
    public $tagFilters = []; // group_id => tag_slug

    #[Url(as: 'level', except: '')]
    public $level = '';

    #[Url(as: 'search', except: '')]
    public $search = '';

    #[On('membership-activated')]
    public function refresh()
    {
        // Triggers re-render
    }

    public function updating($name)
    {
        if (in_array($name, ['level', 'search']) || str_starts_with($name, 'tagFilters')) {
            $this->resetPage();
        }
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

    public function render(LmsPublicService $lmsService)
    {
        $filters = [
            'tag_filters' => $this->tagFilters,
            'level' => $this->level,
            'search' => $this->search,
        ];

        $tagGroups = $lmsService->getPublicTagGroups();
        $modules = $lmsService->getPublishedModules($filters);

        return view('livewire.public.lms.modules-index', [
            'tagGroups' => $tagGroups,
            'modules' => $modules,
        ])->title('Anthropology Modules');
    }
}
