<div class="relative pb-24">
    @push('styles')
    <style>
        .sortable-ghost {
            opacity: 0.4;
            background: var(--md-sys-color-primary-container);
            border: 2px dashed var(--md-sys-color-primary);
        }
    </style>
    @endpush
    <!-- Header -->
    <div class="flex justify-between items-center mb-10">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.lms.modules.index') }}" class="text-stone-400 hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Back to Modules</span>
                </a>
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/60 italic">/ {{ $title }}</span>
            </div>
            <h1 class="font-headline text-4xl text-on-surface italic font-bold">
                {{ $title }}
            </h1>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/60 mt-1 italic">Knowledge Module Assembly & Configuration</p>
        </div>
        <div class="flex items-center gap-4">
            <button wire:click="saveModule" class="bg-primary text-on-primary px-10 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all">
                Save Configuration
            </button>
        </div>
    </div>

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Lesson Manager -->
            <section class="bg-surface-container-lowest rounded-[32px] p-10 border border-outline-variant/10 shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-center mb-10 border-b border-outline-variant/20 pb-4">
                    <h4 class="font-headline text-xl text-on-surface italic font-bold flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary">play_circle</span>
                        Film & Video Lectures
                    </h4>
                    @if($isEdit)
                    <button @click="$wire.openLessonModal()" class="bg-secondary/10 text-secondary px-4 py-2 rounded-xl font-bold uppercase tracking-widest text-[9px] flex items-center gap-2 hover:bg-secondary/20 transition-all">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Assemble Lesson
                    </button>
                    @endif
                </div>

                @if(!$isEdit)
                <div class="py-20 text-center opacity-30 select-none">
                    <span class="material-symbols-outlined text-6xl mb-4">lock</span>
                    <p class="font-headline text-xl italic font-bold">Assembly Locked</p>
                    <p class="text-[10px] uppercase tracking-widest mt-2">Create the module core first to enable lesson curation.</p>
                </div>
                @else
                <div class="space-y-4" 
                     x-data 
                     x-init="new Sortable($el, { 
                         handle: '.drag-handle', 
                         ghostClass: 'sortable-ghost',
                         animation: 150,
                         onEnd: (evt) => {
                             let items = Array.from($el.querySelectorAll('[data-id]')).map((el, index) => {
                                 return { value: el.getAttribute('data-id'), order: index + 1 };
                             });
                             $wire.updateLessonOrder(items);
                         }
                     })">
                    @forelse($module->lessons->sortBy('sort_order') as $lesson)
                        <div data-id="{{ $lesson->id }}" wire:key="lesson-{{ $lesson->id }}" class="flex items-center justify-between p-6 bg-surface-container-low rounded-2xl border border-outline-variant/10 group shadow-sm hover:border-primary/20 transition-all">
                            <div class="flex items-center gap-6">
                                <div class="drag-handle cursor-move text-stone-300 hover:text-primary transition-colors pr-2 border-r border-outline-variant/10">
                                    <span class="material-symbols-outlined text-[20px]">drag_indicator</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-headline text-lg font-bold text-on-surface italic leading-tight">{{ $lesson->title }}</span>
                                    <div class="flex items-center gap-4 mt-1">
                                        <div class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px] text-stone-400">schedule</span>
                                            <span class="text-[9px] font-bold uppercase tracking-widest text-stone-400">{{ $lesson->duration_minutes }}m</span>
                                        </div>
                                        @if($lesson->is_members_only)
                                            <span class="text-[9px] font-bold uppercase tracking-widest text-primary bg-primary/10 px-2 py-0.5 rounded flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[10px]">lock</span>
                                                Members
                                            </span>
                                        @endif
                                        @if(!$lesson->is_published)
                                            <span class="text-[9px] font-bold uppercase tracking-widest text-stone-400 border border-stone-200 px-2 py-0.5 rounded">Draft</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="openLessonModal({{ $lesson->id }})" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                                <button wire:confirm="Are you sure?" wire:click="deleteLesson({{ $lesson->id }})" class="p-2 text-error hover:bg-error/10 rounded-lg transition-all">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center border-2 border-dashed border-outline-variant/20 rounded-3xl opacity-40">
                            <p class="font-headline text-lg italic uppercase tracking-widest font-bold">No lectures assembled yet.</p>
                        </div>
                    @endforelse
                </div>
                @endif
            </section>

            <!-- Resource Manager -->
            <section class="bg-surface-container-lowest rounded-[32px] p-10 border border-outline-variant/10 shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-center mb-10 border-b border-outline-variant/20 pb-4">
                    <h4 class="font-headline text-xl text-on-surface italic font-bold flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">description</span>
                        Scholarly Resources
                    </h4>
                    @if($isEdit)
                    <button @click="$wire.openResourceModal()" class="bg-primary/10 text-primary px-4 py-2 rounded-xl font-bold uppercase tracking-widest text-[9px] flex items-center gap-2 hover:bg-primary/20 transition-all">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Attach PDF
                    </button>
                    @endif
                </div>

                @if(!$isEdit)
                <div class="py-20 text-center opacity-30 select-none">
                    <span class="material-symbols-outlined text-6xl mb-4">lock</span>
                    <p class="font-headline text-xl italic font-bold">Archives Locked</p>
                    <p class="text-[10px] uppercase tracking-widest mt-2">Create the module core first to attach documents.</p>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($module->resources as $res)
                        <div class="p-5 bg-surface-container-low rounded-2xl border border-outline-variant/10 group flex items-start gap-4">
                            <div class="w-10 h-10 bg-error/10 text-error rounded-xl flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                            </div>
                            <div class="flex-1">
                                <span class="font-headline text-sm font-bold text-on-surface italic block group-hover:text-primary transition-colors flex items-center gap-2">
                                    {{ $res->title }}
                                    @if($res->is_members_only)
                                        <span class="material-symbols-outlined text-[12px] text-primary">lock</span>
                                    @endif
                                </span>
                                <span class="text-[9px] text-stone-400 line-clamp-1 mt-1">{{ $res->short_description ?: 'Scholarly document file.' }}</span>
                            </div>
                            <div class="flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="openResourceModal({{ $res->id }})" class="p-1.5 text-primary hover:bg-primary/10 rounded-lg transition-all">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                </button>
                                <button wire:confirm="Remove resource?" wire:click="deleteResource({{ $res->id }})" class="p-1.5 text-error hover:bg-error/10 rounded-lg transition-all">
                                    <span class="material-symbols-outlined text-[14px]">delete</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 py-12 text-center border-2 border-dashed border-outline-variant/20 rounded-3xl opacity-40">
                            <p class="font-headline text-lg italic uppercase tracking-widest font-bold">The archives are empty.</p>
                        </div>
                    @endforelse
                </div>
                @endif
            </section>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-10">


            <!-- Media Visuals -->
            <section class="bg-surface-container-lowest rounded-[32px] p-8 border border-outline-variant/10 shadow-sm">
                <h4 class="text-[10px] font-bold uppercase tracking-widest text-on-surface mb-6 border-b border-outline-variant/20 pb-3">Curated Visuals</h4>
                
                <div class="space-y-8">
                    <div class="space-y-4">
                        <label class="text-[9px] font-bold uppercase tracking-[0.2em] text-stone-400 block px-1">Module Cover Image</label>
                        <div class="relative group aspect-[4/3] rounded-[24px] overflow-hidden border-2 border-dashed border-outline-variant/30 bg-surface-container-low flex flex-col items-center justify-center cursor-pointer hover:border-primary/50 transition-all">
                            @if ($cover_image && is_object($cover_image))
                                <img src="{{ $cover_image->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif ($isEdit && $module->cover_image)
                                <img src="{{ Storage::url($module->cover_image) }}" class="w-full h-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-4xl text-stone-300">add_photo_alternate</span>
                                <span class="text-[10px] font-bold uppercase tracking-tight text-stone-400 mt-2">Upload 1:1 or 4:3 Cover</span>
                            @endif
                            <input type="file" wire:model="cover_image" class="absolute inset-0 opacity-0 cursor-pointer">
                            <div wire:loading wire:target="cover_image" class="absolute inset-0 bg-white/80 flex items-center justify-center">
                                <span class="text-[9px] font-bold uppercase tracking-widest animate-pulse">Processing...</span>
                            </div>
                        </div>
                    </div>


                    <div class="space-y-4">
                        <label class="text-[9px] font-bold uppercase tracking-[0.2em] text-stone-400 block px-1">Classification Taxonomy</label>
                        <x-admin.tag-selector id="lms-editor-tag-selector" wire:model="tags" />
                        
                        <div class="pt-4 border-t border-outline-variant/10">
                            <label class="flex items-center gap-3 cursor-pointer group w-fit">
                                <div class="relative inline-flex items-center">
                                    <input wire:model="is_upsc_relevant" type="checkbox" class="sr-only peer">
                                    <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">UPSC Relevant</span>
                                    <span class="text-[8px] text-stone-400 uppercase tracking-tight italic">Flag for UPSC Hub</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Metadata Stats -->
            @if($isEdit)
            <section class="bg-surface-container-lowest/50 rounded-[32px] p-8 border border-outline-variant/10 flex flex-col gap-4 italic opacity-80">
                <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-stone-400">
                    <span>Initiated</span>
                    <span>{{ $module->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-stone-400">
                    <span>By Archivist</span>
                    <span>{{ $module->creator->name ?? 'System' }}</span>
                </div>
            </section>
            @endif
        </div>
    </div>

    <!-- Lesson Modal -->
    <div x-show="$wire.isLessonModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-6" x-cloak>
        <div style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(8px);" class="fixed inset-0" @click="$wire.isLessonModalOpen = false"></div>
        <div class="bg-surface-container-lowest rounded-[32px] shadow-2xl w-full max-w-2xl overflow-hidden relative z-10 flex flex-col max-h-[90vh] animate-in zoom-in-95 duration-200">
            <div class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/30">
                <h4 class="font-headline text-2xl text-on-surface italic font-bold">{{ $editingLessonId ? 'Modify Lecture' : 'New Film Lecture' }}</h4>
                <button @click="$wire.isLessonModalOpen = false" class="text-stone-400 hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-10 overflow-y-auto space-y-8">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Lecture Title</label>
                    <input wire:model="lesson_title" type="text" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl p-4 text-lg font-headline italic font-bold text-on-surface focus:ring-2 focus:ring-primary outline-none">
                </div>

                <div class="space-y-4 pt-4">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Video Source Authority</label>
                    <div class="grid grid-cols-2 gap-4">
                        <button wire:click="$set('lesson_video_source_type', 'url')" class="p-5 rounded-2xl border-2 transition-all flex flex-col items-center gap-2 {{ $lesson_video_source_type === 'url' ? 'border-primary bg-primary/5' : 'border-outline-variant/20 hover:border-primary/30' }}">
                            <span class="material-symbols-outlined text-primary">link</span>
                            <span class="text-[9px] font-bold uppercase tracking-widest">External Embed URL</span>
                        </button>
                        <button wire:click="$set('lesson_video_source_type', 'upload')" class="p-5 rounded-2xl border-2 transition-all flex flex-col items-center gap-2 {{ $lesson_video_source_type === 'upload' ? 'border-primary bg-primary/5' : 'border-outline-variant/20 hover:border-primary/30' }}">
                            <span class="material-symbols-outlined text-primary">cloud_upload</span>
                            <span class="text-[9px] font-bold uppercase tracking-widest">Direct Local Upload</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 py-6 border-y border-outline-variant/10">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[12px]">schedule</span>
                            Duration (min)
                        </label>
                        <div class="relative">
                            <input wire:model="lesson_duration_minutes" type="number" 
                                {{ $lesson_video_source_type === 'upload' ? 'readonly' : '' }}
                                class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-primary outline-none {{ $lesson_video_source_type === 'upload' ? 'opacity-60 cursor-not-allowed' : '' }}">
                            @if($lesson_video_source_type === 'upload')
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-stone-400 text-sm" title="Auto-calculated from file">bolt</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col justify-end">
                        <label class="flex items-center gap-3 cursor-pointer group p-4 bg-surface-container-low rounded-2xl border border-outline-variant/10 hover:border-primary/30 transition-all h-[54px]">
                            <div class="relative inline-flex items-center">
                                <input wire:model="lesson_is_members_only" type="checkbox" class="sr-only peer">
                                <div class="w-9 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-on-surface uppercase tracking-widest leading-none">Members Only</span>
                                <span class="text-[7px] text-stone-400 italic">Restricted access</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="space-y-4">
                    @if($lesson_video_source_type === 'url')
                        <input wire:model="lesson_video_url" type="text" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl p-4 text-xs font-medium focus:ring-2 focus:ring-primary outline-none mt-2" placeholder="Paste YouTube, Vimeo or streaming URL...">
                    @else
                        <div x-data="{ isUploading: false, progress: 0, uploadComplete: false }"
                             x-on:livewire-upload-start="isUploading = true; uploadComplete = false; progress = 0"
                             x-on:livewire-upload-finish="isUploading = false; uploadComplete = true"
                             x-on:livewire-upload-error="isUploading = false"
                             x-on:livewire-upload-progress="progress = $event.detail.progress"
                             class="relative group p-8 rounded-2xl border-2 border-dashed border-outline-variant/30 bg-surface-container-low flex flex-col items-center justify-center cursor-pointer overflow-hidden transition-all hover:border-primary/50">
                            
                            <!-- Progress Overlay -->
                            <div x-show="isUploading" class="absolute inset-0 bg-surface-container-lowest/90 backdrop-blur-[2px] z-20 flex flex-col items-center justify-center p-10 animate-in fade-in duration-300" x-cloak>
                                <div class="w-full max-w-[200px] h-1.5 bg-stone-100 rounded-full overflow-hidden relative border border-outline-variant/10">
                                    <div class="absolute inset-y-0 left-0 bg-primary transition-all duration-300 ease-out shadow-[0_0_10px_rgba(var(--md-sys-color-primary),0.5)]" :style="`width: ${progress}%`"></div>
                                </div>
                                <div class="flex flex-col items-center mt-4 gap-1">
                                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary" x-text="`${progress}% Digitized`"></span>
                                    <span class="text-[8px] text-stone-400 italic">Preserving film to archives...</span>
                                </div>
                            </div>

                            <!-- Success Confirmation Overlay -->
                            <div x-show="uploadComplete" x-transition class="absolute inset-0 bg-primary/5 backdrop-blur-[1px] z-20 flex flex-col items-center justify-center p-10" x-cloak>
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-lg shadow-primary/20 animate-in zoom-in-50 duration-300">
                                        <span class="material-symbols-outlined text-xl">check</span>
                                    </div>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-primary mt-2">Archival Success</span>
                                    <span class="text-[8px] text-stone-500 italic">Video record stabilized</span>
                                </div>
                                <button @click="uploadComplete = false" class="mt-4 text-[8px] font-bold uppercase tracking-widest text-stone-400 hover:text-primary transition-colors">Replace Film</button>
                            </div>

                            <span x-show="!isUploading && !uploadComplete" class="material-symbols-outlined text-2xl text-stone-300">movie</span>
                            <span x-show="!isUploading && !uploadComplete" class="text-[10px] font-bold uppercase tracking-tight text-stone-400 mt-1">Upload MP4 Video (Max 500MB)</span>
                            
                            <input type="file" 
                                   wire:model="lesson_video_path" 
                                   class="absolute inset-0 opacity-0 cursor-pointer" 
                                   x-bind:disabled="isUploading"
                                   wire:target="lesson_video_path">

                            @if($lesson_video_path) 
                                <div class="flex items-center gap-2 mt-2 px-3 py-1 bg-primary/10 rounded-full border border-primary/20 animate-in slide-in-from-bottom-1 shadow-sm">
                                    <span class="material-symbols-outlined text-[12px] text-primary">description</span>
                                    <span class="text-[9px] text-primary font-bold italic">{{ $lesson_video_path->getClientOriginalName() }}</span>
                                </div>
                            @endif
                            @error('lesson_video_path') <span class="text-[10px] text-error font-medium mt-2">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-4 pt-6 border-t border-outline-variant/10">
                    <button @click="$wire.isLessonModalOpen = false" class="px-8 py-3 text-[10px] font-bold uppercase tracking-widest text-stone-500 hover:bg-stone-100 rounded-xl transition-all">Cancel</button>
                    <button wire:click="saveLesson" 
                            wire:loading.attr="disabled" 
                            wire:target="lesson_video_path"
                            class="bg-primary text-on-primary px-8 py-3 rounded-xl font-bold text-[10px] uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                        <span wire:loading.remove wire:target="lesson_video_path">Identify Record</span>
                        <span wire:loading wire:target="lesson_video_path">Archiving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Resource Modal -->
    <div x-show="$wire.isResourceModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-6" x-cloak>
        <div style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(8px);" class="fixed inset-0" @click="$wire.isResourceModalOpen = false"></div>
        <div class="bg-surface-container-lowest rounded-[32px] shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto relative z-10 flex flex-col animate-in zoom-in-95 duration-200">
            <div class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/30">
                <h4 class="font-headline text-2xl text-on-surface italic font-bold">Attach Archive PDF</h4>
                <button @click="$wire.isResourceModalOpen = false" class="text-stone-400 hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-10 space-y-8">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Document Title</label>
                    <input wire:model="resource_title" type="text" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-primary outline-none">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Abstract</label>
                    <textarea wire:model="resource_short_description" rows="2" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl p-4 text-xs font-medium focus:ring-2 focus:ring-primary outline-none resize-none"></textarea>
                </div>

                <label class="flex items-center gap-3 cursor-pointer group p-4 bg-surface-container-low rounded-2xl border border-outline-variant/10 self-start">
                    <div class="relative inline-flex items-center">
                        <input wire:model="resource_is_members_only" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-bold text-on-surface uppercase tracking-widest">Members Only Access</span>
                        <span class="text-[8px] text-stone-400 italic">Restrict document to subscribers</span>
                    </div>
                </label>

                <div class="relative group p-10 rounded-2xl border-2 border-dashed border-outline-variant/30 bg-surface-container-low flex flex-col items-center justify-center cursor-pointer hover:border-primary/50 transition-all">
                    <span class="material-symbols-outlined text-3xl text-error/30">picture_as_pdf</span>
                    <span class="text-[10px] font-bold uppercase tracking-tight text-stone-400 mt-2">Identify PDF Archive</span>
                    <input type="file" wire:model="resource_file_path" class="absolute inset-0 opacity-0 cursor-pointer">
                    @if($resource_file_path) <span class="text-[9px] text-primary font-bold mt-2 italic">{{ $resource_file_path->getClientOriginalName() }}</span> @endif
                    @error('resource_file_path') <span class="text-[10px] text-error font-medium mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-4 pt-6 border-t border-outline-variant/10">
                    <button @click="$wire.isResourceModalOpen = false" class="px-8 py-3 text-[10px] font-bold uppercase tracking-widest text-stone-500 hover:bg-stone-100 rounded-xl transition-all">Cancel</button>
                    <button wire:click="saveResource" class="bg-primary text-on-primary px-8 py-3 rounded-xl font-bold text-[10px] uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 transition-all">Deposit Record</button>
                </div>
            </div>
        </div>
    </div>
</div>
