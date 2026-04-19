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
    public $discipline = 'All Disciplines';
    public $region = 'Global';

    public $isFiltered = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => 'All Categories'],
        'discipline' => ['except' => 'All Disciplines'],
        'region' => ['except' => 'Global'],
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'category', 'discipline', 'region'])) {
            $this->isFiltered = ($this->search !== '' || 
                               $this->category !== 'All Categories' || 
                               $this->discipline !== 'All Disciplines' || 
                               $this->region !== 'Global');
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'category', 'discipline', 'region', 'isFiltered']);
    }

    public function render(EncyclopediaFrontendService $service)
    {
        if ($this->isFiltered) {
            $results = $service->search([
                'search' => $this->search,
                'category' => $this->category,
                'discipline' => $this->discipline,
                'region' => $this->region,
            ]);

            return view('livewire.pages.encyclopedia.encyclopedia-index-page', [
                'anthropologists' => $results['anthropologists'],
                'concepts' => $results['concepts'],
                'theories' => $results['theories'],
                'allDisciplines' => $service->getDisciplines(),
                'allRegions' => $service->getRegions(),
                'isSearching' => true,
            ])->layout('layouts.public', ['title' => 'Encyclopedia | AnthroConnect']);
        }

        return view('livewire.pages.encyclopedia.encyclopedia-index-page', [
            'anthropologists' => $service->getFeaturedAnthropologists(3),
            'concepts' => $service->getCoreConcepts(4),
            'theories' => $service->getMajorTheories(3),
            'allDisciplines' => $service->getDisciplines(),
            'allRegions' => $service->getRegions(),
            'isSearching' => false,
        ])->layout('layouts.public', ['title' => 'Encyclopedia | AnthroConnect']);
    }
}
