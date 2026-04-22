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
                        <span class="text-stone-400 text-xs font-medium">{{ $module->lessons_count }} Lessons • ~{{ $module->estimated_duration ?? '120m' }}</span>
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

                <!-- Curriculum List -->
                <section class="bg-white rounded-[24px] md:rounded-[32px] p-6 md:p-12 border border-stone-200 shadow-sm">
                    <h2 class="font-headline text-2xl md:text-3xl font-bold italic text-stone-900 mb-8 md:mb-10 uppercase tracking-tight">Academic Units</h2>
                    
                    <div class="space-y-4">
                        @foreach($lessons as $index => $lesson)
                            @php 
                                $isCompleted = in_array($lesson->id, $completedLessonIds);
                                $isLocked = !$lesson->canAccess(Auth::user());
                            @endphp
                            <div wire:click="openLesson('{{ $lesson->slug }}')" 
                                class="group p-4 md:p-6 bg-stone-50 rounded-2xl border border-stone-100 flex flex-col md:flex-row md:items-center gap-4 md:gap-6 cursor-pointer hover:bg-white hover:border-primary/20 hover:shadow-lg transition-all duration-300">
                                <div class="flex items-center gap-4 md:gap-6 flex-1 min-w-0">
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-white border border-stone-200 flex items-center justify-center shrink-0 font-headline font-bold italic text-base md:text-lg {{ $isCompleted ? 'bg-green-500 border-green-500 text-white' : 'text-stone-300 group-hover:text-primary transition-colors' }}">
                                        @if($isCompleted)
                                            <span class="material-symbols-outlined text-sm">check</span>
                                        @else
                                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2 md:gap-3 mb-1">
                                            <h4 class="font-headline text-lg md:text-xl font-bold italic truncate transition-colors {{ $isCompleted ? 'text-green-700' : 'text-stone-900 group-hover:text-primary' }}">{{ $lesson->title }}</h4>
                                            @if($lesson->is_preview)
                                                <span class="bg-primary/10 text-primary px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest">Preview</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-stone-500 line-clamp-1 italic">{{ $lesson->short_description }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between md:justify-end gap-6 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-stone-100">
                                    <div class="flex flex-col items-start md:items-end">
                                        <span class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-stone-400">{{ $lesson->video_source_type == 'upload' ? 'Video Lecture' : 'External Seminar' }}</span>
                                        <span class="text-[8px] md:text-[9px] text-stone-400 italic">{{ $lesson->duration_minutes ?? '0' }}m duration</span>
                                    </div>
                                    @if($isLocked)
                                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-stone-200 flex items-center justify-center shadow-inner group-hover:bg-primary group-hover:text-white transition-all">
                                            <span class="material-symbols-outlined text-xs md:text-sm" style="font-variation-settings: 'FILL' 1;">lock</span>
                                        </div>
                                    @elseif($isCompleted)
                                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-green-100 border border-green-200 flex items-center justify-center text-green-600">
                                            <span class="material-symbols-outlined text-xs md:text-sm">verified</span>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-white border border-stone-200 flex items-center justify-center group-hover:border-primary group-hover:bg-primary group-hover:text-white transition-all">
                                            <span class="material-symbols-outlined text-xs md:text-sm">play_arrow</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
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
                @if($resources->isNotEmpty())
                <section class="bg-white rounded-[24px] md:rounded-[32px] p-6 md:p-8 border border-stone-200 shadow-sm">
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.3em] text-stone-500 mb-6 border-b border-stone-100 pb-3">Scholarly Materials</h3>
                    <div class="space-y-4">
                        @foreach($resources as $resource)
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
