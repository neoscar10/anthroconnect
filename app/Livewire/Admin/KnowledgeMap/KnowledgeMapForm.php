<?php

namespace App\Livewire\Admin\KnowledgeMap;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\KnowledgeMap\KnowledgeMap;
use App\Services\KnowledgeMap\KnowledgeMapService;
use Illuminate\Support\Facades\Storage;

class KnowledgeMapForm extends Component
{
    use WithFileUploads;

    public $mapId;
    public $title;
    public $subtitle;
    public $description;
    public $status = 'draft';
    public $visibility = 'public';
    public $is_featured = true;
    public $default_zoom = 1.0;
    public $canvas_background = 'dotted';
    public $canvas_width = 4000;
    public $canvas_height = 3000;

    public function mount()
    {
        $map = KnowledgeMap::firstOrCreate([
            'slug' => 'main-map'
        ], [
            'title' => 'Anthropology Knowledge Map',
            'status' => 'published',
            'visibility' => 'public',
            'is_featured' => true,
            'default_zoom' => 1.0,
            'canvas_settings' => ['background' => 'dotted']
        ]);

        $this->mapId = $map->id;
        $this->title = $map->title;
        $this->subtitle = $map->subtitle;
        $this->description = $map->description;
        $this->status = $map->status;
        $this->visibility = $map->visibility;
        $this->is_featured = true;
        $this->default_zoom = $map->default_zoom;
        
        $settings = $map->canvas_settings ?? [];
        $this->canvas_background = $settings['background'] ?? 'dotted';
        $this->canvas_width = $settings['width'] ?? 4000;
        $this->canvas_height = $settings['height'] ?? 3000;
    }

    public function save(KnowledgeMapService $service)
    {
        $rules = [
            'title' => 'required|max:255',
            'subtitle' => 'nullable|max:255',
            'description' => 'nullable',
            'status' => 'required|in:draft,published,archived',
            'visibility' => 'required|in:public,members_only',
            'default_zoom' => 'numeric|min:0.1|max:5',
            'canvas_width' => 'required|integer|min:1000|max:10000',
            'canvas_height' => 'required|integer|min:1000|max:10000',
        ];

        $validated = $this->validate($rules);

        $validated['is_featured'] = true;
        
        $currentSettings = KnowledgeMap::find($this->mapId)?->canvas_settings ?? [];
        $validated['canvas_settings'] = array_merge($currentSettings, [
            'background' => $this->canvas_background,
            'width' => (int) $this->canvas_width,
            'height' => (int) $this->canvas_height,
        ]);

        if ($this->mapId) {
            $map = KnowledgeMap::find($this->mapId);
            $service->updateMap($map, $validated);
            session()->flash('success', 'Knowledge Map updated successfully.');
        } else {
            $map = $service->createMap($validated);
            session()->flash('success', 'Knowledge Map created successfully.');
        }

        return redirect()->route('admin.knowledge-maps.builder');
    }

    public function render()
    {
        return view('livewire.admin.knowledge-map.form')
            ->layout('layouts.admin', ['title' => 'Map Settings']);
    }
}
