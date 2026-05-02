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
            <a href="{{ route('admin.lms.modules.index') }}" class="bg-surface-container-high text-on-surface px-8 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-surface-container-highest transition-all">
                Exit Editor
            </a>
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

    <!-- Module Stats Row -->
    <section class="bg-surface-container-lowest rounded-[2rem] p-6 mb-10 border border-outline-variant/10 shadow-sm flex flex-wrap items-center justify-between gap-8 animate-in fade-in slide-in-from-top-4 duration-500">
        <div class="flex items-center gap-8">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-widest text-stone-400 mb-1">Total Classes</span>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-stone-300 text-sm">folder_open</span>
                    <span class="font-headline text-xl font-bold italic text-on-surface leading-none">{{ $module->classes_count ?? $module->classes->count() }}</span>
                </div>
            </div>
            <div class="w-px h-8 bg-outline-variant/20"></div>
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-widest text-stone-400 mb-1">Lectures</span>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-stone-300 text-sm">play_circle</span>
                    <span class="font-headline text-xl font-bold italic text-on-surface leading-none">{{ $module->lessons_count ?? $module->lessons->count() }}</span>
                </div>
            </div>
            <div class="w-px h-8 bg-outline-variant/20"></div>
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-widest text-stone-400 mb-1">Resources</span>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-stone-300 text-sm">description</span>
                    <span class="font-headline text-xl font-bold italic text-on-surface leading-none">{{ $module->resources_count ?? $module->resources->count() }}</span>
                </div>
            </div>
            <div class="w-px h-8 bg-outline-variant/20"></div>
            <div class="flex flex-col">
                <span class="text-[9px] font-bold uppercase tracking-widest text-stone-400 mb-1">Assessments</span>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-stone-300 text-sm">quiz</span>
                    <span class="font-headline text-xl font-bold italic text-on-surface leading-none">{{ $module->mcq_questions_count ?? $module->classes->sum('mcq_questions_count') }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3 bg-surface-container-low px-4 py-2 rounded-xl border border-outline-variant/10">
                <div class="w-2 h-2 rounded-full {{ $module->is_published ? 'bg-primary' : 'bg-stone-300' }}"></div>
                <span class="text-[10px] font-bold uppercase tracking-widest {{ $module->is_published ? 'text-primary' : 'text-stone-400' }}">
                    {{ $module->is_published ? 'Published' : 'Draft Mode' }}
                </span>
            </div>
            <div class="flex flex-col items-end">
                <span class="text-[8px] font-bold uppercase tracking-widest text-stone-400">Last Modified</span>
                <span class="text-[10px] font-bold text-on-surface italic">{{ $module->updated_at->format('M d, H:i') }}</span>
            </div>
        </div>
    </section>

    <div class="w-full">
        <!-- Main Content -->
        <div>
            @if(!$selectedClassId)
                <!-- Module Root: Class Explorer -->
                <section class="bg-surface-container-lowest rounded-[40px] p-10 border border-outline-variant/10 shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-center mb-10 border-b border-outline-variant/20 pb-6">
                        <div>
                            <h4 class="font-headline text-2xl text-on-surface italic font-bold flex items-center gap-3">
                                <span class="material-symbols-outlined text-secondary">folder_special</span>
                                Class Folders
                            </h4>
                            <p class="text-[9px] font-bold uppercase tracking-widest text-stone-400 mt-1">Organize module content into structured learning folders.</p>
                        </div>
                        @if($isEdit)
                        <button wire:click="openClassModal" class="bg-secondary text-on-secondary px-6 py-3 rounded-2xl font-bold uppercase tracking-widest text-[9px] flex items-center gap-2 hover:-translate-y-0.5 transition-all shadow-lg shadow-secondary/20">
                            <span class="material-symbols-outlined text-sm">create_new_folder</span>
                            New Class
                        </button>
                        @endif
                    </div>

                    @if(!$isEdit)
                        <div class="py-20 text-center opacity-30 select-none">
                            <span class="material-symbols-outlined text-6xl mb-4">lock</span>
                            <p class="font-headline text-2xl italic font-bold">Explorer Locked</p>
                            <p class="text-[10px] uppercase tracking-widest mt-2">Initialize module to enable folder structure.</p>
                        </div>
                    @else
                        @php
                            $legacyLessonsCount = $module->lessons->whereNull('lms_module_class_id')->count();
                            $legacyResourcesCount = $module->resources->whereNull('lms_module_class_id')->count();
                        @endphp

                        @if($legacyLessonsCount > 0 || $legacyResourcesCount > 0)
                            <div class="mb-10 p-8 bg-amber-50 border border-amber-200 rounded-[2.5rem] flex items-center justify-between gap-6 animate-in fade-in slide-in-from-top-4 duration-700">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-200 text-amber-700 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined">warning</span>
                                    </div>
                                    <div>
                                        <h5 class="font-headline text-lg font-bold italic text-amber-900">Legacy Content Detected</h5>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-amber-700/70 mt-1">
                                            This module has {{ $legacyLessonsCount }} videos and {{ $legacyResourcesCount }} PDFs saved at root level. 
                                            The new LMS structure requires content inside classes.
                                        </p>
                                    </div>
                                </div>
                                <button wire:click="moveLegacy" class="bg-amber-700 text-white px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-[9px] hover:bg-amber-800 transition-all shadow-lg shadow-amber-900/20 whitespace-nowrap">
                                    Migrate to Intro Class
                                </button>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" 
                             x-data 
                             x-init="new Sortable($el, { 
                                 handle: '.drag-handle', 
                                 ghostClass: 'sortable-ghost',
                                 animation: 150,
                                 onEnd: (evt) => {
                                     let items = Array.from($el.querySelectorAll('[data-id]')).map((el, index) => {
                                         return { value: el.getAttribute('data-id'), order: index + 1 };
                                     });
                                     $wire.updateClassOrder(items);
                                 }
                             })">
                            @forelse($module->classes as $class)
                                @include('admin.lms.modules.partials.class-folder-card', ['class' => $class])
                            @empty
                                <div class="md:col-span-2 lg:col-span-3 xl:col-span-4">
                                    @include('admin.lms.modules.partials.class-empty-state')
                                </div>
                            @endforelse
                        </div>
                    @endif
                </section>
            @else
                <!-- Open Class: Content Manager -->
                <div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    @include('admin.lms.modules.partials.class-breadcrumb')

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        <!-- Lesson Manager -->
                        <section class="bg-surface-container-lowest rounded-[32px] p-8 border border-outline-variant/10 shadow-sm relative overflow-hidden h-fit">
                            <div class="flex justify-between items-center mb-8 border-b border-outline-variant/20 pb-4">
                                <h4 class="font-headline text-lg text-on-surface italic font-bold flex items-center gap-3">
                                    <span class="material-symbols-outlined text-secondary">play_circle</span>
                                    Film & Video Lectures
                                </h4>
                                <button @click="$wire.openLessonModal()" class="bg-secondary/10 text-secondary px-3 py-1.5 rounded-xl font-bold uppercase tracking-widest text-[8px] flex items-center gap-2 hover:bg-secondary/20 transition-all">
                                    <span class="material-symbols-outlined text-xs">add</span>
                                    Add Video
                                </button>
                            </div>

                            <div class="space-y-3" 
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
                                @forelse($module->lessons->where('lms_module_class_id', $selectedClassId)->sortBy('sort_order') as $lesson)
                                    <div data-id="{{ $lesson->id }}" wire:key="lesson-{{ $lesson->id }}" class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl border border-outline-variant/10 group shadow-sm hover:border-primary/20 transition-all">
                                        <div class="flex items-center gap-4 min-w-0">
                                            <div class="drag-handle cursor-move text-stone-300 hover:text-primary transition-colors pr-2 border-r border-outline-variant/10 shrink-0">
                                                <span class="material-symbols-outlined text-[18px]">drag_indicator</span>
                                            </div>
                                            <div class="flex flex-col min-w-0">
                                                <span class="font-headline text-sm font-bold text-on-surface italic leading-tight truncate">{{ $lesson->title }}</span>
                                                <div class="flex items-center gap-3 mt-1">
                                                    <span class="text-[8px] font-bold uppercase tracking-widest text-stone-400 flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[10px]">schedule</span>
                                                        {{ $lesson->duration_minutes }}m
                                                    </span>
                                                    @if($lesson->is_members_only)
                                                        <span class="text-[7px] font-bold uppercase tracking-widest text-primary bg-primary/10 px-1.5 py-0.5 rounded">Lock</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                                            <button wire:click="openLessonModal({{ $lesson->id }})" class="p-1.5 text-primary hover:bg-primary/10 rounded-lg transition-all">
                                                <span class="material-symbols-outlined text-xs">edit</span>
                                            </button>
                                            <button wire:confirm="Are you sure?" wire:click="deleteLesson({{ $lesson->id }})" class="p-1.5 text-error hover:bg-error/10 rounded-lg transition-all">
                                                <span class="material-symbols-outlined text-xs">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-12 text-center border-2 border-dashed border-outline-variant/20 rounded-2xl opacity-40">
                                        <p class="text-[10px] uppercase tracking-widest font-bold">No lectures yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </section>

                        <!-- Resource Manager -->
                        <section class="bg-surface-container-lowest rounded-[32px] p-8 border border-outline-variant/10 shadow-sm relative overflow-hidden h-fit">
                            <div class="flex justify-between items-center mb-8 border-b border-outline-variant/20 pb-4">
                                <h4 class="font-headline text-lg text-on-surface italic font-bold flex items-center gap-3">
                                    <span class="material-symbols-outlined text-primary">description</span>
                                    Scholarly Resources
                                </h4>
                                <button @click="$wire.openResourceModal()" class="bg-primary/10 text-primary px-3 py-1.5 rounded-xl font-bold uppercase tracking-widest text-[8px] flex items-center gap-2 hover:bg-primary/20 transition-all">
                                    <span class="material-symbols-outlined text-xs">add</span>
                                    Add PDF
                                </button>
                            </div>

                            <div class="space-y-3">
                                @forelse($module->resources->where('lms_module_class_id', $selectedClassId) as $res)
                                    <div class="p-4 bg-surface-container-low rounded-xl border border-outline-variant/10 group flex items-start gap-4 shadow-sm hover:border-primary/20 transition-all">
                                        <div class="w-8 h-8 bg-error/10 text-error rounded-lg flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="font-headline text-sm font-bold text-on-surface italic block group-hover:text-primary transition-colors truncate">
                                                {{ $res->title }}
                                            </span>
                                            <span class="text-[8px] text-stone-400 mt-0.5 block truncate">{{ $res->short_description ?: 'Scholarly document file.' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                                            <button wire:click="openResourceModal({{ $res->id }})" class="p-1.5 text-primary hover:bg-primary/10 rounded-lg transition-all">
                                                <span class="material-symbols-outlined text-[12px]">edit</span>
                                            </button>
                                            <button wire:confirm="Remove resource?" wire:click="deleteResource({{ $res->id }})" class="p-1.5 text-error hover:bg-error/10 rounded-lg transition-all">
                                                <span class="material-symbols-outlined text-[12px]">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-12 text-center border-2 border-dashed border-outline-variant/20 rounded-2xl opacity-40">
                                        <p class="text-[10px] uppercase tracking-widest font-bold">No resources yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    </div>

                    <!-- MCQ Manager -->
                    <section class="bg-surface-container-lowest rounded-[32px] p-10 border border-outline-variant/10 shadow-sm relative overflow-hidden">
                        <div class="flex justify-between items-center mb-8 border-b border-outline-variant/20 pb-6">
                            <div>
                                <h4 class="font-headline text-2xl text-on-surface italic font-bold flex items-center gap-3">
                                    <span class="material-symbols-outlined text-orange-600">quiz</span>
                                    Class MCQ Assessment
                                </h4>
                                <p class="text-[9px] font-bold uppercase tracking-widest text-stone-400 mt-1">Create multiple-choice checks for this class using the same assessment standards as the Exams module.</p>
                            </div>
                        </div>

                        <livewire:admin.lms.module-classes.class-mcq-manager 
                            :module="$module" 
                            :class="$module->classes->find($selectedClassId)" 
                            :key="'mcq-manager-'.$selectedClassId" />
                    </section>
                </div>
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

                <div x-data="{ isUploading: false, progress: 0, uploadComplete: false }"
                     x-on:livewire-upload-start="isUploading = true; uploadComplete = false; progress = 0"
                     x-on:livewire-upload-finish="isUploading = false; uploadComplete = true"
                     x-on:livewire-upload-error="isUploading = false"
                     x-on:livewire-upload-progress="progress = $event.detail.progress"
                     class="relative group p-10 rounded-2xl border-2 border-dashed border-outline-variant/30 bg-surface-container-low flex flex-col items-center justify-center cursor-pointer overflow-hidden transition-all hover:border-primary/50">
                    
                    <!-- Progress Overlay -->
                    <div x-show="isUploading" class="absolute inset-0 bg-surface-container-lowest/90 backdrop-blur-[2px] z-20 flex flex-col items-center justify-center p-10 animate-in fade-in duration-300" x-cloak>
                        <div class="w-full max-w-[150px] h-1 bg-stone-100 rounded-full overflow-hidden relative border border-outline-variant/10">
                            <div class="absolute inset-y-0 left-0 bg-primary transition-all duration-300 ease-out" :style="`width: ${progress}%`"></div>
                        </div>
                        <div class="flex flex-col items-center mt-3 gap-1">
                            <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-primary" x-text="`${progress}% Uploaded`"></span>
                        </div>
                    </div>

                    <span x-show="!isUploading && !uploadComplete" class="material-symbols-outlined text-3xl text-error/30">picture_as_pdf</span>
                    <span x-show="!isUploading && !uploadComplete" class="text-[10px] font-bold uppercase tracking-tight text-stone-400 mt-2">Identify PDF Archive</span>
                    
                    <div x-show="uploadComplete" class="flex flex-col items-center gap-1 animate-in zoom-in-95 duration-200">
                        <div class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-md">
                            <span class="material-symbols-outlined text-sm">check</span>
                        </div>
                        <span class="text-[9px] font-bold uppercase tracking-widest text-primary mt-1">Archived</span>
                    </div>

                    <input type="file" wire:model="resource_file_path" class="absolute inset-0 opacity-0 cursor-pointer" x-bind:disabled="isUploading">
                    
                    @if($resource_file_path) 
                        <div class="flex items-center gap-2 mt-3 px-3 py-1 bg-error/5 rounded-full border border-error/10 animate-in slide-in-from-bottom-1">
                            <span class="material-symbols-outlined text-[10px] text-error">description</span>
                            <span class="text-[8px] text-error font-bold italic">{{ $resource_file_path->getClientOriginalName() }}</span>
                        </div>
                    @endif
                    @error('resource_file_path') <span class="text-[10px] text-error font-medium mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-4 pt-6 border-t border-outline-variant/10">
                    <button @click="$wire.isResourceModalOpen = false" class="px-8 py-3 text-[10px] font-bold uppercase tracking-widest text-stone-500 hover:bg-stone-100 rounded-xl transition-all">Cancel</button>
                    <button wire:click="saveResource" 
                            wire:loading.attr="disabled"
                            wire:target="resource_file_path"
                            class="bg-primary text-on-primary px-8 py-3 rounded-xl font-bold text-[10px] uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="resource_file_path">Deposit Record</span>
                        <span wire:loading wire:target="resource_file_path">Archiving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Class Modal -->
    <div x-show="$wire.isClassModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-6" x-cloak>
        <div style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(8px);" class="fixed inset-0" @click="$wire.isClassModalOpen = false"></div>
        <div class="bg-surface-container-lowest rounded-[32px] shadow-2xl w-full max-w-lg overflow-hidden relative z-10 flex flex-col animate-in zoom-in-95 duration-200">
            <div class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/30">
                <h4 class="font-headline text-2xl text-on-surface italic font-bold">Manage Class Folder</h4>
                <button @click="$wire.isClassModalOpen = false" class="text-stone-400 hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="p-10 space-y-8">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Class Title</label>
                    <input wire:model="class_title" type="text" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-primary outline-none" placeholder="e.g. Introduction to Ritual">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Description</label>
                    <textarea wire:model="class_description" rows="3" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl p-4 text-xs font-medium focus:ring-2 focus:ring-primary outline-none resize-none" placeholder="What will students learn in this class?"></textarea>
                </div>

                <label class="flex items-center gap-3 cursor-pointer group w-fit">
                    <div class="relative inline-flex items-center">
                        <input wire:model="class_is_published" type="checkbox" class="sr-only peer">
                        <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Published</span>
                        <span class="text-[8px] text-stone-400 uppercase tracking-tight italic">Visible to students</span>
                    </div>
                </label>

                <div class="flex justify-end gap-4 pt-6 border-t border-outline-variant/10">
                    <button @click="$wire.isClassModalOpen = false" class="px-8 py-3 text-[10px] font-bold uppercase tracking-widest text-stone-500 hover:bg-stone-100 rounded-xl transition-all">Cancel</button>
                    <button wire:click="saveClass" class="bg-primary text-on-primary px-8 py-3 rounded-xl font-bold text-[10px] uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 transition-all">Save Folder</button>
                </div>
            </div>
        </div>
    </div>
</div>
