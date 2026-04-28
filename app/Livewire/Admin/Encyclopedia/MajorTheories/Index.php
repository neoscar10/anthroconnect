<?php

namespace App\Livewire\Admin\Encyclopedia\MajorTheories;

use App\Models\Encyclopedia\MajorTheory;
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
    public $theoryId;
    public $title = '';
    public $short_description = '';
    public $body_markdown = '';
    public $key_thinkers_text = '';
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
            'key_thinkers_text' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
            'is_upsc_relevant' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ];
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset('title', 'short_description', 'body_markdown', 'key_thinkers_text', 'status', 'is_upsc_relevant', 'theoryId', 'tags');

        if ($id) {
            $theory = MajorTheory::findOrFail($id);
            $this->theoryId = $theory->id;
            $this->title = $theory->title;
            $this->short_description = $theory->short_description;
            $this->body_markdown = $theory->body_markdown;
            $this->key_thinkers_text = $theory->key_thinkers_text;
            $this->status = $theory->status;
            $this->is_upsc_relevant = $theory->is_upsc_relevant;
            $this->tags = $theory->tags->pluck('id')->toArray();
        }

        $this->modalSessionId = uniqid();
        $this->isModalOpen = true;
        $this->dispatch('open-modal');
        $this->dispatch('set-tags', id: 'theory-tag-selector', tags: $this->tags);
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
            'key_thinkers_text' => $this->key_thinkers_text,
            'status' => $this->status,
            'is_upsc_relevant' => (bool) $this->is_upsc_relevant,
        ];

        if ($this->theoryId) {
            $theory = MajorTheory::findOrFail($this->theoryId);
            
            if ($this->title !== $theory->title) {
                $data['slug'] = $this->generateUniqueSlug($this->title, $theory->id);
            }

            $theory->update($data);
            $theory->syncTags($this->tags);
            session()->flash('success', 'Major Theory updated successfully.');
        } else {
            $data['slug'] = $this->generateUniqueSlug($this->title);
            $theory = MajorTheory::create($data);
            $theory->syncTags($this->tags);
            session()->flash('success', 'Major Theory created successfully.');
        }

        $this->closeModal();
    }

    protected function generateUniqueSlug($title, $ignoreId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (MajorTheory::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
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
        $theory = MajorTheory::findOrFail($id);
        $theory->delete();
        session()->flash('success', 'Major Theory moved to trash.');
    }

    public $tagFilters = []; // key: group_id, value: tag_id



    public function updatedTagFilters()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = MajorTheory::with('tags')->orderBy('title');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('short_description', 'like', '%' . $this->search . '%')
                  ->orWhere('key_thinkers_text', 'like', '%' . $this->search . '%');
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

        $theories = $query->paginate(15);
        $filterableTagGroups = \App\Models\TagGroup::getGroupsWithUsage(MajorTheory::class);

        return view('livewire.admin.encyclopedia.major-theories.index', compact('theories', 'filterableTagGroups'))
            ->layout('layouts.admin', ['title' => 'Encyclopedia: Major Theories']);
    }
}
