@extends('layouts.admin')

@section('content')
<div x-data="articleManager()" x-init="initSortable()" class="relative">
    
    <!-- Info Banner (Success Message) -->
    @if(session('success'))
        <div class="mb-8 px-6 py-4 bg-primary/10 text-primary rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center justify-between border border-primary/20 animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                {{ session('success') }}
            </div>
            <button @click="location.reload()" class="hover:rotate-180 transition-transform duration-500">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-8 px-6 py-4 bg-error/10 text-error rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center justify-between border border-error/20 animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-sm">error</span>
                {{ session('error') }}
            </div>
            <button @click="location.reload()" class="hover:rotate-180 transition-transform duration-500">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-12">
        <div>
            <h1 class="font-headline text-4xl text-on-surface mb-2 italic">Explore Humanity Management</h1>
            <p class="font-body text-on-surface-variant text-lg">Curate editorial narratives and research highlights for the public Explore page.</p>
        </div>
        <button @click="openModal()" class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-[10px] flex items-center gap-2 shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">add</span>
            Create Article
        </button>
    </div>

    <!-- Info Banner -->
    <div class="bg-secondary-container/30 border border-secondary/10 p-6 rounded-3xl mb-12 flex gap-6 items-center">
        <div class="w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-secondary">explore</span>
        </div>
        <div>
            <h4 class="font-bold text-secondary text-sm mb-1 uppercase tracking-widest">Public Narratives</h4>
            <p class="text-on-surface-variant text-sm leading-relaxed">
                The Explore section is our public-facing archive. Each narrative here should be meticulously crafted with high-quality imagery and thoughtful commentary.
            </p>
        </div>
    </div>

    <!-- Management Controls -->
    <div class="bg-surface-container-lowest rounded-3xl shadow-sm border border-outline-variant/10 overflow-hidden mb-8">
        <form id="filterForm" action="{{ route('admin.explore.index') }}" method="GET" class="p-6 border-b border-outline-variant/10 flex flex-wrap gap-4 items-center justify-between bg-surface-container-low/30">
            <div class="flex gap-4 items-center flex-1 min-w-[300px]">
                <div class="relative flex-1 max-w-sm">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                    <input name="search" value="{{ request('search') }}" onchange="document.getElementById('filterForm').submit()" class="w-full bg-white border border-outline-variant/20 rounded-xl pl-10 pr-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm" placeholder="Search archives..." type="text"/>
                </div>
                @foreach($filterableTagGroups as $group)
                    <select name="tag_ids[]" onchange="document.getElementById('filterForm').submit()" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                        <option value="">All {{ $group->name }}</option>
                        @foreach($group->activeTags as $tag)
                            <option value="{{ $tag->id }}" {{ in_array($tag->id, request('tag_ids', [])) ? 'selected' : '' }}>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                @endforeach
                <select name="status_filter" onchange="document.getElementById('filterForm').submit()" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status_filter') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status_filter') == 'draft' ? 'selected' : '' }}>Drafts</option>
                    <option value="archived" {{ request('status_filter') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <select name="upsc_filter" onchange="document.getElementById('filterForm').submit()" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                    <option value="all">All UPSC Status</option>
                    <option value="upsc" {{ request('upsc_filter') == 'upsc' ? 'selected' : '' }}>UPSC Relevant</option>
                    <option value="general" {{ request('upsc_filter') == 'general' ? 'selected' : '' }}>General</option>
                </select>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] uppercase font-bold text-stone-400">Total:</span>
                    <span class="text-xs font-bold">{{ $stats['total'] }}</span>
                </div>
                <div class="w-px h-4 bg-outline-variant/20"></div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] uppercase font-bold text-stone-400">Live:</span>
                    <span class="text-xs font-bold text-primary">{{ $stats['published'] }}</span>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low/50">
                    <tr>
                        <th class="px-6 py-4 w-10"></th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Article Details</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Classification</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Status</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Feat.</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Last Updated</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="explore-articles-body" class="divide-y divide-outline-variant/10">
                    @forelse($articles as $article)
                        <tr data-id="{{ $article->id }}" class="hover:bg-surface-container-low/20 transition-colors group">
                            <td class="px-6 py-4 text-center cursor-grab active:cursor-grabbing js-drag-handle">
                                <span class="material-symbols-outlined text-stone-300 group-hover:text-stone-400">drag_indicator</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-16 bg-stone-100 rounded-lg overflow-hidden shrink-0 border border-outline-variant/10">
                                        @if($article->featured_image)
                                            <img src="{{ Storage::url($article->featured_image) }}" class="object-cover h-full w-full" alt="">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center opacity-10">
                                                <span class="material-symbols-outlined text-sm">image</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-headline font-bold text-on-surface mb-0.5 leading-tight">{{ $article->title }}</p>
                                        <div class="flex items-center gap-2">
                                            <p class="text-[10px] text-stone-400 font-mono italic">{{ $article->slug }}</p>
                                            @if($article->is_upsc_relevant)
                                                <span class="badge bg-warning-subtle text-warning text-[8px] uppercase font-bold px-2 py-0.5 rounded">UPSC</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($article->tags as $tag)
                                        <span class="text-[9px] font-bold uppercase tracking-tighter px-2 py-0.5 rounded bg-surface-container-highest text-on-surface-variant">{{ $tag->name }}</span>
                                    @empty
                                        <span class="text-[9px] font-bold uppercase tracking-tighter text-stone-300">Untagged</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($article->status) {
                                        'published' => 'bg-primary-container text-on-primary-container',
                                        'draft' => 'bg-surface-container-highest text-on-surface-variant',
                                        'archived' => 'bg-error-container text-on-error-container',
                                        default => 'bg-stone-100'
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase {{ $statusColor }}">
                                    {{ $article->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <form action="{{ route('admin.explore.toggle-featured', $article) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="p-1 rounded-lg transition-colors {{ $article->is_featured ? 'text-primary' : 'text-stone-300 hover:text-stone-400' }}" title="Toggle Featured">
                                            <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' {{ $article->is_featured ? 1 : 0 }};">star</span>
                                        </button>
                                    </form>
                                    <div class="h-4 w-px bg-outline-variant/10"></div>
                                    <button type="button" @click="toggleMembersOnly({{ $article->id }})" class="p-1 rounded-lg transition-colors {{ $article->is_members_only ? 'text-primary' : 'text-stone-300 hover:text-stone-400' }}" title="Toggle Members Only">
                                        <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' {{ $article->is_members_only ? 1 : 0 }}; transition: all 0.3s ease;">{{ $article->is_members_only ? 'shield_person' : 'no_accounts' }}</span>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[11px] text-on-surface-variant font-mono italic">
                                {{ $article->updated_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap overflow-visible">
                                <div x-data="{ open: false }" class="relative inline-flex justify-end items-center">
                                    <button @click="open = !open" @click.away="open = false" class="p-2 text-stone-400 hover:text-on-surface transition-colors rounded-full hover:bg-surface-container-high transition-colors">
                                        <span class="material-symbols-outlined text-sm">more_vert</span>
                                    </button>
                                    
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
                                         x-cloak
                                         class="absolute right-0 top-1/2 -translate-y-1/2 mr-10 w-48 bg-surface-container-lowest rounded-xl shadow-2xl border border-outline-variant/20 z-[100] overflow-hidden text-left">
                                        <button type="button" @click="open = false; openModal({{ $article->id }})" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm text-primary">edit_note</span>
                                            Update Narrative
                                        </button>
                                        
                                        <form id="delete-article-{{ $article->id }}" action="{{ route('admin.explore.destroy', $article) }}" method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button" @click="open = false; $dispatch('open-delete-modal', { 
                                                    title: 'Archive Narrative', 
                                                    message: 'Move this narrative to trash?', 
                                                    action: { type: 'form', target: 'delete-article-{{ $article->id }}' } 
                                                })" 
                                                class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-error hover:bg-error/5 transition-colors flex items-center gap-2 border-t border-outline-variant/10">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                            Archive Narrative
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-5xl text-outline-variant/30 mb-2">auto_stories</span>
                                    <p class="text-on-surface-variant font-headline text-2xl italic font-bold">The archives are silent.</p>
                                    <p class="text-[10px] uppercase tracking-widest text-stone-400 mt-2 max-w-xs leading-relaxed">No narratives matches your current filtration criteria. Start a new exploration above.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($articles->total() > 0)
            <div class="px-6 py-6 border-t border-outline-variant/10 bg-surface-container-low/10 flex items-center justify-between">
                <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/60 italic">
                    Showing {{ $articles->firstItem() }} to {{ $articles->lastItem() }} of {{ $articles->total() }} narratives
                </div>
                <div class="flex items-center gap-4">
                    @if($articles->onFirstPage())
                        <div class="w-10 h-10 flex items-center justify-center text-stone-300 cursor-not-allowed border border-outline-variant/5 rounded-xl">
                            <span class="material-symbols-outlined text-sm">chevron_left</span>
                        </div>
                    @else
                        <a href="{{ $articles->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center text-primary hover:bg-primary/5 border border-primary/10 rounded-xl transition-all hover:scale-105 active:scale-95 shadow-sm">
                            <span class="material-symbols-outlined text-sm">chevron_left</span>
                        </a>
                    @endif

                    @if($articles->hasMorePages())
                        <a href="{{ $articles->nextPageUrl() }}" class="h-10 px-6 flex items-center justify-center gap-3 text-primary hover:bg-primary/5 border border-primary/10 rounded-xl transition-all hover:scale-105 active:scale-95 shadow-sm group">
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
    </div>

    <!-- Article Management Modal (Large) -->
    <div x-show="modalOpen"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
         x-cloak>
        <div x-show="modalOpen" 
             style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(8px);"
             class="fixed inset-0 transition-opacity"
             @click="closeModal()"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div x-show="modalOpen"
             class="bg-surface-container-lowest rounded-[32px] shadow-2xl ring-1 ring-white/10 w-full max-w-6xl overflow-hidden relative z-10 flex flex-col h-[90vh]"
             x-transition:enter="ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0 translate-y-4"
             x-transition:enter-end="scale-100 opacity-100 translate-y-0"
             x-transition:leave="ease-in duration-200 transform"
             x-transition:leave-start="scale-100 opacity-100 translate-y-0"
             x-transition:leave-end="scale-95 opacity-0 translate-y-4">
            
            <form :action="editingArticleId ? `/admin/explore/${editingArticleId}` : '{{ route('admin.explore.store') }}'" method="POST" enctype="multipart/form-data" class="flex flex-col h-full">
                @csrf
                <template x-if="editingArticleId">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/30">
                    <div>
                        <h4 class="font-headline text-2xl text-on-surface italic font-bold leading-tight" x-text="editingArticleId ? 'Modify Narrative' : 'Create New Narrative'"></h4>
                        <p class="text-[10px] uppercase font-bold text-stone-400 tracking-widest mt-1">Explore Humanity Editor</p>
                    </div>
                    <button type="button" @click="closeModal()" class="text-stone-400 hover:text-on-surface transition-colors p-2">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto no-scrollbar p-10">
                    @if ($errors->any())
                        <div class="p-4 bg-error/10 text-error rounded-xl text-sm mb-6">
                            Please check the form below for errors.
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                        
                        <!-- Main Editorial Fields (8 Col) -->
                        <div class="lg:col-span-8 space-y-10">
                            <div class="space-y-4">
                                <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-4">Article Title</label>
                                <input name="title" x-model="form.title" type="text" required class="w-full bg-surface-container-low border border-transparent rounded-2xl p-6 text-2xl font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="Enter a compelling title for the archive...">
                                @error('title') <span class="text-[10px] text-error font-medium px-4">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 px-4">
                                <div class="col-span-full">
                                    <x-admin.tag-selector id="explore-tag-selector" @change="form.tags = $event.detail" />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">URL Slug</label>
                                    <input name="slug" x-model="form.slug" type="text" readonly class="w-full bg-surface-container-low/50 border border-transparent rounded-xl px-5 py-4 text-xs font-mono text-stone-500 focus:ring-transparent transition-all outline-none cursor-not-allowed select-none" placeholder="article-url-slug">
                                    @error('slug') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="space-y-2 px-4">
                                <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Short Summary (Excerpt)</label>
                                <textarea name="excerpt" x-model="form.excerpt" rows="3" required class="w-full bg-surface-container-low border border-transparent rounded-2xl p-5 text-sm leading-relaxed text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none resize-none" placeholder="Briefly describe what this article covers..."></textarea>
                                @error('excerpt') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center px-4">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Narrative Content</label>
                                    <span class="text-[9px] uppercase font-bold text-stone-400 bg-stone-50 px-2 py-0.5 rounded border">Markdown Enabled</span>
                                </div>
                                <div class="rounded-[28px] overflow-hidden border border-outline-variant/10 shadow-inner">
                                    <template x-if="modalOpen">
                                        <x-markdown-editor name="markdown_content" x-model="form.markdown_content" />
                                    </template>
                                </div>
                                @error('markdown_content') <span class="text-[10px] text-error font-medium px-4">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Media & Settings (4 Col) -->
                        <div class="lg:col-span-4 space-y-10">
                            
                            <!-- Image Upload Section -->
                            <div class="bg-surface-container-low/50 border border-outline-variant/10 rounded-[28px] p-8 space-y-6">
                                <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Featured Imagery</label>
                                
                                <div class="relative aspect-[16/9] bg-stone-100 rounded-2xl overflow-hidden border border-stone-200 group">
                                    <template x-if="imagePreview">
                                        <img :src="imagePreview" class="object-cover w-full h-full" alt="">
                                    </template>
                                    <template x-if="!imagePreview && form.existing_image_url">
                                        <img :src="form.existing_image_url" class="object-cover w-full h-full" alt="">
                                    </template>
                                    <template x-if="!imagePreview && !form.existing_image_url">
                                        <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                                            <span class="material-symbols-outlined text-4xl text-stone-300 mb-2">add_photo_alternate</span>
                                            <p class="text-[9px] uppercase font-bold text-stone-400 leading-tight">Drop narrative <br>image here</p>
                                        </div>
                                    </template>
                                    <input type="file" name="featured_image" accept="image/*" @change="handleImageUpload($event)" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                                <p class="text-[9px] text-stone-400 italic px-1">Optimized for 16:9 ratio. Max 2MB.</p>
                                @error('featured_image') <span class="text-[10px] text-error font-medium px-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Visibility & Status -->
                            <div class="bg-surface-container-low/50 border border-outline-variant/10 rounded-[28px] p-8 space-y-8">
                                <div class="space-y-4">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest block">Publication Status</label>
                                    <select name="status" x-model="form.status" class="w-full bg-surface-container-low border border-transparent rounded-xl px-5 py-4 text-xs font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none cursor-pointer">
                                        <option value="published">Live</option>
                                        <option value="archived">Archive</option>
                                        <option value="draft">Draft</option>
                                    </select>
                                    @error('status') <span class="text-[10px] text-error font-medium px-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="pt-2 flex flex-col gap-4">
                                    <label class="flex items-center gap-3 cursor-pointer group w-fit">
                                        <div class="relative inline-flex items-center">
                                            <input name="is_members_only" type="checkbox" value="1" x-model="form.is_members_only" class="sr-only peer">
                                            <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Members Only Access</span>
                                            <span class="text-[8px] text-stone-400 uppercase tracking-tight italic">Restrict to active scholars</span>
                                        </div>
                                    </label>

                                    <label class="flex items-center gap-3 cursor-pointer group w-fit pt-2 border-t border-outline-variant/5">
                                        <div class="relative inline-flex items-center">
                                            <input name="is_featured" type="checkbox" value="1" x-model="form.is_featured" class="sr-only peer">
                                            <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Mark as Featured Narrative</span>
                                    </label>

                                    <label class="flex items-center gap-3 cursor-pointer group w-fit pt-2 border-t border-outline-variant/5">
                                        <div class="relative inline-flex items-center">
                                            <input name="is_upsc_relevant" type="checkbox" value="1" x-model="form.is_upsc_relevant" class="sr-only peer">
                                            <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-warning"></div>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">UPSC Relevant</span>
                                            <span class="text-[8px] text-stone-400 uppercase tracking-tight italic">Show in UPSC Hub</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-outline-variant/10 bg-surface-container-low/30 flex justify-end items-center gap-4">
                    <button type="button" @click="closeModal()" class="px-10 py-4 rounded-2xl text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:bg-surface-container-high transition-all">Discard Refinements</button>
                    <button type="submit" class="bg-primary text-on-primary px-12 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all">
                        <span x-text="editingArticleId ? 'Update Archive' : 'Commit to Archive'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('articleManager', () => ({
                modalOpen: @json($errors->any()),
                editingArticleId: null,
                imagePreview: null,
                articles: @json($articles->items()),
                form: {
                    tags: [],
                    title: '',
                    slug: '',
                    excerpt: '',
                    markdown_content: '',
                    status: 'published',
                    is_featured: false,
                    is_members_only: false,
                    is_upsc_relevant: false,
                    existing_image_url: null
                },

                init() {
                    // Watch for title changes to auto-generate slug, ONLY if we are creating a new article
                    this.$watch('form.title', (value) => {
                        if (!this.editingArticleId) {
                            this.form.slug = this.slugify(value);
                        }
                    });
                },

                slugify(text) {
                    return text.toString().toLowerCase()
                        .replace(/\s+/g, '-')           // Replace spaces with -
                        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                        .replace(/^-+/, '')             // Trim - from start of text
                        .replace(/-+$/, '');            // Trim - from end of text
                },

                handleImageUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        this.imagePreview = null;
                    }
                },

                openModal(id = null) {
                    this.editingArticleId = id;
                    this.imagePreview = null;
                    
                    if (id) {
                        const article = this.articles.find(a => a.id === id);
                        if (article) {
                            this.form.tags = article.tags ? article.tags.map(t => t.id) : [];
                            this.form.title = article.title;
                            this.form.slug = article.slug;
                            this.form.excerpt = article.excerpt || '';
                            this.form.markdown_content = article.markdown_content || '';
                            this.form.status = article.status || 'draft';
                            this.form.is_featured = article.is_featured ? true : false;
                            this.form.is_members_only = article.is_members_only ? true : false;
                            this.form.is_upsc_relevant = article.is_upsc_relevant ? true : false;
                            this.form.existing_image_url = article.featured_image ? '/storage/' + article.featured_image : null;
                            
                            this.$nextTick(() => {
                                window.dispatchEvent(new CustomEvent('set-tags', { 
                                    detail: { id: 'explore-tag-selector', tags: this.form.tags } 
                                }));
                            });
                        }
                    } else {
                        this.form.tags = [];
                        this.form.title = '';
                        this.form.slug = '';
                        this.form.excerpt = '';
                        this.form.markdown_content = '';
                        this.form.status = 'published';
                        this.form.is_featured = false;
                        this.form.is_members_only = false;
                        this.form.is_upsc_relevant = false;
                        this.form.existing_image_url = null;

                        this.$nextTick(() => {
                            window.dispatchEvent(new CustomEvent('set-tags', { 
                                detail: { id: 'explore-tag-selector', tags: [] } 
                            }));
                        });
                    }
                    this.modalOpen = true;
                    document.body.style.overflow = 'hidden';
                },
                
                closeModal() {
                    this.modalOpen = false;
                    document.body.style.overflow = 'auto';
                },

                toggleMembersOnly(id) {
                    fetch(`/admin/explore/${id}/toggle-members-only`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const article = this.articles.find(a => a.id === id);
                            if (article) article.is_members_only = data.is_members_only;
                            location.reload(); // Refresh to update labels
                        }
                    });
                },

                initSortable() {
                    setTimeout(() => {
                        const el = document.getElementById('explore-articles-body');
                        if (!el) return;

                        window.exploreSortable = new Sortable(el, {
                            handle: '.js-drag-handle',
                            animation: 150,
                            ghostClass: 'bg-primary/5',
                            chosenClass: 'bg-primary/10',
                            onEnd: (evt) => {
                                const ids = Array.from(el.querySelectorAll('tr')).map(tr => tr.dataset.id);
                                
                                fetch("{{ route('admin.explore.reorder') }}", {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ ids: ids })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        console.log('Order updated');
                                    }
                                });
                            }
                        });
                    }, 500);
                }
            }));
        });
    </script>
    @endpush
</div>
@endsection
