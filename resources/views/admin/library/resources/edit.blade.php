@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-12 pb-24" x-data="{ access_type: '{{ old('access_type', $resource->access_type) }}' }">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.library.resources.index') }}" class="w-12 h-12 bg-surface-container-low rounded-2xl flex items-center justify-center text-stone-400 hover:text-on-surface transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="font-headline text-4xl text-on-surface italic font-bold">Edit Publication</h1>
                <p class="font-body text-on-surface-variant">Update metadata for <span class="text-primary font-bold">{{ $resource->title }}</span></p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.library.resources.update', $resource) }}" method="POST" enctype="multipart/form-data" class="space-y-12">
        @csrf
        @method('PUT')

        <!-- Section 1: Basic Information -->
        <div class="bg-white rounded-[40px] p-10 border border-outline-variant/10 shadow-sm space-y-8">
            <div class="flex items-center gap-4 pb-6 border-b border-outline-variant/5">
                <div class="w-10 h-10 bg-primary/5 rounded-xl flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-sm">menu_book</span>
                </div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-on-surface">1. Basic Information</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="col-span-2 space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Resource Title</label>
                    <input name="title" type="text" value="{{ old('title', $resource->title) }}" required class="w-full bg-surface-container-low border-none rounded-2xl p-5 text-xl font-headline italic font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. The Elementary Structures of Kinship">
                    @error('title') <span class="text-error text-[10px] font-bold px-4">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Authors (Display Name)</label>
                    <input name="author_display" type="text" value="{{ old('author_display', $resource->author_display) }}" required class="w-full bg-surface-container-low border-none rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. Claude Lévi-Strauss">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Publisher</label>
                    <input name="publisher" type="text" value="{{ old('publisher', $resource->publisher) }}" class="w-full bg-surface-container-low border-none rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. Presses Universitaires de France">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Publication Year</label>
                    <input name="publication_year" type="number" value="{{ old('publication_year', $resource->publication_year) }}" class="w-full bg-surface-container-low border-none rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. 1949">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">ISBN</label>
                    <div class="flex gap-2">
                        <input name="isbn" type="text" id="isbn-input" value="{{ old('isbn', $resource->isbn) }}" class="flex-1 bg-surface-container-low border-none rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. 9780140445145">
                        <button type="button" onclick="lookupIsbnEdit()" class="bg-primary text-on-primary px-6 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:opacity-90 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">search</span>
                            Fetch
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Language</label>
                    <input name="language" type="text" value="{{ old('language', $resource->language) }}" class="w-full bg-surface-container-low border-none rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. English, French">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Page Count</label>
                    <input name="pages_count" type="number" value="{{ old('pages_count', $resource->pages_count) }}" class="w-full bg-surface-container-low border-none rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. 432">
                </div>
            </div>
        </div>

        <!-- Section 2: Classification -->
        <div class="bg-white rounded-[40px] p-10 border border-outline-variant/10 shadow-sm space-y-8">
            <div class="flex items-center gap-4 pb-6 border-b border-outline-variant/5">
                <div class="w-10 h-10 bg-secondary/5 rounded-xl flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-sm">category</span>
                </div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-on-surface">2. Classification & Taxonomy</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Resource Type</label>
                    <select name="resource_type_id" required class="w-full bg-surface-container-low border-none rounded-xl p-4 text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary cursor-pointer appearance-none">
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ $resource->resource_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">

                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Geographic Region</label>
                    <select name="region_id" class="w-full bg-surface-container-low border-none rounded-xl p-4 text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary cursor-pointer appearance-none">
                        <option value="">Select Region...</option>
                        @foreach($regions as $reg)
                            <option value="{{ $reg->id }}" {{ $resource->region_id == $reg->id ? 'selected' : '' }}>{{ $reg->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Library Topics</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-surface-container-low p-6 rounded-[32px]">
                    @php $selectedTopics = $resource->topics->pluck('id')->toArray(); @endphp
                    @foreach($topics as $topic)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="topics[]" value="{{ $topic->id }}" {{ in_array($topic->id, $selectedTopics) ? 'checked' : '' }} class="w-4 h-4 rounded border-stone-300 text-primary focus:ring-primary transition-all">
                            <span class="text-[11px] font-medium text-on-surface-variant group-hover:text-on-surface">{{ $topic->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Keywords / Tags</label>
                <input name="tags" type="text" value="{{ old('tags', $tagsString) }}" class="w-full bg-surface-container-low border-none rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. Kinship, Social Structure, Fieldwork (Separate with commas)">
            </div>
        </div>

        <!-- Section 3: Narrative & Content -->
        <div class="bg-white rounded-[40px] p-10 border border-outline-variant/10 shadow-sm space-y-8">
            <div class="flex items-center gap-4 pb-6 border-b border-outline-variant/5">
                <div class="w-10 h-10 bg-tertiary/5 rounded-xl flex items-center justify-center text-tertiary">
                    <span class="material-symbols-outlined text-sm">description</span>
                </div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-on-surface">3. Abstract & Content</h3>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Academic Abstract</label>
                <textarea name="abstract" rows="8" required class="w-full bg-surface-container-low border-none rounded-[32px] p-6 text-sm leading-relaxed focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none resize-none" placeholder="Summarize the core hypothesis, methodology, and findings...">{{ old('abstract', $resource->abstract) }}</textarea>
            </div>
        </div>

        <!-- Section 4: Files & Access -->
        <div class="bg-white rounded-[40px] p-10 border border-outline-variant/10 shadow-sm space-y-8">
            <div class="flex items-center gap-4 pb-6 border-b border-outline-variant/5">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-700">
                    <span class="material-symbols-outlined text-sm">upload_file</span>
                </div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-on-surface">4. Media & Access Control</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Cover Image</label>
                        @if($resource->cover_image_path)
                            <div class="mb-4">
                                <img src="{{ Storage::url($resource->cover_image_path) }}" class="h-32 rounded-xl border border-stone-200">
                            </div>
                        @endif
                        <input name="cover_image" type="file" accept="image/*" class="w-full text-xs text-stone-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:uppercase file:tracking-widest file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Primary Resource File (PDF)</label>
                        @if($resource->file_path)
                            <p class="text-[10px] text-primary font-bold mb-2 uppercase">Current file: {{ basename($resource->file_path) }}</p>
                        @endif
                        <input name="resource_file" type="file" accept=".pdf" class="w-full text-xs text-stone-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:uppercase file:tracking-widest file:bg-stone-100 file:text-stone-600 hover:file:bg-stone-200 cursor-pointer">
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Access Model</label>
                        <div class="flex gap-4">
                            <button type="button" @click="access_type = 'public'" 
                                    :class="access_type === 'public' ? 'bg-primary/10 border-primary/20 text-primary' : 'bg-stone-50 border-stone-100 text-stone-400'" 
                                    class="flex-1 p-4 rounded-2xl border text-center transition-all text-[10px] font-bold uppercase tracking-widest">
                                Public Archive
                            </button>
                            <button type="button" @click="access_type = 'member_only'" 
                                    :class="access_type === 'member_only' ? 'bg-secondary/10 border-secondary/20 text-secondary' : 'bg-stone-50 border-stone-100 text-stone-400'" 
                                    class="flex-1 p-4 rounded-2xl border text-center transition-all text-[10px] font-bold uppercase tracking-widest">
                                Member Only
                            </button>
                            <input type="hidden" name="access_type" x-model="access_type">
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-2xl">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Publication Status</span>
                        <select name="status" class="bg-white border-none rounded-xl px-4 py-2 text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary cursor-pointer">
                            <option value="draft" {{ $resource->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ $resource->status === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ $resource->status === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-6">
                @foreach(['is_featured' => 'Featured', 'is_recommended' => 'Recommended', 'is_editors_pick' => "Editor's Pick", 'allow_download' => 'Allow Download'] as $field => $label)
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative inline-flex items-center">
                            <input name="{{ $field }}" type="checkbox" value="1" {{ $resource->$field ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                        </div>
                        <span class="text-[10px] font-bold text-on-surface uppercase tracking-widest">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Sticky Submit Bar -->
        <div class="sticky bottom-8 z-30 bg-surface-container-lowest/80 backdrop-blur-md p-6 rounded-[32px] border border-outline-variant/20 shadow-2xl flex items-center justify-between">
            <div class="flex items-center gap-3 px-4">
                <span class="material-symbols-outlined text-stone-400">shield_person</span>
                <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Editing as {{ Auth::user()->name }}</span>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('admin.library.resources.index') }}" class="px-8 py-4 rounded-2xl text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:bg-surface-container-high transition-all">Cancel</a>
                <button type="submit" class="bg-primary text-on-primary px-12 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all">
                    Update Publication
                </button>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
async function lookupIsbnEdit() {
    const isbn = document.getElementById('isbn-input').value;
    if (!isbn) {
        alert('Please enter an ISBN first.');
        return;
    }

    try {
        const response = await fetch('{{ route('admin.library.resources.lookup-isbn') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ isbn: isbn })
        });

        const result = await response.json();

        if (result.success) {
            const data = result.data;
            if (confirm('Book details found. Auto-fill fields?')) {
                document.querySelector('input[name="title"]').value = data.title || '';
                document.querySelector('input[name="author_display"]').value = data.authors || '';
                document.querySelector('input[name="publisher"]').value = data.publisher || '';
                document.querySelector('input[name="publication_year"]').value = data.publication_year || '';
                document.querySelector('input[name="language"]').value = data.language || '';
                document.querySelector('input[name="pages_count"]').value = data.page_count || '';
                
                if (data.categories && data.categories.length > 0) {
                    document.querySelector('input[name="tags"]').value = data.categories.join(', ');
                }

                if (data.stored_cover_url) {
                    alert('Book metadata updated. Cover image was also fetched and will be applied if you don\'t upload a new one.');
                    // Add hidden fields for cover
                    let coverPathInput = document.getElementById('fetched_cover_path');
                    if (!coverPathInput) {
                        coverPathInput = document.createElement('input');
                        coverPathInput.type = 'hidden';
                        coverPathInput.id = 'fetched_cover_path';
                        coverPathInput.name = 'fetched_cover_path';
                        document.querySelector('form').appendChild(coverPathInput);
                    }
                    coverPathInput.value = data.stored_cover_path;

                    let coverExtInput = document.getElementById('cover_external_url');
                    if (!coverExtInput) {
                        coverExtInput = document.createElement('input');
                        coverExtInput.type = 'hidden';
                        coverExtInput.id = 'cover_external_url';
                        coverExtInput.name = 'cover_external_url';
                        document.querySelector('form').appendChild(coverExtInput);
                    }
                    coverExtInput.value = data.cover_source_url;
                }
            }
        } else {
            alert(result.message);
        }
    } catch (e) {
        alert('System error occurred during lookup.');
    }
}
</script>
@endpush
@endsection
