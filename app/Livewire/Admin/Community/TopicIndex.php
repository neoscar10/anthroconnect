<?php

namespace App\Livewire\Admin\Community;

use App\Models\Community\CommunityTopic;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class TopicIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public ?CommunityTopic $editingTopic = null;

    // Form fields
    public $name = '';
    public $slug = '';
    public $description = '';
    public $icon = '';
    public $color = '#9a3412';
    public $short_label = '';
    public $sort_order = 0;
    public $is_active = true;

    protected $queryString = ['search' => ['except' => '']];

    public function updatedName($value)
    {
        if (!$this->editingTopic) {
            $this->slug = Str::slug($value);
        }
    }

    public function openModal($id = null)
    {
        $this->resetErrorBag();
        if ($id) {
            $this->editingTopic = CommunityTopic::find($id);
            $this->name = $this->editingTopic->name;
            $this->slug = $this->editingTopic->slug;
            $this->description = $this->editingTopic->description;
            $this->icon = $this->editingTopic->icon;
            $this->color = $this->editingTopic->color;
            $this->short_label = $this->editingTopic->short_label;
            $this->sort_order = $this->editingTopic->sort_order;
            $this->is_active = $this->editingTopic->is_active;
        } else {
            $this->editingTopic = null;
            $this->reset('name', 'slug', 'description', 'icon', 'color', 'short_label', 'sort_order', 'is_active');
            $this->color = '#9a3412';
            $this->is_active = true;
        }
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:community_topics,slug,' . ($this->editingTopic->id ?? 'NULL'),
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'short_label' => 'nullable|string|max:50',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'short_label' => $this->short_label,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->editingTopic) {
            $this->editingTopic->update($data);
            session()->flash('success', 'Topic updated successfully.');
        } else {
            CommunityTopic::create($data);
            session()->flash('success', 'Topic created successfully.');
        }

        $this->showModal = false;
    }

    public function toggleActive($id)
    {
        $topic = CommunityTopic::findOrFail($id);
        $topic->update(['is_active' => !$topic->is_active]);
    }

    public function render()
    {
        $topics = CommunityTopic::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->paginate(10);

        return view('livewire.admin.community.topic-index', [
            'topics' => $topics
        ])->layout('layouts.admin', ['title' => 'Community Topic Management']);
    }
}
