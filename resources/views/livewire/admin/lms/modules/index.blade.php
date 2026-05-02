<div x-data="{ modalOpen: @entangle('isModalOpen') }" class="relative">
    <!-- Notifications -->
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
            <h1 class="font-headline text-4xl text-on-surface mb-2 italic">LMS Modules</h1>
            <p class="font-body text-on-surface-variant text-lg">Curate and manage academic units of scholarly anthropology content.</p>
        </div>
        <button wire:click="openCreateModal" class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-[10px] flex items-center gap-2 shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">add_circle</span>
            New Module
        </button>
    </div>

    <!-- Filters & Stats -->
    <div class="bg-surface-container-lowest rounded-3xl shadow-sm border border-outline-variant/10 overflow-hidden mb-12">
        <div class="p-8 border-b border-outline-variant/10 flex flex-wrap gap-6 items-center bg-surface-container-low/20">
            <div class="flex items-center gap-4 flex-1 min-w-[300px]">
                <span class="material-symbols-outlined text-stone-400">search</span>
                <input wire:model.live="search" type="text" placeholder="Search modules..." class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder-stone-400 font-medium">
            </div>
            
            <div class="flex gap-4 items-center flex-wrap">
                @foreach($filterableTagGroups as $group)
                    <select wire:model.live="tagFilters.{{ $group->id }}" class="bg-surface-container-low border-none rounded-xl px-4 py-2 text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary cursor-pointer">
                        <option value="">All {{ $group->name }}</option>
                        @foreach($group->activeTags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                @endforeach

                <select wire:model.live="levelFilter" class="bg-surface-container-low border-none rounded-xl px-4 py-2 text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary cursor-pointer">
                    <option value="">All Levels</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>

                <select wire:model.live="statusFilter" class="bg-surface-container-low border-none rounded-xl px-4 py-2 text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary cursor-pointer">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>

                <select wire:model.live="upscFilter" class="bg-surface-container-low border-none rounded-xl px-4 py-2 text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary cursor-pointer">
                    <option value="all">All UPSC Status</option>
                    <option value="upsc">UPSC Relevant</option>
                    <option value="general">General</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto pb-24">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low/50">
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em]">Module</th>
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em]">Categorization</th>
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em]">Metrics</th>
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em]">Visibility</th>
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($modules as $module)
                        <tr class="hover:bg-surface-container-low/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-stone-100 dark:bg-stone-800 overflow-hidden border border-outline-variant/10 shrink-0">
                                        @if($module->cover_image)
                                            <img src="{{ Storage::url($module->cover_image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center opacity-20">
                                                <span class="material-symbols-outlined">image</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <a href="{{ route('admin.lms.modules.edit', $module) }}" class="font-headline text-lg font-bold text-on-surface italic leading-tight hover:text-primary transition-colors">{{ $module->title }}</a>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] uppercase tracking-widest text-stone-500 font-bold mt-1">{{ $module->level ?? 'General' }}</span>
                                            @if($module->is_upsc_relevant)
                                                <span class="badge bg-warning-subtle text-warning text-[8px] uppercase font-bold px-2 py-0.5 rounded">UPSC</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($module->tags as $tag)
                                        <span class="text-[9px] font-bold uppercase tracking-tighter px-2 py-0.5 rounded bg-primary/10 text-primary">{{ $tag->name }}</span>
                                    @empty
                                        <span class="text-[9px] font-bold uppercase tracking-tighter text-stone-300">Untagged</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex gap-4">
                                    <div class="flex flex-col">
                                        <span class="text-lg font-headline font-bold italic">{{ $module->lessons_count }}</span>
                                        <span class="text-[9px] uppercase tracking-widest text-stone-500 font-bold">Lessons</span>
                                    </div>
                                    <div class="flex flex-col border-l border-outline-variant/20 pl-4">
                                        <span class="text-lg font-headline font-bold italic">{{ $module->resources_count }}</span>
                                        <span class="text-[9px] uppercase tracking-widest text-stone-500 font-bold">Resources</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <button wire:click="togglePublish({{ $module->id }})" class="flex items-center gap-2 group/status">
                                    <div class="w-2 h-2 rounded-full {{ $module->is_published ? 'bg-primary shadow-[0_0_8px_rgba(80,101,42,0.4)]' : 'bg-stone-300' }}"></div>
                                    <span class="text-[10px] font-bold uppercase tracking-widest {{ $module->is_published ? 'text-primary' : 'text-stone-400' }}">
                                        {{ $module->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </button>
                            </td>
                            <td class="px-8 py-6 text-right whitespace-nowrap overflow-visible">
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
                                        
                                        <a href="{{ route('admin.lms.modules.edit', $module) }}" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm text-secondary">settings</span>
                                            Manage Content
                                        </a>

                                        <button @click="open = false; $wire.openEditModal({{ $module->id }})" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm text-primary">edit_note</span>
                                            Edit Details
                                        </button>
                                        
                                        <button type="button" wire:confirm="Are you sure you want to archive this module?" wire:click="deleteModule({{ $module->id }})" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-error hover:bg-error/5 transition-colors flex items-center gap-2 border-t border-outline-variant/10">
                                            <span class="material-symbols-outlined text-sm">archive</span>
                                            Archive Module
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-32 text-center">
                                <div class="flex flex-col items-center opacity-40">
                                    <span class="material-symbols-outlined text-6xl mb-4">school</span>
                                    <p class="font-headline text-2xl italic">The curriculum is empty.</p>
                                    <p class="text-xs uppercase tracking-widest mt-2">No modules found matching your request.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($modules->hasPages())
            <div class="px-8 py-6 border-t border-outline-variant/10 bg-surface-container-low/20">
                {{ $modules->links() }}
            </div>
        @endif
    </div>

    <!-- Core Details Modal -->
    <div x-show="modalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
         x-cloak>
        <div x-show="modalOpen" 
             style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(8px);"
             class="fixed inset-0 transition-opacity"
             @click="modalOpen = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div x-show="modalOpen"
             class="bg-surface-container-lowest rounded-[32px] shadow-2xl ring-1 ring-white/10 w-full max-w-2xl max-h-[90vh] overflow-y-auto relative z-10 flex flex-col"
             x-transition:enter="ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0 translate-y-4"
             x-transition:enter-end="scale-100 opacity-100 translate-y-0"
             x-transition:leave="ease-in duration-200 transform"
             x-transition:leave-start="scale-100 opacity-100 translate-y-0"
             x-transition:leave-end="scale-95 opacity-0 translate-y-4">
            
            <div class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/30">
                <h4 class="font-headline text-2xl text-on-surface italic font-bold" x-text="$wire.moduleId ? 'Identify Record Details' : 'New Knowledge Module'"></h4>
                <button type="button" @click="modalOpen = false" class="text-stone-400 hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form wire:submit.prevent="saveModule" class="p-10 space-y-8">
                <!-- Cover Image Upload -->
                <div class="space-y-4">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1 block">Module Cover Image</label>
                    <div class="relative group aspect-video md:aspect-[21/9] rounded-[24px] overflow-hidden border-2 border-dashed border-outline-variant/30 bg-surface-container-low flex flex-col items-center justify-center cursor-pointer hover:border-primary/50 transition-all">
                        @if ($cover_image)
                            <img src="{{ $cover_image->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif ($existingCoverImage)
                            <img src="{{ Storage::url($existingCoverImage) }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-4xl text-stone-300">add_photo_alternate</span>
                            <span class="text-[10px] font-bold uppercase tracking-tight text-stone-400 mt-2">Upload Module Cover (16:9 recommended)</span>
                        @endif
                        <input type="file" wire:model="cover_image" class="absolute inset-0 opacity-0 cursor-pointer">
                        <div wire:loading wire:target="cover_image" class="absolute inset-0 bg-white/80 flex items-center justify-center">
                            <span class="text-[9px] font-bold uppercase tracking-widest animate-pulse text-primary">Digitizing...</span>
                        </div>
                    </div>
                    @error('cover_image') <span class="text-[10px] text-error font-medium px-4">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Module Title</label>
                            <input wire:model.live="title" type="text" required class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl p-5 text-lg font-headline italic font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. Ritual & Symbol">
                            @error('title') <span class="text-[10px] text-error font-medium px-4">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Identifier Slug</label>
                            <input wire:model="slug" type="text" required class="w-full bg-surface-container-low/50 border border-outline-variant/30 rounded-2xl p-5 text-sm font-medium text-stone-500 outline-none cursor-not-allowed" readonly placeholder="Auto-generated">
                            @error('slug') <span class="text-[10px] text-error font-medium px-4">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Short Abstract (Executive Summary)</label>
                        <textarea wire:model="short_description" rows="2" required class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none resize-none" placeholder="A brief scholarly abstract of this unit..."></textarea>
                        @error('short_description') <span class="text-[10px] text-error font-medium px-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Detailed Module Overview</label>
                        <textarea wire:model="overview" rows="6" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="Elaborate on the module structure, objectives, and narratives..."></textarea>
                        @error('overview') <span class="text-[10px] text-error font-medium px-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-full">
                            <x-admin.tag-selector id="lms-module-tag-selector" wire:model="tags" />
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Academic Level</label>
                            <select wire:model="level" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none appearance-none cursor-pointer">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            @error('level') <span class="text-[10px] text-error font-medium px-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-outline-variant/10">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative inline-flex items-center">
                                <input wire:model="is_published" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </div>
                            <span class="text-[10px] font-bold text-on-surface uppercase tracking-widest">Publish Immediately</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative inline-flex items-center">
                                <input wire:model="is_upsc_relevant" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </div>
                            <span class="text-[10px] font-bold text-on-surface uppercase tracking-widest">UPSC Relevant</span>
                        </label>

                        <div class="flex gap-4">
                            <button type="button" @click="modalOpen = false" class="px-8 py-4 rounded-2xl text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:bg-surface-container-high transition-all">Cancel</button>
                            <button type="submit" class="bg-primary text-on-primary px-10 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-2">
                                <span wire:loading.remove wire:target="saveModule">Identify Record</span>
                                <span wire:loading wire:target="saveModule" class="animate-pulse">Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
