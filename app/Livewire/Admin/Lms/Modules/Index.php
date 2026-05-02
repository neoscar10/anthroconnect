<?php

namespace App\Livewire\Admin\Lms\Modules;

use App\Models\Lms\LmsModule;
use App\Models\Topic;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $statusFilter = '';
    public $levelFilter = '';
    public $upscFilter = 'all';
    public $tags = [];
    public $tagFilters = []; // key: group_id, value: tag_id

    // Modal State
    public $isModalOpen = false;
    public $moduleId = null;
    public $title = '';
    public $slug = '';
    public $short_description = '';
    public $overview = '';
    public $level = 'beginner';
    public $is_published = false;
    public $is_upsc_relevant = false;
    public $cover_image;
    public $existingCoverImage;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'levelFilter' => ['except' => ''],
        'upscFilter' => ['except' => 'all'],
        'tagFilters' => ['except' => []],
    ];

    public function updatedTitle()
    {
        if (!$this->moduleId) {
            $this->slug = \Illuminate\Support\Str::slug($this->title);
        }
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['moduleId', 'title', 'slug', 'short_description', 'level', 'tags', 'is_published', 'is_upsc_relevant', 'cover_image', 'existingCoverImage']);
        $this->isModalOpen = true;
        $this->dispatch('open-modal');
        $this->dispatch('set-tags', id: 'lms-module-tag-selector', tags: []);
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $module = LmsModule::findOrFail($id);
        $this->moduleId = $module->id;
        $this->title = $module->title;
        $this->slug = $module->slug;
        $this->short_description = $module->short_description;
        $this->overview = $module->overview;
        $this->level = $module->level ?? 'beginner';
        $this->tags = $module->tags->pluck('id')->toArray();
        $this->is_published = $module->is_published;
        $this->is_upsc_relevant = $module->is_upsc_relevant;
        $this->existingCoverImage = $module->cover_image;
        $this->cover_image = null;
        $this->isModalOpen = true;
        $this->dispatch('open-modal');
        $this->dispatch('set-tags', id: 'lms-module-tag-selector', tags: $this->tags);
    }

    public function saveModule()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:lms_modules,slug,' . ($this->moduleId ?? 'NULL'),
            'short_description' => 'required|string|max:1000',
            'overview' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'is_upsc_relevant' => 'boolean',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'overview' => $this->overview,
            'level' => $this->level,
            'is_published' => $this->is_published,
            'is_upsc_relevant' => (bool) $this->is_upsc_relevant,
        ];

        if ($this->cover_image) {
            $data['cover_image'] = $this->cover_image->store('lms/covers', 'public');
        }

        if ($this->moduleId) {
            $module = LmsModule::findOrFail($this->moduleId);
            $module->update(array_merge($data, ['updated_by' => auth()->id()]));
            $module->syncTags($this->tags);
            session()->flash('success', 'Module details updated.');
        } else {
            $module = LmsModule::create(array_merge($data, [
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]));
            $module->syncTags($this->tags);
            session()->flash('success', 'Module created successfully.');
            return redirect()->route('admin.lms.modules.edit', $module);
        }

        $this->isModalOpen = false;
        $this->dispatch('close-modal');
    }

    public function updatedTagFilters()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteModule($id)
    {
        $module = LmsModule::findOrFail($id);
        $module->delete();
        session()->flash('success', 'Module archived successfully.');
    }

    public function togglePublish($id)
    {
        $module = LmsModule::findOrFail($id);
        $module->is_published = !$module->is_published;
        $module->save();
    }

    public function render()
    {
        $query = LmsModule::with(['tags', 'lessons', 'resources'])
            ->withCount(['lessons', 'resources']);

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->statusFilter !== '') {
            $query->where('is_published', $this->statusFilter === 'published');
        }

        if ($this->levelFilter) {
            $query->where('level', $this->levelFilter);
        }

        if ($this->upscFilter === 'upsc') {
            $query->where('is_upsc_relevant', true);
        } elseif ($this->upscFilter === 'general') {
            $query->where('is_upsc_relevant', false);
        }

        foreach ($this->tagFilters as $groupId => $tagId) {
            if ($tagId) {
                $query->withTag($tagId);
            }
        }

        $modules = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $filterableTagGroups = \App\Models\TagGroup::getGroupsWithUsage(LmsModule::class);

        return view('livewire.admin.lms.modules.index', [
            'modules' => $modules,
            'filterableTagGroups' => $filterableTagGroups,
        ])->layout('layouts.admin', ['title' => 'LMS Modules']);
    }
}
