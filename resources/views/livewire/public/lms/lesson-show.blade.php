<div class="min-h-screen bg-stone-50">
    <div class="max-w-[1600px] mx-auto">
        <div class="flex flex-col lg:flex-row min-h-screen">
            <!-- Main Content: Video & Article -->
            <main class="flex-1 lg:border-r border-stone-200">
                <!-- Breadcrumbs & Header -->
                <div class="bg-white px-8 py-6 border-b border-stone-200">
                    <nav class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-4 font-body">
                        <a href="{{ route('modules.index') }}" class="hover:text-primary transition-colors">Modules</a>
                        <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                        <a href="{{ route('modules.show', $module->slug) }}" class="hover:text-primary transition-colors truncate max-w-[150px]">{{ $module->title }}</a>
                        <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                        <span class="text-stone-900 truncate">Current Unit</span>
                    </nav>
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <h1 class="font-headline text-3xl font-bold italic text-stone-900 leading-tight">{{ $lesson->title }}</h1>
                    </div>
                </div>

                <!-- Video Player Section -->
                <div class="bg-stone-900 aspect-video relative group">
                    @if($lesson->video_source_type == 'upload' && $lesson->video_path)
                        <video controls class="w-full h-full" poster="{{ $lesson->thumbnail ? Storage::url($lesson->thumbnail) : '' }}">
                            <source src="{{ Storage::url($lesson->video_path) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @elseif($lesson->video_source_type == 'url' && $lesson->video_url)
                        <div class="w-full h-full">
                            @if(str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
                                @php 
                                    $videoId = '';
                                    if(str_contains($lesson->video_url, 'embed/')) {
                                        $parts = explode('embed/', $lesson->video_url);
                                        $videoId = explode('?', $parts[1] ?? '')[0];
                                    } elseif(str_contains($lesson->video_url, 'v=')) {
                                        $parts = explode('v=', $lesson->video_url);
                                        $videoId = explode('&', $parts[1] ?? '')[0];
                                    } else {
                                        $parts = explode('.be/', $lesson->video_url);
                                        $videoId = explode('?', $parts[1] ?? '')[0];
                                    }
                                @endphp
                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" class="w-full h-full border-none shadow-2xl" allowfullscreen></iframe>
                            @elseif(str_contains($lesson->video_url, 'vimeo.com'))
                                @php 
                                    $vimeoId = (int) filter_var($lesson->video_url, FILTER_SANITIZE_NUMBER_INT);
                                @endphp
                                <iframe src="https://player.vimeo.com/video/{{ $vimeoId }}" class="w-full h-full border-none" allowfullscreen></iframe>
                            @else
                                <div class="w-full h-full flex items-center justify-center text-white/50 flex-col gap-4">
                                    <span class="material-symbols-outlined text-6xl">link_off</span>
                                    <p class="font-headline italic text-lg">Direct stream via external portal</p>
                                    <a href="{{ $lesson->video_url }}" target="_blank" class="bg-white/10 hover:bg-white/20 px-6 py-2 rounded-xl text-white text-xs font-bold uppercase tracking-widest transition-all">Launch Seminar</a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-white/20">
                            <span class="material-symbols-outlined text-8xl">play_circle</span>
                        </div>
                    @endif
                </div>

                <!-- Navigation Controls -->
                <div class="bg-white border-b border-stone-200 p-8 flex items-center justify-between shadow-sm sticky top-[64px] z-20">
                    <div class="flex items-center gap-10">
                        @if($prevLesson)
                            <a href="{{ route('lessons.show', ['moduleSlug' => $module->slug, 'lessonSlug' => $prevLesson->slug]) }}" class="group flex items-center gap-4 text-stone-500 hover:text-primary transition-all">
                                <div class="w-10 h-10 rounded-full border border-stone-200 flex items-center justify-center group-hover:border-primary transition-colors">
                                    <span class="material-symbols-outlined text-sm">chevron_left</span>
                                </div>
                                <div class="hidden md:flex flex-col">
                                    <span class="text-[9px] font-bold uppercase tracking-widest opacity-50">Previous Unit</span>
                                    <span class="text-[11px] font-bold leading-tight line-clamp-1 italic">{{ $prevLesson->title }}</span>
                                </div>
                            </a>
                        @else
                            <div class="w-10 h-10 opacity-0 md:hidden"></div>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <button class="flex items-center gap-2 bg-stone-900 text-white px-8 py-3 rounded-2xl font-bold uppercase tracking-widest text-[9px] hover:bg-primary transition-all shadow-lg shadow-stone-900/10">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            Mark Complete
                        </button>
                    </div>

                    <div class="flex items-center gap-10 justify-end">
                        @if($nextLesson)
                            <a href="{{ route('lessons.show', ['moduleSlug' => $module->slug, 'lessonSlug' => $nextLesson->slug]) }}" class="group flex items-center gap-4 text-stone-500 hover:text-primary transition-all text-right">
                                <div class="hidden md:flex flex-col">
                                    <span class="text-[9px] font-bold uppercase tracking-widest opacity-50">Up Next</span>
                                    <span class="text-[11px] font-bold leading-tight line-clamp-1 italic">{{ $nextLesson->title }}</span>
                                </div>
                                <div class="w-10 h-10 rounded-full border border-stone-200 flex items-center justify-center group-hover:border-primary transition-colors">
                                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Lesson Content Body -->
                <div class="p-8 md:p-16 max-w-4xl mx-auto">
                    <article class="prose prose-stone prose-lg max-w-none">
                        <div class="prose-content text-stone-700 dark:text-stone-300 space-y-10 leading-relaxed italic text-lg">
                            {!! nl2br(e($lesson->short_description)) !!}
                            
                            @if($lesson->notes)
                                <div class="mt-12 bg-stone-100 dark:bg-stone-900 rounded-[24px] p-8 md:p-12 border border-stone-200 dark:border-stone-800 shadow-inner relative overflow-hidden group">
                                    <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none transition-opacity group-hover:opacity-10">
                                        <span class="material-symbols-outlined text-6xl">history_edu</span>
                                    </div>
                                    <h3 class="font-headline text-2xl font-bold italic text-stone-400 mb-6 flex items-center gap-3">
                                        <span class="material-symbols-outlined text-primary text-xl">menu_book</span>
                                        Scholarly Notes
                                    </h3>
                                    <div class="relative z-10 prose prose-stone max-w-none text-stone-600 italic">
                                        {!! nl2br(e($lesson->notes)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </article>
                </div>
            </main>

            <!-- Sidebar: Curriculum Navigation -->
            <aside class="w-full lg:w-[400px] bg-white lg:sticky lg:top-[64px] lg:h-[calc(100vh-64px)] overflow-y-auto no-scrollbar shadow-2xl flex flex-col">
                <div class="p-8 border-b border-stone-200 bg-stone-50/50">
                    <h4 class="font-headline text-xl font-bold italic text-stone-900 mb-2">Curriculum Flow</h4>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[12px] text-primary">school</span>
                        {{ $lessons->count() }} Scholarly Units
                    </p>
                </div>

                <div class="flex-1 overflow-y-auto no-scrollbar">
                    <div class="divide-y divide-stone-100">
                        @foreach($lessons as $index => $unit)
                            @php 
                                $isActive = $unit->id === $lesson->id;
                                $isLocked = !$unit->canAccess(Auth::user());
                            @endphp
                            <div 
                                @if(!$isLocked) onclick="window.location.href='{{ route('lessons.show', ['moduleSlug' => $module->slug, 'lessonSlug' => $unit->slug]) }}'" @else @click="$dispatch('open-upgrade-modal')" @endif
                                class="p-6 transition-all cursor-pointer group {{ $isActive ? 'bg-orange-50/50 border-r-4 border-primary' : 'hover:bg-stone-50/30' }}">
                                <div class="flex gap-4">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border transition-all {{ $isActive ? 'bg-primary text-white border-primary shadow-lg shadow-primary/20' : 'bg-white text-stone-300 border-stone-200 group-hover:border-primary/30 group-hover:text-primary' }}">
                                        @if($isActive)
                                            <span class="material-symbols-outlined text-sm animate-pulse">equalizer</span>
                                        @else
                                            <span class="text-[10px] font-headline font-bold italic">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="text-xs font-bold leading-tight italic truncate {{ $isActive ? 'text-primary' : 'text-stone-900' }}">{{ $unit->title }}</h5>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[9px] font-bold uppercase tracking-widest text-stone-400">{{ $unit->duration_minutes }}m</span>
                                            @if($isLocked)
                                                <span class="material-symbols-outlined text-[10px] text-stone-300" style="font-variation-settings: 'FILL' 1;">lock</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($isActive)
                                        <span class="text-[9px] font-bold text-primary uppercase tracking-widest opacity-60">Now Watching</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Assistance Footer -->
                <div class="p-8 bg-stone-900 border-t border-white/5 text-center relative overflow-hidden group">
                    <div class="absolute inset-0 opacity-5 ethno-pattern scale-150 transition-transform group-hover:scale-100 duration-[2s]"></div>
                    <div class="relative z-10">
                        <span class="material-symbols-outlined text-orange-500 text-3xl mb-4" style="font-variation-settings: 'FILL' 1;">forum</span>
                        <h6 class="text-white text-sm font-headline italic font-bold mb-2">Join Daily Seminar</h6>
                        <p class="text-[10px] text-stone-500 leading-relaxed max-w-[200px] mx-auto mb-6">Connect with fellow scholars to discuss this narrative unit.</p>
                        <button class="w-full py-4 border border-white/20 rounded-xl text-[9px] font-bold text-white uppercase tracking-widest hover:bg-white/5 transition-all">Launch Community</button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
