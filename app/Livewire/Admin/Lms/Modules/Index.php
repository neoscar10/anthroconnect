<?php

namespace App\Livewire\Admin\Lms\Modules;

use App\Models\Lms\LmsModule;
use App\Models\Topic;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $levelFilter = '';
    public $topicFilter = '';

    // Modal State
    public $isModalOpen = false;
    public $moduleId = null;
    public $title = '';
    public $slug = '';
    public $short_description = '';
    public $overview = '';
    public $level = 'beginner';
    public $topic_id;
    public $is_published = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'levelFilter' => ['except' => ''],
        'topicFilter' => ['except' => ''],
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
        $this->reset(['moduleId', 'title', 'slug', 'short_description', 'level', 'topic_id', 'is_published']);
        $this->isModalOpen = true;
        $this->dispatch('open-modal');
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
        $this->topic_id = $module->topic_id;
        $this->is_published = $module->is_published;
        $this->isModalOpen = true;
        $this->dispatch('open-modal');
    }

    public function saveModule()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:lms_modules,slug,' . ($this->moduleId ?? 'NULL'),
            'short_description' => 'required|string|max:1000',
            'overview' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'topic_id' => 'nullable|exists:topics,id',
        ]);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'overview' => $this->overview,
            'level' => $this->level,
            'topic_id' => $this->topic_id,
            'is_published' => $this->is_published,
        ];

        if ($this->moduleId) {
            $module = LmsModule::findOrFail($this->moduleId);
            $module->update(array_merge($data, ['updated_by' => auth()->id()]));
            session()->flash('success', 'Module details updated.');
        } else {
            $module = LmsModule::create(array_merge($data, [
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]));
            session()->flash('success', 'Module created successfully.');
            return redirect()->route('admin.lms.modules.edit', $module);
        }

        $this->isModalOpen = false;
        $this->dispatch('close-modal');
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
        $query = LmsModule::with(['topic', 'lessons', 'resources'])
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

        if ($this->topicFilter) {
            $query->where('topic_id', $this->topicFilter);
        }

        $modules = $query->orderBy('created_at', 'desc')->paginate(10);
        $topics = Topic::active()->orderBy('name')->get();

        return view('livewire.admin.lms.modules.index', [
            'modules' => $modules,
            'topics' => $topics,
        ])->layout('layouts.admin', ['title' => 'LMS Modules']);
    }
}
