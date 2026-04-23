@extends('layouts.admin')

@section('content')
<div x-data="{ 
    modalOpen: false,
    modalMode: 'create',
    editingResourceId: null,
    
    // Form Fields
    title: '',
    author_display: '',
    publisher: '',
    publication_year: '',
    language: '',
    pages_count: '',
    resource_type_id: '',
    region_id: '',
    abstract: '',
    tags: '',
    access_type: 'public',
    status: 'published',
    selectedTopics: [],
    
    // Flags
    is_featured: false,
    is_recommended: false,
    is_editors_pick: false,
    allow_download: false,

    // ISBN/Cover State
    coverSource: 'upload',
    isbn: '',
    cover_external_url: '',
    isFetching: false,
    fetchedCoverPreview: null,
    fetchedCoverPath: null,
    currentCoverUrl: null,
    fetchError: null,
    fetchSuccess: null,

    openCreateModal() {
        this.modalMode = 'create';
        this.editingResourceId = null;
        this.resetForm();
        this.modalOpen = true;
    },

    openEditModal(resource) {
        this.modalMode = 'edit';
        this.editingResourceId = resource.id;
        
        this.title = resource.title;
        this.author_display = resource.author_display;
        this.publisher = resource.publisher;
        this.publication_year = resource.publication_year;
        this.language = resource.language;
        this.pages_count = resource.pages_count;
        this.resource_type_id = resource.resource_type_id;
        this.region_id = resource.region_id;
        this.abstract = resource.abstract;
        this.access_type = resource.access_type;
        this.status = resource.status;
        this.isbn = resource.isbn || '';
        this.cover_external_url = resource.cover_external_url || '';
        this.currentCoverUrl = resource.cover_image_path ? `/storage/${resource.cover_image_path}` : null;
        
        // Flags
        this.is_featured = !!resource.is_featured;
        this.is_recommended = !!resource.is_recommended;
        this.is_editors_pick = !!resource.is_editors_pick;
        this.allow_download = !!resource.allow_download;

        // Topics
        this.selectedTopics = resource.topics.map(t => t.id);
        
        // Tags
        this.tags = resource.tags.map(t => t.name).join(', ');

        this.fetchedCoverPreview = null;
        this.fetchedCoverPath = null;
        this.coverSource = 'upload';
        
        this.modalOpen = true;
    },

    resetForm() {
        this.title = '';
        this.author_display = '';
        this.publisher = '';
        this.publication_year = '';
        this.language = '';
        this.pages_count = '';
        this.resource_type_id = '';
        this.region_id = '';
        this.abstract = '';
        this.tags = '';
        this.access_type = 'public';
        this.status = 'published';
        this.selectedTopics = [];
        this.isbn = '';
        this.cover_external_url = '';
        this.fetchedCoverPreview = null;
        this.fetchedCoverPath = null;
        this.currentCoverUrl = null;
        this.coverSource = 'upload';
        
        this.is_featured = false;
        this.is_recommended = false;
        this.is_editors_pick = false;
        this.allow_download = false;
        this.fetchError = null;
        this.fetchSuccess = null;
    },

    async fetchBookDetails() {
        if (!this.isbn) {
            this.fetchError = 'Please enter an ISBN first.';
            return;
        }
        
        this.isFetching = true;
        this.fetchError = null;
        this.fetchSuccess = null;
        
        try {
            const response = await fetch('{{ route('admin.library.resources.lookup-isbn') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    isbn: this.isbn
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                const data = result.data;
                
                // Confirm before overwrite if fields are filled
                const hasExistingData = this.title || this.author_display || this.publisher;
                if (hasExistingData && !confirm('Book details found. Overwrite existing form fields?')) {
                    this.isFetching = false;
                    return;
                }

                this.title = data.title || this.title;
                this.author_display = data.authors || this.author_display;
                this.publisher = data.publisher || this.publisher;
                this.publication_year = data.publication_year || this.publication_year;
                this.language = data.language || this.language;
                this.pages_count = data.page_count || this.pages_count;
                this.isbn = data.normalized_isbn;
                
                if (data.categories && data.categories.length > 0) {
                    this.tags = data.categories.join(', ');
                }

                if (data.stored_cover_url) {
                    this.fetchedCoverPreview = data.stored_cover_url;
                    this.fetchedCoverPath = data.stored_cover_path;
                    this.cover_external_url = data.cover_source_url;
                    this.coverSource = 'isbn';
                }

                this.fetchSuccess = 'Book details imported successfully.';
            } else {
                this.fetchError = result.message;
            }
        } catch (e) {
            this.fetchError = 'System error occurred while fetching.';
        } finally {
            this.isFetching = false;
        }
    },

    init() {
        // Auto-open modal if requested via URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('open_modal')) {
            this.openCreateModal();
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        // Restore focus to search if it was active
        if (urlParams.has('search')) {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
                // Move cursor to end
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        }
    }
}" class="space-y-12">
    <!-- Header -->
    <div class="flex justify-between items-end">
        <div>
            <h1 class="font-headline text-4xl text-on-surface mb-2 italic">Research Archive</h1>
            <p class="font-body text-on-surface-variant text-lg">Curate scholarly resources, papers, and monographs.</p>
        </div>
        <button @click="openCreateModal()" class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-[10px] flex items-center gap-2 shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">add</span>
            Add New Resource
        </button>
    </div>

    <!-- Info Banner -->
    @if(session('success'))
        <div class="px-6 py-4 bg-primary/10 text-primary rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center justify-between border border-primary/20 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-surface-container-lowest p-6 rounded-[32px] border border-outline-variant/10 shadow-sm">
        <form action="{{ route('admin.library.resources.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px] relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" 
                       @input.debounce.750ms="$el.form.submit()"
                       placeholder="Search by title or author..." 
                       class="w-full bg-surface-container-low border-none rounded-xl pl-12 pr-4 py-3 text-sm focus:ring-2 focus:ring-primary">
            </div>
            
            <select name="type_id" @change="$el.form.submit()" class="bg-surface-container-low border-none rounded-xl px-6 py-3 text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary cursor-pointer">
                <option value="">All Types</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ ($filters['type_id'] ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>

            <select name="status" @change="$el.form.submit()" class="bg-surface-container-low border-none rounded-xl px-6 py-3 text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary cursor-pointer">
                <option value="">All Status</option>
                <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ ($filters['status'] ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="archived" {{ ($filters['status'] ?? '') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>

            <button type="submit" class="p-3 bg-primary text-white rounded-xl hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-sm">filter_list</span>
            </button>

            @if(count(array_filter($filters)) > 0)
                <a href="{{ route('admin.library.resources.index') }}" class="text-[10px] font-bold uppercase tracking-widest text-stone-400 hover:text-error transition-colors px-2">
                    Clear Filters
                </a>
            @endif
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-[32px] border border-outline-variant/10 overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low/50">
                <tr>
                    <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Resource</th>
                    <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Type</th>
                    <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Year</th>
                    <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Flags</th>
                    <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Status</th>
                    <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                @forelse($resources as $res)
                    <tr class="hover:bg-surface-container-low/30 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-16 bg-stone-100 rounded-lg flex-shrink-0 overflow-hidden border border-stone-200">
                                    @if($res->cover_image_path)
                                        <img src="{{ Storage::url($res->cover_image_path) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-stone-300">
                                            <span class="material-symbols-outlined text-sm">image</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-headline text-lg font-bold text-on-surface italic leading-tight line-clamp-1">{{ $res->title }}</p>
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-widest mt-1">{{ $res->author_display }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-[10px] font-bold text-secondary uppercase tracking-widest">{{ $res->resourceType->name }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="font-headline text-sm italic font-bold text-on-surface-variant">{{ $res->publication_year ?: 'N/A' }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex gap-2">
                                @if($res->is_featured)
                                    <span class="material-symbols-outlined text-xs text-orange-500" title="Featured">star</span>
                                @endif
                                @if($res->is_recommended)
                                    <span class="material-symbols-outlined text-xs text-primary" title="Recommended">recommend</span>
                                @endif
                                @if($res->access_type === 'member_only')
                                    <span class="material-symbols-outlined text-xs text-secondary" title="Members Only">lock</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest {{ $res->status === 'published' ? 'bg-primary/10 text-primary border border-primary/20' : 'bg-stone-100 text-stone-500 border border-stone-200' }}">
                                {{ $res->status }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <button @click="openEditModal({{ $res->toJson() }})" class="p-2 text-stone-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                                <form action="{{ route('admin.library.resources.destroy', $res) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-stone-400 hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-sm">archive</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-32 text-center">
                            <div class="flex flex-col items-center opacity-40">
                                <span class="material-symbols-outlined text-6xl mb-4">inventory_2</span>
                                <p class="font-headline text-2xl italic">No research found...</p>
                                <p class="text-xs uppercase tracking-widest mt-2">Try adjusting your filters or add a new publication.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($resources->total() > 0)
        <div class="mt-8 bg-surface-container-low/20 rounded-[32px] border border-outline-variant/10 p-6 flex items-center justify-between">
            <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/60 italic px-4">
                Showing {{ $resources->firstItem() }} to {{ $resources->lastItem() }} of {{ $resources->total() }} entries
            </div>
            <div class="flex items-center gap-4">
                @if($resources->onFirstPage())
                    <div class="w-10 h-10 flex items-center justify-center text-stone-300 cursor-not-allowed border border-outline-variant/5 rounded-xl">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </div>
                @else
                    <a href="{{ $resources->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center text-primary hover:bg-primary/5 border border-primary/10 rounded-xl transition-all hover:scale-105 active:scale-95 shadow-sm">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </a>
                @endif

                @if($resources->hasMorePages())
                    <a href="{{ $resources->nextPageUrl() }}" class="h-10 px-6 flex items-center justify-center gap-3 text-primary hover:bg-primary/5 border border-primary/10 rounded-xl transition-all hover:scale-105 active:scale-95 shadow-sm group">
                        <span class="text-[10px] font-bold uppercase tracking-widest">Next Page</span>
                        <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">chevron_right</span>
                    </a>
                @else
                    <div class="h-10 px-6 flex items-center justify-center gap-3 text-stone-300 border border-outline-variant/5 rounded-xl cursor-not-allowed">
                        <span class="text-[10px] font-bold uppercase tracking-widest">Next Page</span>
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Unified Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-12" x-cloak>
        <div class="fixed inset-0 bg-stone-900/60 backdrop-blur-md" @click="modalOpen = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>
        <div class="bg-white rounded-[48px] shadow-2xl w-full max-w-6xl relative z-10 overflow-hidden flex flex-col max-h-[90vh]" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <!-- Modal Header -->
            <div class="px-8 py-5 border-b border-outline-variant/10 bg-surface-container-low/30 flex justify-between items-center shrink-0">
                <div>
                    <h2 class="font-headline text-2xl italic font-bold text-on-surface" x-text="modalMode === 'create' ? 'Catalogue New Research' : 'Edit Publication'"></h2>
                    <p class="text-[9px] uppercase tracking-widest text-on-surface-variant font-bold" x-text="modalMode === 'create' ? 'Archivist Accession Portal' : 'Archivist Update Portal'"></p>
                </div>
                <button @click="modalOpen = false" class="w-10 h-10 rounded-xl hover:bg-stone-100 transition-colors flex items-center justify-center text-stone-400 hover:text-on-surface">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-8 md:p-10 space-y-10 bg-stone-50/50">
                <form :action="modalMode === 'create' ? '{{ route('admin.library.resources.store') }}' : `/admin/library/resources/${editingResourceId}`" 
                      method="POST" enctype="multipart/form-data" id="resourceForm" class="space-y-10">
                    @csrf
                    <template x-if="modalMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <input type="hidden" name="fetched_cover_path" x-model="fetchedCoverPath">
                    <input type="hidden" name="cover_source" x-model="coverSource">
                    <input type="hidden" name="cover_external_url" x-model="cover_external_url">

                    <!-- ISBN Lookup Card -->
                    <div class="bg-primary/5 border border-primary/10 rounded-[32px] p-8 space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div>
                                <h3 class="font-headline text-xl italic font-bold text-primary flex items-center gap-3">
                                    <span class="material-symbols-outlined">auto_stories</span>
                                    ISBN Auto-Cataloguing
                                </h3>
                                <p class="text-[10px] text-on-surface-variant font-medium mt-1">Enter an ISBN to retrieve book metadata and cover image automatically.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="relative w-full md:w-64">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">barcode</span>
                                    <input type="text" x-model="isbn" @keydown.enter.prevent="fetchBookDetails()" placeholder="Enter ISBN-10 or ISBN-13" class="w-full bg-white border border-outline-variant/20 rounded-xl pl-10 pr-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-primary outline-none transition-all">
                                </div>
                                <button type="button" @click="fetchBookDetails()" :disabled="isFetching" class="bg-primary text-on-primary px-6 py-2.5 rounded-xl font-bold uppercase tracking-widest text-[10px] shadow-lg shadow-primary/20 hover:opacity-90 disabled:opacity-50 transition-all flex items-center gap-2">
                                    <template x-if="!isFetching">
                                        <span class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm">download</span>
                                            Fetch Details
                                        </span>
                                    </template>
                                    <template x-if="isFetching">
                                        <span class="flex items-center gap-2">
                                            <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Fetching...
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </div>
                        
                        <div x-show="fetchError" x-transition class="px-4 py-2 bg-error/10 text-error rounded-lg text-[9px] font-bold uppercase tracking-widest flex items-center gap-2 border border-error/20">
                            <span class="material-symbols-outlined text-xs">error</span>
                            <span x-text="fetchError"></span>
                        </div>
                        <div x-show="fetchSuccess" x-transition class="px-4 py-2 bg-primary/10 text-primary rounded-lg text-[9px] font-bold uppercase tracking-widest flex items-center gap-2 border border-primary/20">
                            <span class="material-symbols-outlined text-xs">check_circle</span>
                            <span x-text="fetchSuccess"></span>
                            <span class="ml-auto opacity-50 italic normal-case font-medium">Source: Google Books</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        <!-- Left Column: Metadata -->
                        <div class="space-y-8">
                            <div class="space-y-5">
                                <h3 class="text-[9px] font-bold uppercase tracking-widest text-primary flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                    Core Metadata
                                </h3>
                                <div class="space-y-3.5">
                                    <div class="space-y-1">
                                        <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Resource Title</label>
                                        <input name="title" x-model="title" type="text" required class="w-full bg-white border border-outline-variant/20 rounded-xl p-3.5 text-base font-headline italic font-bold focus:ring-2 focus:ring-primary outline-none transition-all">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3.5">
                                        <div class="space-y-1">
                                            <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Author(s)</label>
                                            <input name="author_display" x-model="author_display" type="text" required class="w-full bg-white border border-outline-variant/20 rounded-xl p-3 text-sm font-medium focus:ring-2 focus:ring-primary outline-none">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Publisher</label>
                                            <input name="publisher" x-model="publisher" type="text" class="w-full bg-white border border-outline-variant/20 rounded-xl p-3 text-sm font-medium focus:ring-2 focus:ring-primary outline-none">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-3.5">
                                        <div class="col-span-2 space-y-1">
                                            <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">ISBN</label>
                                            <input name="isbn" x-model="isbn" type="text" class="w-full bg-white border border-outline-variant/20 rounded-xl p-3 text-sm font-medium focus:ring-2 focus:ring-primary outline-none" placeholder="e.g. 9780140445145">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Year</label>
                                            <input name="publication_year" x-model="publication_year" type="number" class="w-full bg-white border border-outline-variant/20 rounded-xl p-3 text-sm font-medium focus:ring-2 focus:ring-primary outline-none">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3.5">
                                        <div class="space-y-1">
                                            <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Language</label>
                                            <input name="language" x-model="language" type="text" class="w-full bg-white border border-outline-variant/20 rounded-xl p-3 text-sm font-medium focus:ring-2 focus:ring-primary outline-none" placeholder="e.g. English">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Page Count</label>
                                            <input name="pages_count" x-model="pages_count" type="number" class="w-full bg-white border border-outline-variant/20 rounded-xl p-3 text-sm font-medium focus:ring-2 focus:ring-primary outline-none">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <h3 class="text-[9px] font-bold uppercase tracking-widest text-secondary flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                    Taxonomy & Classification
                                </h3>
                                <div class="grid grid-cols-2 gap-3.5">
                                    <div class="space-y-1">
                                        <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Resource Type</label>
                                        <select name="resource_type_id" x-model="resource_type_id" required class="w-full bg-white border border-outline-variant/20 rounded-xl p-3 text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary cursor-pointer appearance-none">
                                            <option value="">Select Type...</option>
                                            @foreach($types as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Geographic Region</label>
                                        <select name="region_id" x-model="region_id" class="w-full bg-white border border-outline-variant/20 rounded-xl p-3 text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary cursor-pointer appearance-none">
                                            <option value="">Select Region...</option>
                                            @foreach($regions as $reg)
                                                <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Library Topics</label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5 bg-white border border-outline-variant/20 p-3.5 rounded-xl max-h-40 overflow-y-auto">
                                        @foreach($topics as $topic)
                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                <input type="checkbox" name="topics[]" value="{{ $topic->id }}" x-model="selectedTopics" class="w-3 h-3 rounded border-stone-300 text-primary focus:ring-primary">
                                                <span class="text-[9px] font-medium text-on-surface-variant group-hover:text-on-surface leading-none">{{ $topic->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Abstract & Media -->
                        <div class="space-y-8">
                            <div class="space-y-5">
                                <h3 class="text-[9px] font-bold uppercase tracking-widest text-tertiary flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
                                    Narrative & Abstract
                                </h3>
                                <textarea name="abstract" x-model="abstract" rows="6" required class="w-full bg-white border border-outline-variant/20 rounded-2xl p-4 text-sm leading-relaxed focus:ring-2 focus:ring-primary outline-none transition-all resize-none" placeholder="Provide a scholarly abstract..."></textarea>
                                <div class="space-y-1">
                                    <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Keywords / Tags</label>
                                    <input name="tags" x-model="tags" type="text" class="w-full bg-white border border-outline-variant/20 rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none" placeholder="Separate with commas...">
                                </div>
                            </div>

                            <div class="space-y-5">
                                <h3 class="text-[9px] font-bold uppercase tracking-widest text-orange-700 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-700"></span>
                                    Media Assets & Access
                                </h3>
                                
                                <!-- Cover Image Source Selection -->
                                <div class="space-y-3 bg-white border border-outline-variant/10 p-4 rounded-2xl">
                                    <div class="flex items-center justify-between">
                                        <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest">Cover Source</label>
                                        <div class="flex gap-2 bg-stone-100 p-1 rounded-lg">
                                            <button type="button" @click="coverSource = 'upload'" :class="coverSource === 'upload' ? 'bg-white shadow-sm text-primary' : 'text-stone-400'" class="px-3 py-1 text-[8px] font-bold uppercase rounded-md transition-all">Upload</button>
                                            <button type="button" @click="coverSource = 'isbn'" :class="coverSource === 'isbn' ? 'bg-white shadow-sm text-primary' : 'text-stone-400'" class="px-3 py-1 text-[8px] font-bold uppercase rounded-md transition-all">ISBN Fetch</button>
                                        </div>
                                    </div>

                                    <!-- Current Cover Preview (If editing) -->
                                    <div x-show="modalMode === 'edit' && currentCoverUrl && !fetchedCoverPreview" class="flex items-center gap-4 p-3 bg-stone-50 rounded-xl border border-stone-200">
                                        <img :src="currentCoverUrl" class="w-12 h-16 object-cover rounded shadow-sm">
                                        <div>
                                            <p class="text-[9px] font-bold uppercase text-on-surface">Current Cover</p>
                                            <p class="text-[8px] text-stone-400 italic">Will be preserved if no new file is selected.</p>
                                        </div>
                                    </div>

                                    <div x-show="coverSource === 'upload'" class="space-y-2">
                                        <input name="cover_image" type="file" accept="image/*" class="w-full text-[9px] text-stone-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[8px] file:font-bold file:uppercase file:bg-primary/10 file:text-primary cursor-pointer">
                                        <p class="text-[8px] text-stone-400 italic">Select a portrait-style JPG or PNG cover.</p>
                                    </div>

                                    <div x-show="coverSource === 'isbn'" class="space-y-3">
                                        <div x-show="!fetchedCoverPreview" class="text-[8px] text-stone-400 italic text-center py-4 bg-stone-50 rounded-xl border border-dashed border-stone-200">
                                            Use ISBN lookup above to fetch a cover.
                                        </div>
                                        
                                        <div x-show="fetchedCoverPreview" class="flex items-center gap-4 p-3 bg-stone-50 rounded-xl ring-2 ring-primary/20">
                                            <img :src="fetchedCoverPreview" class="w-12 h-16 object-cover rounded shadow-sm border border-stone-200">
                                            <div>
                                                <p class="text-[9px] font-bold uppercase text-primary">Fetched Cover</p>
                                                <p class="text-[8px] text-stone-400">Ready to save as resource cover.</p>
                                                <button type="button" @click="fetchedCoverPreview = null; fetchedCoverPath = null; coverSource = 'upload'" class="text-[8px] font-bold text-error uppercase mt-1 hover:underline">Clear</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Primary PDF</label>
                                    <input name="resource_file" type="file" accept=".pdf" class="w-full text-[9px] text-stone-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[8px] file:font-bold file:uppercase file:bg-stone-200 file:text-stone-700 cursor-pointer">
                                </div>

                                <div class="flex gap-3">
                                    <button type="button" @click="access_type = 'public'" 
                                            :class="access_type === 'public' ? 'bg-primary/10 border-primary/20 text-primary' : 'bg-white border-stone-100 text-stone-400'" 
                                            class="flex-1 py-2.5 rounded-xl border text-center transition-all text-[8px] font-bold uppercase tracking-widest">
                                        Public Archive
                                    </button>
                                    <button type="button" @click="access_type = 'member_only'" 
                                            :class="access_type === 'member_only' ? 'bg-secondary/10 border-secondary/20 text-secondary' : 'bg-white border-stone-100 text-stone-400'" 
                                            class="flex-1 py-2.5 rounded-xl border text-center transition-all text-[8px] font-bold uppercase tracking-widest">
                                        Member Only
                                    </button>
                                    <input type="hidden" name="access_type" x-model="access_type">
                                </div>
                                <div class="flex items-center justify-between p-3.5 bg-white border border-outline-variant/10 rounded-xl">
                                    <span class="text-[8px] font-bold uppercase tracking-widest text-on-surface-variant">Publication Status</span>
                                    <select name="status" x-model="status" class="bg-stone-50 border-none rounded-lg px-3 py-1 text-[8px] font-bold uppercase tracking-widest focus:ring-1 focus:ring-primary cursor-pointer">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" name="is_featured" value="1" x-model="is_featured" class="w-3 h-3 rounded border-stone-300 text-primary focus:ring-primary">
                                        <span class="text-[8px] font-bold text-on-surface uppercase tracking-widest">Featured</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" name="is_recommended" value="1" x-model="is_recommended" class="w-3 h-3 rounded border-stone-300 text-primary focus:ring-primary">
                                        <span class="text-[8px] font-bold text-on-surface uppercase tracking-widest">Recommended</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="px-8 py-5 bg-white border-t border-outline-variant/10 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5 opacity-50">
                    <span class="material-symbols-outlined text-base">lock</span>
                    <span class="text-[8px] font-bold uppercase tracking-widest">Secure Entry</span>
                </div>
                <div class="flex gap-3">
                    <button @click="modalOpen = false" class="px-6 py-3 rounded-xl text-[9px] font-bold uppercase tracking-widest text-on-surface-variant hover:bg-stone-100 transition-all">Cancel</button>
                    <button form="resourceForm" type="submit" class="bg-primary text-on-primary px-10 py-3 rounded-xl font-bold text-[9px] uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all">
                        <span x-text="modalMode === 'create' ? 'Catalog Resource' : 'Save Changes'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



