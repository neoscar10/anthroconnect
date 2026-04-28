<?php

namespace App\Livewire\Admin\Library\Resources;

use App\Models\LibraryResource;
use App\Models\LibraryResourceType;
use App\Models\LibraryRegion;
use App\Models\Topic;
use App\Services\Library\LibraryAdminService;
use App\Services\Library\BookMetadataLookupService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Filters
    public $search = '';
    public $status = '';
    public $type_id = '';
    public $upscFilter = '';

    // Modal State
    public $modalOpen = false;
    public $modalMode = 'create';
    public $editingResourceId = null;
    public $open_modal = false; // For URL trigger

    // Form Fields
    public $title = '';
    public $author_display = '';
    public $publisher = '';
    public $publication_year = '';
    public $language = '';
    public $pages_count = '';
    public $resource_type_id = '';
    public $abstract = '';
    public $selectedTags = [];
    public $access_type = 'public';
    public $resource_status = 'published';
    public $is_featured = false;
    public $is_recommended = false;
    public $is_upsc_relevant = false;

    // ISBN/Cover State
    public $isbn = '';
    public $coverSource = 'upload';
    public $cover_image;
    public $resource_file;
    public $fetchedCoverPreview = null;
    public $fetchedCoverPath = null;
    public $cover_external_url = '';
    public $currentCoverUrl = null;
    public $isFetching = false;
    public $fetchError = null;
    public $fetchSuccess = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'type_id' => ['except' => ''],
        'upscFilter' => ['except' => ''],
        'open_modal' => ['except' => false],
    ];

    public function mount()
    {
        if ($this->open_modal) {
            $this->openCreateModal();
            $this->open_modal = false;
        }
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }
    public function updatingTypeId() { $this->resetPage(); }
    public function updatingUpscFilter() { $this->resetPage(); }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->modalOpen = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->modalMode = 'edit';
        $this->editingResourceId = $id;
        
        $resource = LibraryResource::with('topics', 'tags')->findOrFail($id);
        $this->editingResourceId = $resource->id;
        $this->title = $resource->title;
        $this->author_display = $resource->author_display;
        $this->publisher = $resource->publisher;
        $this->publication_year = $resource->publication_year;
        $this->language = $resource->language;
        $this->pages_count = $resource->pages_count;
        $this->resource_type_id = $resource->resource_type_id;
        $this->abstract = $resource->abstract;
        $this->access_type = $resource->access_type;
        $this->resource_status = $resource->status;
        $this->isbn = $resource->isbn ?: '';
        $this->cover_external_url = $resource->cover_external_url ?: '';
        $this->currentCoverUrl = $resource->cover_image_path ? Storage::url($resource->cover_image_path) : null;
        $this->is_featured = (bool)$resource->is_featured;
        $this->is_recommended = (bool)$resource->is_recommended;
        $this->is_upsc_relevant = (bool)$resource->is_upsc_relevant;
        $this->selectedTags = $resource->tags->pluck('id')->toArray();
        
        $this->dispatch('set-tags', id: 'library-tag-selector', tags: $this->selectedTags);

        $this->modalOpen = true;
    }

    public function resetForm()
    {
        $this->reset([
            'title', 'author_display', 'publisher', 'publication_year', 'language', 'pages_count',
            'resource_type_id', 'abstract', 'access_type', 'resource_status',
            'is_featured', 'is_recommended', 'is_upsc_relevant', 'isbn', 'coverSource',
            'cover_image', 'resource_file', 'fetchedCoverPreview', 'fetchedCoverPath',
            'cover_external_url', 'currentCoverUrl', 'editingResourceId', 'fetchError', 'fetchSuccess', 'selectedTags'
        ]);
        $this->resource_status = 'published';
        $this->access_type = 'public';
    }

    public function fetchBookDetails(BookMetadataLookupService $lookupService)
    {
        if (!$this->isbn) {
            $this->fetchError = 'Please enter an ISBN first.';
            return;
        }

        $this->isFetching = true;
        $this->fetchError = null;
        $this->fetchSuccess = null;

        $result = $lookupService->lookupByIsbn($this->isbn);

        if ($result['success']) {
            $data = $result['data'];
            
            // Auto-fill
            $this->title = $data['title'] ?: $this->title;
            $this->author_display = $data['authors'] ?: $this->author_display;
            $this->publisher = $data['publisher'] ?: $this->publisher;
            $this->publication_year = $data['publication_year'] ?: $this->publication_year;
            $this->language = $data['language'] ?: $this->language;
            $this->pages_count = $data['page_count'] ?: $this->pages_count;
            $this->isbn = $data['normalized_isbn'];

            if (!empty($data['categories'])) {
                // We could try to map these to tags, but for now let's keep it simple
            }

            if ($data['stored_cover_url']) {
                $this->fetchedCoverPreview = $data['stored_cover_url'];
                $this->fetchedCoverPath = $data['stored_cover_path'];
                $this->cover_external_url = $data['cover_source_url'];
                $this->coverSource = 'isbn';
            }

            $this->fetchSuccess = 'Book details imported successfully.';
        } else {
            $this->fetchError = $result['message'];
        }

        $this->isFetching = false;
    }

    public function save(LibraryAdminService $service)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'author_display' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer',
            'resource_type_id' => 'required|exists:library_resource_types,id',
            'abstract' => 'required|string',
            'resource_status' => 'required|in:draft,published,archived',
            'access_type' => 'required|in:public,member_only',
        ];

        $validatedData = $this->validate($rules);
        
        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'author_display' => $this->author_display,
            'publisher' => $this->publisher,
            'isbn' => $this->isbn,
            'publication_year' => $this->publication_year,
            'language' => $this->language,
            'pages_count' => $this->pages_count,
            'resource_type_id' => $this->resource_type_id,
            'abstract' => $this->abstract,
            'status' => $this->resource_status,
            'access_type' => $this->access_type,
            'is_featured' => $this->is_featured,
            'is_recommended' => $this->is_recommended,
            'is_upsc_relevant' => (bool) $this->is_upsc_relevant,
            'tags' => $this->selectedTags,
            'cover_source' => $this->coverSource,
            'cover_external_url' => $this->cover_external_url,
        ];

        if ($this->modalMode === 'create') {
            $data['created_by'] = auth()->id();
            $resource = $service->createResource($data);
            $message = 'Resource created successfully.';
        } else {
            $resource = LibraryResource::findOrFail($this->editingResourceId);
            $service->updateResource($resource, $data);
            $message = 'Resource updated successfully.';
        }

        // Handle Files
        $files = [];
        if ($this->cover_image && $this->coverSource === 'upload') {
            $files['cover_image'] = $this->cover_image;
        } elseif ($this->fetchedCoverPath && $this->coverSource === 'isbn') {
            // Service already downloaded it, we just need to update the path
            $resource->update(['cover_image_path' => $this->fetchedCoverPath]);
        }

        if ($this->resource_file) {
            $files['resource_file'] = $this->resource_file;
        }

        if (!empty($files)) {
            $service->handleFileUploads($resource, $files);
        }

        $this->modalOpen = false;
        session()->flash('success', $message);
    }

    public function archive($id)
    {
        $resource = LibraryResource::findOrFail($id);
        $resource->update(['status' => 'archived']);
        session()->flash('success', 'Resource archived.');
    }

    public $tagFilters = []; // key: group_id, value: tag_id
    
    public function updatedTagFilters()
    {
        $this->resetPage();
    }

    public function render()
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->status,
            'type_id' => $this->type_id,
            'is_upsc_relevant' => $this->upscFilter,
            'tag_ids' => $this->tagFilters,
        ];

        $resources = app(LibraryAdminService::class)->listResourcesForAdmin($filters)->paginate(15);
        $types = LibraryResourceType::active()->get();
        $filterableTagGroups = \App\Models\TagGroup::getGroupsWithUsage(LibraryResource::class);

        return view('livewire.admin.library.resources.index', compact('resources', 'types', 'filterableTagGroups'))
            ->layout('layouts.admin');
    }
}
