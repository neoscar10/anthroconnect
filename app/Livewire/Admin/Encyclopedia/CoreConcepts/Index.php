<?php

namespace App\Livewire\Admin\Encyclopedia\CoreConcepts;

use App\Models\Encyclopedia\CoreConcept;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $upscFilter = '';

    // Form fields
    public $conceptId;
    public $title = '';
    public $short_description = '';
    public $body_markdown = '';
    public $status = 'active';
    public $is_upsc_relevant = false;
    public $tags = [];

    public $isModalOpen = false;
    public $modalSessionId = '';

    protected $queryString = ['search', 'statusFilter', 'upscFilter', 'tagFilters'];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:1000',
            'body_markdown' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'is_upsc_relevant' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ];
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset('title', 'short_description', 'body_markdown', 'status', 'is_upsc_relevant', 'conceptId', 'tags');

        if ($id) {
            $concept = CoreConcept::findOrFail($id);
            $this->conceptId = $concept->id;
            $this->title = $concept->title;
            $this->short_description = $concept->short_description;
            $this->body_markdown = $concept->body_markdown;
            $this->status = $concept->status;
            $this->is_upsc_relevant = $concept->is_upsc_relevant;
            $this->tags = $concept->tags->pluck('id')->toArray();
        }

        $this->modalSessionId = uniqid();
        $this->isModalOpen = true;
        $this->dispatch('open-modal');
        $this->dispatch('set-tags', id: 'concept-tag-selector', tags: $this->tags);
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->dispatch('close-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'short_description' => $this->short_description,
            'body_markdown' => $this->body_markdown,
            'status' => $this->status,
            'is_upsc_relevant' => (bool) $this->is_upsc_relevant,
        ];

        if ($this->conceptId) {
            $concept = CoreConcept::findOrFail($this->conceptId);
            
            if ($this->title !== $concept->title) {
                $data['slug'] = $this->generateUniqueSlug($this->title, $concept->id);
            }

            $concept->update($data);
            $concept->syncTags($this->tags);
            session()->flash('success', 'Core Concept updated successfully.');
        } else {
            $data['slug'] = $this->generateUniqueSlug($this->title);
            $concept = CoreConcept::create($data);
            $concept->syncTags($this->tags);
            session()->flash('success', 'Core Concept created successfully.');
        }

        $this->closeModal();
    }

    protected function generateUniqueSlug($title, $ignoreId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (CoreConcept::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $concept = CoreConcept::findOrFail($id);
        $concept->delete();
        session()->flash('success', 'Core Concept moved to trash.');
    }

    public $tagFilters = []; // key: group_id, value: tag_id



    public function updatedTagFilters()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = CoreConcept::with('tags')->orderBy('title');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('short_description', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
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

        $concepts = $query->paginate(15);
        $filterableTagGroups = \App\Models\TagGroup::getGroupsWithUsage(CoreConcept::class);

        return view('livewire.admin.encyclopedia.core-concepts.index', compact('concepts', 'filterableTagGroups'))
            ->layout('layouts.admin', ['title' => 'Encyclopedia: Core Concepts']);
    }
}
