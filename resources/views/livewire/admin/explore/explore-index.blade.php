<div x-data="articleManager()"
     x-on:article-saved.window="modalOpen = false; document.body.style.overflow = 'auto'"
     class="relative">
    
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

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-12">
        <div>
            <h1 class="font-headline text-4xl text-on-surface mb-2 italic">Explore Humanity Management</h1>
            <p class="font-body text-on-surface-variant text-lg">Curate editorial narratives and research highlights for the public Explore page</p>
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
        <div class="p-6 border-b border-outline-variant/10 flex flex-wrap gap-4 items-center justify-between bg-surface-container-low/30">
            <div class="flex gap-4 items-center flex-1 min-w-[300px]">
                <div class="relative flex-1 max-w-sm">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                    <input wire:model.live.debounce.300ms="search" class="w-full bg-white border border-outline-variant/20 rounded-xl pl-10 pr-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm" placeholder="Search archives..." type="text"/>
                </div>
                <select wire:model.live="topic_filter_id" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                    <option value="">All Topics</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="status_filter" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Drafts</option>
                    <option value="archived">Archived</option>
                </select>
                <select wire:model.live="access_filter" class="bg-white border border-outline-variant/20 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-primary transition-all shadow-sm cursor-pointer">
                    <option value="">All Access</option>
                    <option value="members">Members Only</option>
                    <option value="public">Publicly Available</option>
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
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low/50">
                    <tr>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Article Details</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Topic</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Access</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Status</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Feat.</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Last Updated</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($articles as $article)
                        <tr class="hover:bg-surface-container-low/20 transition-colors group">
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
                                        <p class="text-[10px] text-stone-400 font-mono italic">{{ $article->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">{{ $article->topic->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleMembersOnly({{ $article->id }})" class="flex items-center gap-1.5 transition-opacity hover:opacity-80">
                                    @if($article->is_members_only)
                                        <span class="flex items-center gap-1.5 text-primary">
                                            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                                            <span class="text-[10px] font-bold uppercase tracking-widest">Members</span>
                                        </span>
                                    @else
                                        <span class="flex items-center gap-1.5 text-stone-400">
                                            <span class="material-symbols-outlined text-sm">public</span>
                                            <span class="text-[10px] font-bold uppercase tracking-widest">Public</span>
                                        </span>
                                    @endif
                                </button>
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
                                <button wire:click="toggleFeatured({{ $article->id }})" class="p-1 rounded-lg transition-colors {{ $article->is_featured ? 'text-primary' : 'text-stone-300 hover:text-stone-400' }}">
                                    <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' {{ $article->is_featured ? 1 : 0 }};">star</span>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-[11px] text-on-surface-variant font-mono italic">
                                {{ $article->updated_at->format('M d, Y') }}
                            </td>
                             <td class="px-6 py-4 text-right whitespace-nowrap overflow-visible">
                                 <div x-data="{ open: false }" class="relative inline-flex justify-end items-center">
                                     <button @click="open = !open" @click.away="open = false" class="p-2 text-stone-400 hover:text-on-surface transition-colors rounded-full hover:bg-surface-container-high">
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
                                             Edit Article
                                         </button>
                                         <button @click="open = false" wire:click="deleteArticle({{ $article->id }})" wire:confirm="Move this narrative to trash?" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-error hover:bg-error/5 transition-colors flex items-center gap-2 border-t border-outline-variant/10">
                                             <span class="material-symbols-outlined text-sm">delete</span>
                                             Delete Article
                                         </button>
                                     </div>
                                 </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
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
        @if($articles->hasPages())
            <div class="px-6 py-6 border-t border-outline-variant/10 bg-surface-container-low/10">
                {{ $articles->links() }}
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
             wire:ignore.self
             class="bg-surface-container-lowest rounded-[32px] shadow-2xl ring-1 ring-white/10 w-full max-w-6xl overflow-hidden relative z-10 flex flex-col h-[90vh]"
             x-transition:enter="ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0 translate-y-4"
             x-transition:enter-end="scale-100 opacity-100 translate-y-0"
             x-transition:leave="ease-in duration-200 transform"
             x-transition:leave-start="scale-100 opacity-100 translate-y-0"
             x-transition:leave-end="scale-95 opacity-0 translate-y-4">
            
            <form wire:submit.prevent="saveArticle" class="flex flex-col h-full">
                <div class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/30">
                    <div>
                        <h4 class="font-headline text-2xl text-on-surface italic font-bold leading-tight">{{ $editingArticle ? 'Modify Narrative' : 'Create New Narrative' }}</h4>
                        <p class="text-[10px] uppercase font-bold text-stone-400 tracking-widest mt-1">Explore Humanity Editor</p>
                    </div>
                    <button type="button" @click="closeModal()" class="text-stone-400 hover:text-on-surface transition-colors p-2">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-10">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                        
                        <!-- Main Editorial Fields (8 Col) -->
                        <div class="lg:col-span-8 space-y-10">
                            <div class="space-y-4">
                                <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-4">Article Title</label>
                                <input wire:model.live.debounce.300ms="title" type="text" class="w-full bg-surface-container-low border border-transparent rounded-2xl p-6 text-2xl font-headline italic font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="Enter a compelling title for the archive...">
                                @error('title') <span class="text-[10px] text-error font-medium px-4">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 px-4">
                                <div class="space-y-2">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Topic</label>
                                    <select wire:model="topic_id" class="w-full bg-surface-container-low border border-transparent rounded-xl px-5 py-4 text-xs font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none cursor-pointer">
                                        <option value="">Choose Topic...</option>
                                        @foreach($topics as $topic)
                                            <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('topic_id') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">URL Slug</label>
                                    <input wire:model="slug" type="text" class="w-full bg-surface-container-low border border-transparent rounded-xl px-5 py-4 text-xs font-mono text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="article-url-slug">
                                    @error('slug') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="space-y-2 px-4">
                                <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Short Summary (Excerpt)</label>
                                <textarea wire:model="excerpt" rows="3" class="w-full bg-surface-container-low border border-transparent rounded-2xl p-5 text-sm leading-relaxed text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none resize-none" placeholder="Briefly describe what this article covers..."></textarea>
                                @error('excerpt') <span class="text-[10px] text-error font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center px-4">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Narrative Content</label>
                                    <span class="text-[9px] uppercase font-bold text-stone-400 bg-stone-50 px-2 py-0.5 rounded border">Markdown Enabled</span>
                                </div>
                                <div class="rounded-[28px] overflow-hidden border border-outline-variant/10 shadow-inner">
                                    <x-markdown-editor wire:model="markdown_content" :wire:key="'editor-'.$modalSessionId" />
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
                                    @if($featured_image)
                                        <img src="{{ $featured_image->temporaryUrl() }}" class="object-cover w-full h-full" alt="">
                                    @elseif($existing_image)
                                        <img src="{{ Storage::url($existing_image) }}" class="object-cover w-full h-full" alt="">
                                    @else
                                        <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                                            <span class="material-symbols-outlined text-4xl text-stone-300 mb-2">add_photo_alternate</span>
                                            <p class="text-[9px] uppercase font-bold text-stone-400 leading-tight">Drop narrative <br>image here</p>
                                        </div>
                                    @endif
                                    <input type="file" wire:model="featured_image" class="absolute inset-0 opacity-0 cursor-pointer">
                                    
                                    <div wire:loading wire:target="featured_image" class="absolute inset-0 bg-white/80 flex items-center justify-center">
                                        <svg class="animate-spin h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </div>
                                </div>
                                <p class="text-[9px] text-stone-400 italic px-1">Optimized for 16:9 ratio. Max 2MB.</p>
                                @error('featured_image') <span class="text-[10px] text-error font-medium px-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Visibility & Availability Settings -->
                            <div class="bg-surface-container-low/50 border border-outline-variant/10 rounded-[28px] p-8 space-y-8">
                                <!-- Members Only Access -->
                                <div class="space-y-4">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest block">Scholar Access</label>
                                    <label class="flex items-center gap-3 cursor-pointer group w-fit">
                                        <div class="relative inline-flex items-center">
                                            <input wire:model="is_members_only" type="checkbox" class="sr-only peer">
                                            <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-on-surface uppercase tracking-widest">Members Only Access</span>
                                            <span class="text-[8px] text-stone-400 uppercase tracking-tight">Restrict to active scholars</span>
                                        </div>
                                    </label>
                                </div>

                                <!-- Publication Status -->
                                <div class="space-y-4 pt-6 border-t border-outline-variant/10">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest block">Publication Status</label>
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach(['draft' => ['icon' => 'edit_note', 'label' => 'Internal Draft'], 'published' => ['icon' => 'verified', 'label' => 'Publicly Live'], 'archived' => ['icon' => 'inventory_2', 'label' => 'Archived']] as $val => $meta)
                                            <label class="flex items-center gap-3 p-4 rounded-xl border cursor-pointer transition-all {{ $status === $val ? 'bg-white border-primary/20 shadow-sm' : 'bg-transparent border-transparent grayscale' }}">
                                                <input type="radio" wire:model="status" value="{{ $val }}" class="hidden">
                                                <span class="material-symbols-outlined text-sm {{ $status === $val ? 'text-primary' : 'text-stone-400' }}">{{ $meta['icon'] }}</span>
                                                <span class="text-[10px] font-bold uppercase tracking-widest {{ $status === $val ? 'text-on-surface' : 'text-stone-400' }}">{{ $meta['label'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Scholar Access -->
                                <div class="space-y-4 pt-6 border-t border-outline-variant/10">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest block">Scholar Access</label>
                                    <label class="flex items-center gap-3 cursor-pointer group w-fit">
                                        <div class="relative inline-flex items-center">
                                            <input wire:model="is_members_only" type="checkbox" class="sr-only peer">
                                            <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-on-surface uppercase tracking-widest">Members Only Access</span>
                                            <span class="text-[8px] text-stone-400 uppercase tracking-tight">Restrict to active scholars</span>
                                        </div>
                                    </label>
                                </div>

                                <!-- Featured Toggle -->
                                <div class="space-y-4 pt-6 border-t border-outline-variant/10">
                                    <label class="flex items-center gap-3 cursor-pointer group w-fit">
                                        <div class="relative inline-flex items-center">
                                            <input wire:model="is_featured" type="checkbox" class="sr-only peer">
                                            <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Mark as Featured</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Metadata Info -->
                            @if($editingArticle)
                                <div class="px-8 flex flex-col gap-2">
                                    <p class="text-[9px] uppercase font-bold text-stone-400 tracking-tight">System Metadata</p>
                                    <p class="text-[10px] text-on-surface-variant italic">
                                        Penned by <strong class="text-on-surface">{{ $editingArticle->creator->name }}</strong><br>
                                        Version initialized on {{ $editingArticle->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-outline-variant/10 bg-surface-container-low/30 flex justify-end items-center gap-4">
                    <button type="button" @click="closeModal()" class="px-10 py-4 rounded-2xl text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:bg-surface-container-high transition-all">Discard Refinements</button>
                    <button type="submit" wire:loading.attr="disabled" class="bg-primary text-on-primary px-12 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading class="mr-2 inline-block w-2 h-2 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        {{ $editingArticle ? 'Update Archive' : 'Commit to Archive' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function articleManager() {
            return {
                modalOpen: false,
                openModal(id = null) {
                    this.modalOpen = true;
                    document.body.style.overflow = 'hidden';
                    this.$wire.openArticleModal(id);
                },
                closeModal() {
                    this.modalOpen = false;
                    document.body.style.overflow = 'auto';
                    this.$wire.closeArticleModal();
                }
            }
        }
    </script>
</div>
