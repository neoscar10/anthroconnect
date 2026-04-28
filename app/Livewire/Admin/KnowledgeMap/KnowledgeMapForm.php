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
    public $cover_image;
    public $cover_image_url;
    public $status = 'draft';
    public $visibility = 'public';
    public $is_featured = false;
    public $default_zoom = 1.0;
    public $canvas_background = 'dotted';

    public function mount()
    {
        $map = KnowledgeMap::firstOrCreate([
            'slug' => 'main-map'
        ], [
            'title' => 'Anthropology Knowledge Map',
            'status' => 'published',
            'visibility' => 'public',
            'default_zoom' => 1.0,
            'canvas_settings' => ['background' => 'dotted']
        ]);

        $this->mapId = $map->id;
        $this->title = $map->title;
        $this->subtitle = $map->subtitle;
        $this->description = $map->description;
        $this->status = $map->status;
        $this->visibility = $map->visibility;
        $this->is_featured = $map->is_featured;
        $this->default_zoom = $map->default_zoom;
        $this->cover_image_url = $map->cover_image ? Storage::url($map->cover_image) : null;
        
        $settings = $map->canvas_settings ?? [];
        $this->canvas_background = $settings['background'] ?? 'dotted';
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
            'cover_image' => 'nullable|image|max:2048',
        ];

        $validated = $this->validate($rules);

        if ($this->cover_image) {
            $validated['cover_image'] = $this->cover_image->store('knowledge-maps', 'public');
        }

        $validated['is_featured'] = $this->is_featured;
        $validated['canvas_settings'] = [
            'background' => $this->canvas_background
        ];

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
