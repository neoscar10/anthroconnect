<?php

namespace App\Livewire\Admin\Encyclopedia\Anthropologists;

use App\Models\Encyclopedia\Anthropologist;
use App\Models\Encyclopedia\CoreConcept;
use App\Models\Topic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $statusFilter = '';
    public $featuredFilter = '';
    public $upscFilter = '';

    // Form fields
    public $anthropologistId;
    public $full_name = '';
    public $summary = '';
    public $biography_markdown = '';
    public $birth_year = '';
    public $death_year = '';
    public $discipline_or_specialization = '';
    public $nationality = '';
    public $profile_image;
    public $status = 'active';
    public $is_featured = false;
    public $is_upsc_relevant = false;

    // Relationship fields
    public $tags = [];
    public $selectedCoreConcepts = [];

    public $isModalOpen = false;
    public $modalSessionId = '';

    protected $queryString = ['search', 'statusFilter', 'featuredFilter', 'upscFilter', 'tagFilters'];

    protected function rules()
    {
        return [
            'full_name' => 'required|string|max:255',
            'summary' => 'required|string|max:1000',
            'biography_markdown' => 'required|string',
            'birth_year' => 'nullable|integer',
            'death_year' => 'nullable|integer|gte:birth_year',
            'discipline_or_specialization' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
            'is_upsc_relevant' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'selectedCoreConcepts' => 'array',
        ];
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset([
            'anthropologistId', 'full_name', 'summary', 'biography_markdown', 'birth_year', 'death_year', 
            'discipline_or_specialization', 'nationality', 'profile_image', 'status', 'is_featured', 
            'is_upsc_relevant', 'tags', 'selectedCoreConcepts'
        ]);

        if ($id) {
            $anthropologist = Anthropologist::with(['topics', 'coreConcepts'])->findOrFail($id);
            $this->anthropologistId = $anthropologist->id;
            $this->full_name = $anthropologist->full_name;
            $this->summary = $anthropologist->summary;
            $this->biography_markdown = $anthropologist->biography_markdown;
            $this->birth_year = $anthropologist->birth_year;
            $this->death_year = $anthropologist->death_year;
            $this->discipline_or_specialization = $anthropologist->discipline_or_specialization;
            $this->nationality = $anthropologist->nationality;
            $this->status = $anthropologist->status;
            $this->is_featured = $anthropologist->is_featured;
            $this->is_upsc_relevant = $anthropologist->is_upsc_relevant;
            
            $this->tags = $anthropologist->tags->pluck('id')->toArray();
            $this->selectedCoreConcepts = $anthropologist->coreConcepts->pluck('id')->toArray();
        }

        $this->modalSessionId = uniqid();
        $this->isModalOpen = true;
        $this->dispatch('open-modal');
        $this->dispatch('set-tags', id: 'anthropologist-tag-selector', tags: $this->tags);
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->dispatch('close-modal');
    }



    public function save()
    {
        $this->validate();

        if ($this->anthropologistId) {
            $anthropologist = Anthropologist::findOrFail($this->anthropologistId);
            
            // Check if name changed for slug update
            if ($this->full_name !== $anthropologist->full_name) {
                $slug = $this->generateUniqueSlug($this->full_name, $anthropologist->id);
            } else {
                $slug = $anthropologist->slug;
            }

            // Image handling
            $imagePath = $anthropologist->profile_image;
            if ($this->profile_image instanceof \Illuminate\Http\UploadedFile) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $this->profile_image->store('encyclopedia/anthropologists', 'public');
            }

            $anthropologist->update([
                'full_name' => $this->full_name,
                'slug' => $slug,
                'summary' => $this->summary,
                'biography_markdown' => $this->biography_markdown,
                'birth_year' => $this->birth_year ?: null,
                'death_year' => $this->death_year ?: null,
                'discipline_or_specialization' => $this->discipline_or_specialization,
                'nationality' => $this->nationality,
                'profile_image' => $imagePath,
                'status' => $this->status,
                'is_featured' => $this->is_featured,
                'is_upsc_relevant' => (bool) $this->is_upsc_relevant,
            ]);

            $anthropologist->syncTags($this->tags);
            $anthropologist->coreConcepts()->sync(array_unique($this->selectedCoreConcepts));
            session()->flash('success', 'Anthropologist updated successfully.');
        } else {
            $imagePath = null;
            if ($this->profile_image instanceof \Illuminate\Http\UploadedFile) {
                $imagePath = $this->profile_image->store('encyclopedia/anthropologists', 'public');
            }

            $anthropologist = Anthropologist::create([
                'full_name' => $this->full_name,
                'slug' => $this->generateUniqueSlug($this->full_name),
                'summary' => $this->summary,
                'biography_markdown' => $this->biography_markdown,
                'birth_year' => $this->birth_year ?: null,
                'death_year' => $this->death_year ?: null,
                'discipline_or_specialization' => $this->discipline_or_specialization,
                'nationality' => $this->nationality,
                'profile_image' => $imagePath,
                'status' => $this->status,
                'is_featured' => $this->is_featured,
                'is_upsc_relevant' => (bool) $this->is_upsc_relevant,
            ]);

            $anthropologist->syncTags($this->tags);
            $anthropologist->coreConcepts()->sync(array_unique($this->selectedCoreConcepts));
            session()->flash('success', 'Anthropologist created successfully.');
        }

        $this->closeModal();
    }



    protected function generateUniqueSlug($name, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Anthropologist::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
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

    public function updatingFeaturedFilter()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $anthropologist = Anthropologist::findOrFail($id);
        $anthropologist->delete();
        session()->flash('success', 'Anthropologist moved to trash.');
    }

    public $tagFilters = []; // key: group_id, value: tag_id



    public function updatedTagFilters()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Anthropologist::with(['tags', 'coreConcepts'])->orderBy('full_name');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('summary', 'like', '%' . $this->search . '%')
                  ->orWhere('discipline_or_specialization', 'like', '%' . $this->search . '%')
                  ->orWhere('nationality', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->featuredFilter)) {
            $query->where('is_featured', $this->featuredFilter === '1');
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

        $anthropologists = $query->paginate(15);
        $coreConcepts = CoreConcept::orderBy('title')->get();
        $filterableTagGroups = \App\Models\TagGroup::getGroupsWithUsage(Anthropologist::class);

        return view('livewire.admin.encyclopedia.anthropologists.index', compact('anthropologists', 'coreConcepts', 'filterableTagGroups'))
            ->layout('layouts.admin', ['title' => 'Encyclopedia: Anthropologists']);
    }
}
