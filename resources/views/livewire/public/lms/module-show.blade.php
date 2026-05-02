<div class="min-h-screen bg-stone-50 pb-32">
    <!-- Module Hero -->
    <header class="relative bg-stone-900 pt-16 md:pt-24 pb-32 md:pb-48 overflow-hidden">
        <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
        <div class="max-w-7xl mx-auto px-4 md:px-6 relative z-10">
            <nav class="flex items-center gap-2 text-[10px] uppercase font-bold tracking-[0.2em] text-stone-500 mb-8 md:mb-12 overflow-x-auto no-scrollbar whitespace-nowrap">
                <a href="{{ route('modules.index') }}" class="hover:text-primary transition-colors">Catalog</a>
                <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                <span class="text-stone-300">{{ $module->topic->name ?? 'General' }}</span>
            </nav>

            <div class="grid lg:grid-cols-12 gap-8 md:gap-16 items-center">
                <div class="lg:col-span-12">
                    <div class="flex items-center gap-3 md:gap-4 mb-4 md:mb-6">
                        <span class="bg-primary px-3 py-1 rounded-full text-[9px] font-bold text-white uppercase tracking-widest">{{ $module->level ?? 'Academic' }}</span>
                        <span class="text-stone-400 text-xs font-medium">{{ $module->lessons_count }} Lessons • {{ $module->formatted_duration }}</span>
                    </div>
                    <h1 class="font-headline text-3xl md:text-5xl lg:text-7xl text-white italic font-bold mb-6 md:mb-8 leading-tight">{{ $module->title }}</h1>
                    <p class="text-stone-300 text-base md:text-xl max-w-3xl font-light leading-relaxed mb-8 md:mb-10">
                        {{ $module->short_description }}
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        @if($progress['percentage'] > 0)
                            <button wire:click="continueJourney" class="w-full sm:w-auto bg-primary text-white px-8 md:px-10 py-4 md:py-5 rounded-2xl font-bold uppercase tracking-widest text-[10px] shadow-2xl shadow-primary/30 hover:-translate-y-1 transition-all flex items-center justify-center gap-3 group">
                                Continue Journey
                                <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </button>
                        @else
                            @php 
                                $firstLesson = $lessons->first();
                                $firstLessonSlug = $firstLesson ? $firstLesson->slug : '';
                            @endphp
                            @if($firstLesson)
                                <button wire:click="openLesson('{{ $firstLessonSlug }}')" class="w-full sm:w-auto bg-primary text-white px-8 md:px-10 py-4 md:py-5 rounded-2xl font-bold uppercase tracking-widest text-[10px] shadow-2xl shadow-primary/30 hover:-translate-y-1 transition-all flex items-center justify-center gap-3 group">
                                    Start Exploration
                                    <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">play_circle</span>
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Content Layout -->
    <div class="max-w-7xl mx-auto px-4 md:px-6 -mt-20 md:-mt-32 relative z-20">
        <div class="grid lg:grid-cols-12 gap-8 md:gap-10">
            <!-- Main Content Area -->
            <div class="lg:col-span-8 space-y-8 md:space-y-10">
                <!-- Overview Section -->
                <section class="bg-white rounded-[24px] md:rounded-[32px] p-6 md:p-12 border border-stone-200 shadow-sm">
                    <h2 class="font-headline text-2xl md:text-3xl font-bold italic text-stone-900 mb-6 md:mb-8 border-b border-stone-100 pb-6 uppercase tracking-tight">Curriculum Briefing</h2>
                    <div class="prose prose-stone max-w-none text-stone-600 leading-relaxed italic text-base md:text-lg">
                        {!! nl2br(e($module->overview)) !!}
                    </div>
                </section>

                <!-- Curriculum Section -->
                <section id="curriculum-root" class="scroll-mt-32">
                    @if(!$selectedClassId)
                        <!-- Folder Explorer State -->
                        <div class="space-y-12">
                            <div class="flex items-center justify-between border-b border-stone-200 pb-6">
                                <h2 class="font-headline text-3xl font-bold italic text-stone-900 uppercase tracking-tight">Module Curricula</h2>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-stone-400">{{ $classes->count() }} Scholarly Units</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @php
                                    $legacyLessons = $lessons->whereNull('lms_module_class_id');
                                    $legacyResources = $resources->whereNull('lms_module_class_id');
                                @endphp

                                @if($legacyLessons->isNotEmpty() || $legacyResources->isNotEmpty())
                                    <div wire:click="selectClass('legacy')" class="group p-8 rounded-[32px] bg-stone-100 border border-stone-200 hover:bg-white hover:border-primary/30 hover:shadow-2xl hover:-translate-y-1 transition-all duration-500 cursor-pointer flex flex-col justify-between h-full">
                                        <div>
                                            <div class="w-14 h-14 rounded-2xl bg-stone-200 text-stone-500 flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-colors">
                                                <span class="material-symbols-outlined text-2xl">folder_zip</span>
                                            </div>
                                            <h3 class="font-headline text-2xl font-bold italic text-stone-900 mb-2">General Overview</h3>
                                            <p class="text-xs text-stone-500 italic leading-relaxed">Fundamental introductory materials and unclassified resources for this unit.</p>
                                        </div>
                                        <div class="mt-8 flex items-center justify-between">
                                            <div class="flex gap-4">
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] font-bold text-stone-900 italic">{{ $legacyLessons->count() }}</span>
                                                    <span class="text-[8px] uppercase tracking-widest text-stone-400 font-bold">Lectures</span>
                                                </div>
                                                <div class="flex flex-col border-l border-stone-200 pl-4">
                                                    <span class="text-[10px] font-bold text-stone-900 italic">{{ $legacyResources->count() }}</span>
                                                    <span class="text-[8px] uppercase tracking-widest text-stone-400 font-bold">Files</span>
                                                </div>
                                            </div>
                                            <div class="w-10 h-10 rounded-full bg-white border border-stone-200 flex items-center justify-center group-hover:border-primary group-hover:text-primary transition-all">
                                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @foreach($classes as $class)
                                    <div wire:click="selectClass({{ $class->id }})" class="group p-8 rounded-[32px] bg-white border border-stone-100 hover:border-primary/30 hover:shadow-2xl hover:-translate-y-1 transition-all duration-500 cursor-pointer flex flex-col justify-between h-full">
                                        <div>
                                            <div class="w-14 h-14 rounded-2xl bg-orange-50 text-primary flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-colors">
                                                <span class="material-symbols-outlined text-2xl">folder_open</span>
                                            </div>
                                            <h3 class="font-headline text-2xl font-bold italic text-stone-900 mb-2 leading-tight">{{ $class->title }}</h3>
                                            <p class="text-xs text-stone-500 italic leading-relaxed line-clamp-2">{{ $class->description ?: 'Scholarly unit folder containing curated lectures and archives.' }}</p>
                                        </div>
                                        <div class="mt-8 flex items-center justify-between">
                                            <div class="flex gap-4">
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] font-bold text-stone-900 italic">{{ $class->lessons->count() }}</span>
                                                    <span class="text-[8px] uppercase tracking-widest text-stone-400 font-bold">Lectures</span>
                                                </div>
                                                <div class="flex flex-col border-l border-stone-200 pl-4">
                                                    <span class="text-[10px] font-bold text-stone-900 italic">{{ $class->resources->count() }}</span>
                                                    <span class="text-[8px] uppercase tracking-widest text-stone-400 font-bold">Archives</span>
                                                </div>
                                            </div>
                                            <div class="w-10 h-10 rounded-full bg-stone-50 border border-stone-100 flex items-center justify-center group-hover:border-primary group-hover:bg-primary group-hover:text-white transition-all shadow-sm">
                                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- Detailed Content State -->
                        <div class="space-y-10 animate-in fade-in slide-in-from-right-8 duration-500">
                            <!-- Breadcrumb Navigation -->
                            <button wire:click="resetNavigation" class="flex items-center gap-2 text-[10px] uppercase font-bold tracking-[0.2em] text-stone-400 hover:text-primary transition-colors group">
                                <span class="material-symbols-outlined text-sm">arrow_back</span>
                                Back to All Folders
                            </button>

                            @php
                                if($selectedClassId === 'legacy') {
                                    $currentTitle = "General Overview";
                                    $currentDesc = "Introductory concepts and unassigned resources.";
                                    $currentLessons = $lessons->whereNull('lms_module_class_id');
                                    $currentResources = $resources->whereNull('lms_module_class_id');
                                } else {
                                    $currentClass = $classes->find($selectedClassId);
                                    $currentTitle = $currentClass->title;
                                    $currentDesc = $currentClass->description;
                                    $currentLessons = $currentClass->lessons;
                                    $currentResources = $currentClass->resources;
                                }
                            @endphp

                            <div class="bg-white rounded-[32px] p-8 md:p-12 border border-stone-200 shadow-sm overflow-hidden relative">
                                <div class="absolute top-0 right-0 p-8 opacity-5">
                                    <span class="material-symbols-outlined text-8xl">school</span>
                                </div>
                                
                                <div class="relative z-10 mb-12 border-b border-stone-100 pb-8">
                                    <h2 class="font-headline text-3xl md:text-5xl font-bold italic text-stone-900 mb-4">{{ $currentTitle }}</h2>
                                    <p class="text-stone-500 text-base md:text-xl font-light italic max-w-2xl leading-relaxed">{{ $currentDesc }}</p>
                                </div>

                                <div class="space-y-12">
                                    <div class="space-y-6">
                                        <h4 class="text-[10px] font-bold uppercase tracking-[0.3em] text-primary flex items-center gap-3">
                                            <span class="w-8 h-px bg-primary/20"></span>
                                            Archival Lectures
                                        </h4>
                                        <div class="space-y-4">
                                            @foreach($currentLessons as $index => $lesson)
                                                @include('livewire.public.lms.partials.lesson-row', ['lesson' => $lesson, 'index' => $index])
                                            @endforeach
                                        </div>
                                    </div>

                                    @if($currentResources->isNotEmpty())
                                        <div class="space-y-8">
                                            <h4 class="text-[10px] font-bold uppercase tracking-[0.3em] text-primary flex items-center gap-3">
                                                <span class="w-8 h-px bg-primary/20"></span>
                                                Unit Reading Materials
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @foreach($currentResources as $resource)
                                                    @include('livewire.public.lms.partials.resource-card', ['resource' => $resource])
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if($selectedClassId !== 'legacy' && ($assessment = $currentClass->assessment) && $assessment->is_published)
                                        <div class="space-y-8">
                                            <h4 class="text-[10px] font-bold uppercase tracking-[0.3em] text-primary flex items-center gap-3">
                                                <span class="w-8 h-px bg-primary/20"></span>
                                                Scholarly Assessment
                                            </h4>
                                            
                                            <div class="bg-stone-900 rounded-[32px] p-8 md:p-12 text-white relative overflow-hidden shadow-2xl group">
                                                <div class="absolute inset-0 opacity-10 ethno-pattern scale-150 group-hover:scale-125 transition-transform duration-1000"></div>
                                                
                                                <div class="relative z-10 grid md:grid-cols-12 gap-8 items-center">
                                                    <div class="md:col-span-8 space-y-6">
                                                        <div class="flex items-center gap-4">
                                                            <div class="w-14 h-14 rounded-2xl bg-primary/20 text-primary flex items-center justify-center shadow-inner">
                                                                <span class="material-symbols-outlined text-3xl">psychology</span>
                                                            </div>
                                                            <div>
                                                                <h3 class="font-headline text-2xl md:text-4xl font-bold italic">{{ $assessment->title }}</h3>
                                                                <div class="flex items-center gap-4 mt-2">
                                                                    <span class="text-[9px] uppercase font-bold tracking-widest text-stone-400 flex items-center gap-1">
                                                                        <span class="material-symbols-outlined text-xs">timer</span>
                                                                        {{ $assessment->duration_minutes ?: 'Untimed' }} Minutes
                                                                    </span>
                                                                    <span class="text-[9px] uppercase font-bold tracking-widest text-stone-400 flex items-center gap-1">
                                                                        <span class="material-symbols-outlined text-xs">grade</span>
                                                                        {{ $assessment->total_marks }} Marks Total
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <p class="text-stone-300 text-sm md:text-base italic leading-relaxed font-light">
                                                            {{ $assessment->description ?: 'Test your comprehension of this scholarly unit with a curated MCQ challenge.' }}
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="md:col-span-4 flex justify-end">
                                                        <a href="{{ route('assessment.take', $assessment->id) }}" class="w-full md:w-auto bg-primary text-white px-10 py-5 rounded-2xl font-bold uppercase tracking-widest text-[10px] shadow-2xl shadow-primary/30 hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                                                            Begin Assessment
                                                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </section>
            </div>

            <!-- Sidebar Widgets -->
            <aside class="lg:col-span-4 space-y-8 lg:sticky lg:top-24 h-fit">
                <!-- Progress Widget (Conditional) -->
                @auth
                @if($lessons->count() > 0)
                <section class="bg-stone-900 rounded-[32px] p-8 text-white relative overflow-hidden shadow-2xl">
                    <div class="absolute inset-0 opacity-10 ethno-pattern scale-150"></div>
                    <div class="relative z-10 text-center">
                        <div class="relative inline-flex items-center justify-center mb-6">
                            <svg class="w-24 h-24 transform -rotate-90">
                                <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="4" fill="transparent" class="text-white/10" />
                                <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="4" fill="transparent" stroke-dasharray="251.2" stroke-dashoffset="{{ 251.2 - (min(100, $progress['percentage']) / 100 * 251.2) }}" class="text-orange-500 transition-all duration-1000" />
                            </svg>
                            <span class="absolute text-2xl font-headline font-bold italic">{{ $progress['percentage'] }}%</span>
                        </div>
                        <h4 class="font-headline text-xl font-bold italic mb-2">Academic Journey</h4>
                        <p class="text-[10px] text-stone-400 uppercase tracking-[0.2em]">{{ $progress['completed_count'] }} of {{ $progress['total_count'] }} Lessons Completed</p>
                    </div>
                </section>
                @endif
                @endauth

                <!-- Resources Widget -->
                @php
                    $sidebarResources = $resources;
                    if($selectedClassId === 'legacy') {
                        $sidebarResources = $resources->whereNull('lms_module_class_id');
                    } elseif($selectedClassId) {
                        $sidebarResources = $resources->where('lms_module_class_id', $selectedClassId);
                    }
                @endphp

                @if($sidebarResources->isNotEmpty())
                <section class="bg-white rounded-[24px] md:rounded-[32px] p-6 md:p-8 border border-stone-200 shadow-sm animate-in fade-in duration-500">
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.3em] text-stone-500 mb-6 border-b border-stone-100 pb-3">
                        {{ $selectedClassId ? 'Unit Archives' : 'Scholarly Materials' }}
                    </h3>
                    <div class="space-y-4">
                        @foreach($sidebarResources as $resource)
                            @php 
                                $isLocked = !$resource->canAccess(Auth::user());
                            @endphp
                            <div wire:click="downloadResource({{ $resource->id }})" class="group flex items-start gap-4 p-3 md:p-4 rounded-2xl hover:bg-stone-50 transition-colors cursor-pointer">
                                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-orange-50 text-primary flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-sm">{{ $isLocked ? 'lock' : 'picture_as_pdf' }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-sm font-bold text-stone-900 group-hover:text-primary transition-colors leading-tight italic mb-1 truncate">{{ $resource->title }}</h5>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-[9px] text-stone-400 uppercase font-bold tracking-widest">PDF Attachment</span>
                                        @if($isLocked)
                                            <span class="text-[8px] bg-primary text-white px-1.5 py-0.5 rounded-full font-bold uppercase tracking-widest">Members Only</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- Related Content -->
                @if($relatedModules->isNotEmpty())
                <section class="bg-white rounded-[32px] p-8 border border-stone-200 shadow-sm">
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.3em] text-stone-500 mb-6 border-b border-stone-100 pb-3">Related Studies</h3>
                    <div class="space-y-6">
                        @foreach($relatedModules as $related)
                            <a href="{{ route('modules.show', $related->slug) }}" class="group block">
                                <div class="aspect-video rounded-2xl overflow-hidden mb-4 bg-stone-100 border border-stone-100">
                                    @if($related->cover_image)
                                        <img src="{{ Storage::url($related->cover_image) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <h5 class="font-headline text-lg font-bold text-stone-900 group-hover:text-primary transition-colors italic leading-tight">{{ $related->title }}</h5>
                            </a>
                        @endforeach
                    </div>
                </section>
                @endif
            </div>
        </div>
    </div>
</div>
