<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\ExploreArticle;
use App\Models\Lms\LmsModule;
use App\Models\Encyclopedia\Anthropologist;
use App\Models\Encyclopedia\CoreConcept;
use App\Models\Encyclopedia\MajorTheory;
use App\Models\LibraryResource;

class UpscHubPage extends Component
{
    public string $search = '';
    public string $section = 'all';

    /**
     * Apply search filters to the query.
     */
    private function applySearch($query)
    {
        if (trim($this->search) === '') {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('title', 'like', "%{$this->search}%")
              ->orWhere('name', 'like', "%{$this->search}%")
              ->orWhere('excerpt', 'like', "%{$this->search}%")
              ->orWhere('summary', 'like', "%{$this->search}%")
              ->orWhere('description', 'like', "%{$this->search}%");
        });
    }

    public function render()
    {
        // Fetch UPSC relevant modules
        $modules = $this->applySearch(
            LmsModule::where('is_upsc_relevant', true)
                ->where('is_published', true)
                ->latest()
        )->take(6)->get();

        // Fetch UPSC relevant explore items (articles)
        $exploreItems = $this->applySearch(
            ExploreArticle::where('is_upsc_relevant', true)
                ->published()
                ->latest()
        )->take(6)->get();

        // Fetch UPSC relevant anthropologists
        $anthropologists = $this->applySearch(
            Anthropologist::where('is_upsc_relevant', true)
                ->latest()
        )->take(4)->get();

        // Fetch UPSC relevant core concepts
        $concepts = $this->applySearch(
            CoreConcept::where('is_upsc_relevant', true)
                ->latest()
        )->take(6)->get();

        // Fetch UPSC relevant major theories
        $theories = $this->applySearch(
            MajorTheory::where('is_upsc_relevant', true)
                ->latest()
        )->take(6)->get();

        // Fetch UPSC relevant library resources
        $resources = $this->applySearch(
            LibraryResource::where('is_upsc_relevant', true)
                ->latest()
        )->take(5)->get();

        return view('livewire.pages.upsc-hub-page', compact(
            'modules',
            'exploreItems',
            'anthropologists',
            'concepts',
            'theories',
            'resources'
        ))->layout('layouts.public');
    }
}
