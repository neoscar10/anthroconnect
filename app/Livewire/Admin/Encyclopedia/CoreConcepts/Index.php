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

    // Form fields
    public $conceptId;
    public $title = '';
    public $short_description = '';
    public $body_markdown = '';
    public $status = 'active';

    public $isModalOpen = false;
    public $modalSessionId = '';

    protected $updatesQueryString = ['search', 'statusFilter'];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:1000',
            'body_markdown' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset('title', 'short_description', 'body_markdown', 'status', 'conceptId');

        if ($id) {
            $concept = CoreConcept::findOrFail($id);
            $this->conceptId = $concept->id;
            $this->title = $concept->title;
            $this->short_description = $concept->short_description;
            $this->body_markdown = $concept->body_markdown;
            $this->status = $concept->status;
        }

        $this->modalSessionId = uniqid();
        $this->isModalOpen = true;
        $this->dispatch('open-modal');
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
        ];

        if ($this->conceptId) {
            $concept = CoreConcept::findOrFail($this->conceptId);
            
            if ($this->title !== $concept->title) {
                $data['slug'] = $this->generateUniqueSlug($this->title, $concept->id);
            }

            $concept->update($data);
            session()->flash('success', 'Core Concept updated successfully.');
        } else {
            $data['slug'] = $this->generateUniqueSlug($this->title);
            CoreConcept::create($data);
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

    public function render()
    {
        $query = CoreConcept::query()->orderBy('title');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('short_description', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        $concepts = $query->paginate(15);

        return view('livewire.admin.encyclopedia.core-concepts.index', compact('concepts'))
            ->layout('layouts.admin', ['title' => 'Encyclopedia: Core Concepts']);
    }
}
