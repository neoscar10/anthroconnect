<?php

namespace App\Livewire\Pages\Encyclopedia;

use App\Services\Encyclopedia\EncyclopediaFrontendService;
use Livewire\Component;
use Livewire\WithPagination;

class EncyclopediaIndexPage extends Component
{
    use WithPagination;

    public $search = '';
    public $category = 'All Categories';
    public $tagFilters = []; // group_id => tag_slug
    public $region = 'Global';

    public $isFiltered = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => 'All Categories'],
        'tagFilters' => ['except' => []],
        'region' => ['except' => 'Global'],
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'category', 'region']) || str_starts_with($propertyName, 'tagFilters')) {
            $this->isFiltered = ($this->search !== '' || 
                               $this->category !== 'All Categories' || 
                               !empty($this->tagFilters) || 
                               $this->region !== 'Global');
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
        $this->isFiltered = true;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'category', 'tagFilters', 'region', 'isFiltered']);
    }

    public function render(EncyclopediaFrontendService $service)
    {
        $tagGroups = $service->getPublicTagGroups();

        if ($this->isFiltered) {
            $results = $service->search([
                'search' => $this->search,
                'category' => $this->category,
                'tag_filters' => $this->tagFilters,
                'region' => $this->region,
            ]);

            return view('livewire.pages.encyclopedia.encyclopedia-index-page', [
                'anthropologists' => $results['anthropologists'],
                'concepts' => $results['concepts'],
                'theories' => $results['theories'],
                'allDisciplines' => $service->getDisciplines(),
                'allRegions' => $service->getRegions(),
                'tagGroups' => $tagGroups,
                'isSearching' => true,
            ])->layout('layouts.public', ['title' => 'Encyclopedia | AnthroConnect']);
        }

        return view('livewire.pages.encyclopedia.encyclopedia-index-page', [
            'anthropologists' => $service->getFeaturedAnthropologists(3),
            'concepts' => $service->getCoreConcepts(4),
            'theories' => $service->getMajorTheories(3),
            'allDisciplines' => $service->getDisciplines(),
            'allRegions' => $service->getRegions(),
            'tagGroups' => $tagGroups,
            'isSearching' => false,
        ])->layout('layouts.public', ['title' => 'Encyclopedia | AnthroConnect']);
    }
}
