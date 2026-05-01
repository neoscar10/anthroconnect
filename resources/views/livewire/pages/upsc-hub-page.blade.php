<div class="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-background-light dark:bg-background-dark font-display text-charcoal">
    <style>
        .academic-pattern {
            background-color: #f8f7f6;
            background-image: radial-gradient(#9e5015 0.5px, transparent 0.5px), radial-gradient(#9e5015 0.5px, #f8f7f6 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.05;
        }
        :root {
            --primary: #9e5015;
            --background-light: #f8f7f6;
            --background-dark: #211811;
            --sandstone: #eaddcf;
            --olive-muted: #6b705c;
            --charcoal: #2d2d2d;
        }
    </style>

    <main class="mx-auto w-full max-w-7xl px-6 lg:px-20 py-10">
        <!-- Hero Header -->
        <section class="relative overflow-hidden rounded-2xl bg-white p-10 shadow-sm border border-sandstone mb-12">
            <div class="academic-pattern absolute inset-0"></div>
            <div class="relative z-10 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider mb-4">
                    <span class="material-symbols-outlined text-sm">school</span> Academic Excellence
                </div>
                <h2 class="font-serif text-5xl text-charcoal mb-4">UPSC Anthropology Hub</h2>
                <p class="text-lg text-olive-muted leading-relaxed">
                    Structured preparation within a global academic ecosystem. Transition from rote learning to deep conceptual clarity for the Civil Services Examination.
                </p>
                <div class="mt-8 flex gap-4">
                    <a wire:navigate href="{{ route('exams.index') }}" class="bg-primary text-white px-6 py-2.5 rounded-lg font-bold hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20">Take Practice Exams</a>
                    <a wire:navigate href="{{ route('exams.index', ['kind' => 'past']) }}" class="bg-background-light text-charcoal px-6 py-2.5 rounded-lg font-bold border border-sandstone hover:bg-sandstone transition-colors">View Past Questions</a>
                </div>
            </div>
        </section>

        <!-- Progress Tracker -->
        <section class="mb-12">
            <div class="bg-sandstone/30 rounded-xl p-6 border border-sandstone flex flex-wrap items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-full bg-white flex items-center justify-center text-primary border border-primary/20">
                        <span class="material-symbols-outlined text-2xl">insights</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-charcoal">Your UPSC Preparation Progress</h3>
                        @if($isGuest)
                            <p class="text-sm text-olive-muted">Sign in to track your syllabus completion</p>
                        @else
                            <p class="text-sm text-olive-muted">{{ $upscProgress }}% of the Core Syllabus completed</p>
                        @endif
                    </div>
                </div>
                <div class="flex-1 max-w-md h-3 bg-white rounded-full overflow-hidden border border-sandstone">
                    <div class="h-full bg-primary transition-all duration-1000" style="width: {{ $upscProgress }}%"></div>
                </div>
                @if($isGuest)
                    <a href="{{ route('login') }}" class="text-primary text-sm font-bold flex items-center gap-1 hover:underline">
                        Sign In to Start Tracking <span class="material-symbols-outlined text-sm">login</span>
                    </a>
                @elseif($nextRecommendedLesson)
                    <a href="{{ route('lessons.show', [$nextRecommendedLesson->module->slug, $nextRecommendedLesson->slug]) }}" class="text-primary text-sm font-bold flex items-center gap-1 hover:underline">
                        {{ $recommendationText }} <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                @elseif($modules->count() > 0)
                    <a href="{{ route('modules.show', $modules->first()->slug) }}" class="text-primary text-sm font-bold flex items-center gap-1 hover:underline">
                        Start First Module <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                @endif
            </div>
        </section>

        {{-- 
        <!-- Syllabus Cards -->
        <section class="mb-16">
            <h3 class="font-serif text-3xl mb-8 flex items-center gap-3">
                <span class="h-px w-12 bg-primary/30"></span> Syllabus Foundations
            </h3>
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Paper I -->
                <div class="group bg-white rounded-2xl p-8 border border-sandstone shadow-sm hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h4 class="text-2xl font-serif text-charcoal mb-2">Paper I: Foundations</h4>
                            <p class="text-olive-muted text-sm italic">Evolution, Social Structure, Theory</p>
                        </div>
                        <span class="material-symbols-outlined text-primary/30 text-4xl group-hover:text-primary transition-colors">history_edu</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2 text-sm text-charcoal/80"><span class="h-1.5 w-1.5 rounded-full bg-primary/40"></span> Human Evolution & Primatology</li>
                        <li class="flex items-center gap-2 text-sm text-charcoal/80"><span class="h-1.5 w-1.5 rounded-full bg-primary/40"></span> Kinship, Marriage & Family</li>
                        <li class="flex items-center gap-2 text-sm text-charcoal/80"><span class="h-1.5 w-1.5 rounded-full bg-primary/40"></span> Economic & Political Org</li>
                        <li class="flex items-center gap-2 text-sm text-charcoal/80"><span class="h-1.5 w-1.5 rounded-full bg-primary/40"></span> Anthropological Theories</li>
                    </ul>
                    <button class="w-full bg-primary/10 text-primary py-3 rounded-lg font-bold hover:bg-primary hover:text-white transition-all">Explore Paper I Syllabus</button>
                </div>
                <!-- Paper II -->
                <div class="group bg-white rounded-2xl p-8 border border-sandstone shadow-sm hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h4 class="text-2xl font-serif text-charcoal mb-2">Paper II: Indian Anthropology</h4>
                            <p class="text-olive-muted text-sm italic">Civilization, Tribes, Social System</p>
                        </div>
                        <span class="material-symbols-outlined text-primary/30 text-4xl group-hover:text-primary transition-colors">location_city</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2 text-sm text-charcoal/80"><span class="h-1.5 w-1.5 rounded-full bg-primary/40"></span> Indian Social System: Varna/Caste</li>
                        <li class="flex items-center gap-2 text-sm text-charcoal/80"><span class="h-1.5 w-1.5 rounded-full bg-primary/40"></span> Tribal India & Problems</li>
                        <li class="flex items-center gap-2 text-sm text-charcoal/80"><span class="h-1.5 w-1.5 rounded-full bg-primary/40"></span> Impact of Islam, Buddhism, Christianity</li>
                        <li class="flex items-center gap-2 text-sm text-charcoal/80"><span class="h-1.5 w-1.5 rounded-full bg-primary/40"></span> Village Studies & Rural Dev</li>
                    </ul>
                    <button class="w-full bg-primary/10 text-primary py-3 rounded-lg font-bold hover:bg-primary hover:text-white transition-all">Explore Paper II Syllabus</button>
                </div>
            </div>
        </section>
        --}}

        <!-- Topic-wise Prep Grid -->
        <section class="mb-16">
            <div class="flex items-end justify-between mb-8">
                <h3 class="font-serif text-3xl">Core Prep Modules</h3>
                <a class="text-primary font-bold text-sm flex items-center gap-1 underline underline-offset-4" href="{{ route('modules.index') }}">View All Modules <span class="material-symbols-outlined text-sm">north_east</span></a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach($modules as $module)
                    <a href="{{ route('modules.show', $module->slug) }}" class="bg-white p-6 rounded-xl border border-sandstone text-center hover:border-primary transition-colors cursor-pointer group">
                        <div class="h-12 w-12 bg-background-light rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-primary/10">
                            <span class="material-symbols-outlined text-primary">school</span>
                        </div>
                        <h5 class="font-bold text-sm">{{ $module->title }}</h5>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- Thinkers Profile Cards -->
        @if($anthropologists->count() > 0)
        <section class="mb-16">
            <h3 class="font-serif text-3xl mb-8">Anthropological Thinkers for UPSC</h3>
            <div class="grid md:grid-cols-4 gap-6">
                @foreach($anthropologists as $item)
                    <div class="bg-white rounded-xl border border-sandstone overflow-hidden group">
                        <div class="h-48 bg-sandstone relative">
                            @if($item->profile_image)
                                <img src="{{ Storage::url($item->profile_image) }}" class="absolute inset-0 w-full h-full object-cover">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-charcoal/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <p class="text-xs uppercase font-bold tracking-widest opacity-80">{{ $item->is_upsc_relevant ? 'UPSC High-Yield' : 'Theorist' }}</p>
                                <h6 class="font-serif text-lg">{{ $item->name }}</h6>
                            </div>
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-olive-muted mb-4 line-clamp-2">{{ Str::limit($item->biography, 100) }}</p>
                            <a href="{{ route('encyclopedia.anthropologists.show', $item->slug) }}" class="text-xs font-bold text-primary flex items-center gap-1">Read Prep Summary <span class="material-symbols-outlined text-[14px]">chevron_right</span></a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif
        <!-- Core Concepts & Theories -->
        <div class="grid lg:grid-cols-2 gap-12 mb-16 items-stretch">
            <!-- Major Theories -->
            <section class="flex flex-col">
                <div class="flex items-end justify-between mb-8">
                    <h3 class="font-serif text-3xl">High-Yield Theories</h3>
                    <a href="{{ route('encyclopedia.index') }}" class="text-primary text-xs font-bold uppercase tracking-widest hover:underline">View All</a>
                </div>
                <div class="space-y-4 flex-1">
                    @foreach($theories as $theory)
                        <a href="{{ route('encyclopedia.theories.show', $theory->slug) }}" class="flex items-center justify-between p-5 bg-white rounded-xl border border-sandstone hover:border-primary transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-sandstone/50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                                    <span class="material-symbols-outlined text-sm">psychology</span>
                                </div>
                                <h6 class="font-bold text-charcoal group-hover:text-primary transition-colors text-sm">{{ $theory->title }}</h6>
                            </div>
                            <span class="material-symbols-outlined text-sandstone group-hover:text-primary transition-colors">arrow_forward</span>
                        </a>
                    @endforeach
                </div>
            </section>

            <!-- Core Concepts -->
            <section class="flex flex-col">
                <div class="flex items-end justify-between mb-8">
                    <h3 class="font-serif text-3xl">Essential Concepts</h3>
                    <a href="{{ route('encyclopedia.index') }}" class="text-primary text-xs font-bold uppercase tracking-widest hover:underline">View All</a>
                </div>
                <div class="space-y-4 flex-1">
                    @foreach($concepts as $concept)
                        <a href="{{ route('encyclopedia.concepts.show', $concept->slug) }}" class="flex items-center justify-between p-5 bg-white rounded-xl border border-sandstone hover:border-primary transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-sandstone/50 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                                    <span class="material-symbols-outlined text-sm">menu_book</span>
                                </div>
                                <h6 class="font-bold text-charcoal group-hover:text-primary transition-colors text-sm">{{ $concept->title }}</h6>
                            </div>
                            <span class="material-symbols-outlined text-sandstone group-hover:text-primary transition-colors">arrow_forward</span>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
        <!-- Expert Insights (Explore Section - Blog Style) -->
        <section class="mb-16">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h3 class="font-serif text-4xl text-charcoal mb-2">Expert Insights & Narratives</h3>
                    <p class="text-olive-muted text-sm italic">In-depth anthropological analyses and field stories</p>
                </div>
                <a href="{{ route('explore.index') }}" class="text-primary font-bold text-sm flex items-center gap-1 underline underline-offset-4">
                    Explore All Insights <span class="material-symbols-outlined text-sm">north_east</span>
                </a>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($exploreItems as $article)
                    @php 
                        $isRestricted = !$article->canAccess(auth()->user()); 
                        $restrictedClick = auth()->check() 
                            ? "\$dispatch('open-upgrade-modal')" 
                            : "window.location.href='" . route('login') . "'";
                    @endphp
                    <div class="group bg-white rounded-2xl overflow-hidden border border-sandstone shadow-sm hover:shadow-md transition-all flex flex-col relative">
                        <div class="relative aspect-[16/9] overflow-hidden bg-sandstone">
                            @if($article->featured_image)
                                <img src="{{ Storage::url($article->featured_image) }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 {{ $isRestricted ? 'blur-sm grayscale opacity-50' : '' }}">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center text-primary/20">
                                    <span class="material-symbols-outlined text-6xl">article</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-charcoal/40 to-transparent"></div>
                            <div class="absolute bottom-4 left-4">
                                <span class="bg-primary text-white text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded">Analysis</span>
                            </div>
                            @if($isRestricted)
                                <div class="absolute inset-0 flex items-center justify-center bg-black/10 backdrop-blur-[2px]">
                                    <div class="bg-white/90 p-2 rounded-full shadow-lg">
                                        <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1">lock</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <h4 class="font-serif text-xl text-charcoal mb-3 line-clamp-2 group-hover:text-primary transition-colors">
                                {{ $article->title }}
                            </h4>
                            <p class="text-olive-muted text-sm mb-6 line-clamp-3 leading-relaxed flex-1">
                                {{ $article->excerpt }}
                            </p>
                            <div class="pt-6 border-t border-sandstone flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="size-6 rounded-full bg-sandstone flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[14px] text-primary">person</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-charcoal/60 uppercase tracking-widest">{{ $article->creator->name ?? 'Editorial' }}</span>
                                </div>
                                <a href="{{ route('explore.show', $article->slug) }}" 
                                   @if($isRestricted) @click.prevent="{!! $restrictedClick !!}" @endif
                                   class="text-primary text-xs font-bold flex items-center gap-1 hover:underline">
                                    Read Full <span class="material-symbols-outlined text-sm">arrow_right_alt</span>
                                </a>
                            </div>
                        </div>
                        @if($isRestricted)
                            <button @click.prevent="{!! $restrictedClick !!}" class="absolute inset-0 w-full h-full cursor-pointer z-10"></button>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <!-- UPSC Reference Library (Resource Shelf Style) -->
        <section class="mb-16">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h3 class="font-serif text-4xl text-charcoal mb-2">UPSC Reference Library</h3>
                    <p class="text-olive-muted text-sm italic">Core textbooks, reports, and digital scholarly archives</p>
                </div>
                <a href="{{ route('library.index') }}" class="text-primary font-bold text-sm flex items-center gap-1 underline underline-offset-4">
                    Open Digital Library <span class="material-symbols-outlined text-sm">north_east</span>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach($resources as $res)
                    @php 
                        $isRestricted = !$res->canAccess(auth()->user()); 
                        $restrictedClick = auth()->check() 
                            ? "\$dispatch('open-upgrade-modal')" 
                            : "window.location.href='" . route('login') . "'";
                    @endphp
                    <a href="{{ route('library.show', $res->slug) }}" 
                       @if($isRestricted) @click.prevent="{!! $restrictedClick !!}" @endif
                       class="group bg-white p-6 rounded-2xl border border-sandstone hover:border-primary transition-all flex flex-col items-center text-center shadow-sm relative overflow-hidden">
                        <div class="w-20 h-28 bg-background-light rounded-lg border border-sandstone flex items-center justify-center mb-4 group-hover:bg-primary/5 transition-colors relative overflow-hidden shadow-sm group-hover:shadow-md">
                            @if($res->cover_image_path || $res->cover_external_url)
                                <img src="{{ $res->cover_url }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 {{ $isRestricted ? 'blur-sm grayscale opacity-50' : '' }}">
                            @else
                                <span class="material-symbols-outlined text-primary text-4xl group-hover:scale-110 transition-transform">auto_stories</span>
                            @endif
                            <div class="absolute -top-1 -right-1 bg-white border border-sandstone px-1.5 py-0.5 rounded text-[8px] font-bold text-olive-muted shadow-sm uppercase tracking-tighter z-10">
                                {{ $res->resourceType->name ?? 'PDF' }}
                            </div>
                            @if($isRestricted)
                                <div class="absolute inset-0 flex items-center justify-center bg-black/5 backdrop-blur-[1px] z-10">
                                    <span class="material-symbols-outlined text-primary text-xl" style="font-variation-settings: 'FILL' 1">lock</span>
                                </div>
                            @endif
                        </div>
                        <h5 class="font-bold text-xs text-charcoal line-clamp-2 mb-1 group-hover:text-primary transition-colors">{{ $res->title }}</h5>
                        <p class="text-[9px] text-olive-muted font-medium uppercase tracking-widest">{{ $res->resourceType->name ?? 'General' }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- Integrated Learning / Knowledge Map -->
        <section class="mb-16 grid lg:grid-cols-2 gap-12 items-center bg-sandstone/10 p-10 rounded-3xl border border-sandstone/40">
            <div>
                <h3 class="font-serif text-4xl mb-6">Learn Concepts for Better Answers</h3>
                <p class="text-olive-muted mb-8 text-lg">Connect traditional syllabus boundaries. Integrate Paper I theories with Paper II Indian context for high-scoring answers.</p>
                <div class="space-y-4">
                    <a href="{{ route('knowledge-map.show') }}" class="flex gap-4 p-4 bg-white rounded-xl border border-sandstone shadow-sm hover:translate-x-1 transition-transform cursor-pointer">
                        <span class="material-symbols-outlined text-primary">hub</span>
                        <div>
                            <h6 class="font-bold">Knowledge Map Integration</h6>
                            <p class="text-sm text-olive-muted">Visualize linkages between Kinship and Caste dynamics.</p>
                        </div>
                    </a>
                    <a href="{{ route('library.index') }}" class="flex gap-4 p-4 bg-white rounded-xl border border-sandstone shadow-sm hover:translate-x-1 transition-transform cursor-pointer">
                        <span class="material-symbols-outlined text-primary">import_contacts</span>
                        <div>
                            <h6 class="font-bold">Global Case Study Library</h6>
                            <p class="text-sm text-olive-muted">Comparative examples for Paper II Tribal issues.</p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="relative bg-white p-6 rounded-2xl border border-sandstone shadow-inner">
                <div class="aspect-square bg-background-light rounded-lg flex items-center justify-center border-dashed border-2 border-sandstone">
                    <div class="text-center p-8">
                        <span class="material-symbols-outlined text-primary text-5xl mb-4">map</span>
                        <p class="font-bold">Interactive Knowledge Map</p>
                        <p class="text-xs text-olive-muted">Click to view conceptual intersections</p>
                        <a href="{{ route('knowledge-map.show') }}" class="mt-4 inline-block bg-primary text-white px-4 py-2 rounded-lg text-xs font-bold">Open Map</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Answer Writing & PYQs -->
        <div class="grid lg:grid-cols-3 gap-12 mb-16">
            <!-- Answer Writing -->
            <div class="lg:col-span-2">
                <h3 class="font-serif text-2xl mb-6">Answer Writing Excellence</h3>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div class="bg-white p-6 rounded-xl border border-sandstone shadow-sm text-center">
                        <span class="material-symbols-outlined text-primary text-3xl mb-2">stylus</span>
                        <h6 class="font-bold text-sm mb-1">Practice Question</h6>
                        <p class="text-xs text-olive-muted">Weekly targeted prompts</p>
                        <a wire:navigate href="{{ route('exams.index') }}" class="mt-4 inline-block text-xs font-bold text-primary">Start Writing</a>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-sandstone shadow-sm text-center">
                        <span class="material-symbols-outlined text-primary text-3xl mb-2">task_alt</span>
                        <h6 class="font-bold text-sm mb-1">Past Questions</h6>
                        <p class="text-xs text-olive-muted">High-scoring frameworks</p>
                        <a wire:navigate href="{{ route('exams.index', ['kind' => 'past']) }}" class="mt-4 inline-block text-xs font-bold text-primary">View Library</a>
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-sandstone shadow-sm text-center">
                        <span class="material-symbols-outlined text-primary text-3xl mb-2">rate_review</span>
                        <h6 class="font-bold text-sm mb-1">Peer Evaluation</h6>
                        <p class="text-xs text-olive-muted">Constructive community feedback</p>
                        <a wire:navigate href="{{ route('community.index') }}" class="mt-4 inline-block text-xs font-bold text-primary">Join Review</a>
                    </div>
                </div>
            </div>
            <!-- PYQs -->
            <div>
                <h3 class="font-serif text-2xl mb-6">Recent PYQs</h3>
                <div class="space-y-4">
                    @foreach($pastQuestions as $pq)
                        <a wire:navigate href="{{ route('exams.show', $pq->slug) }}" class="block p-4 bg-white rounded-lg border-l-4 border-primary shadow-sm hover:border-l-8 transition-all">
                            <div class="flex justify-between items-start mb-2">
                                <span class="bg-sandstone px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">{{ $pq->year }} - Paper {{ $pq->paper_type ?? 'I' }}</span>
                                <span class="text-[10px] text-olive-muted italic">{{ $pq->marks }} Marks</span>
                            </div>
                            <p class="text-sm font-medium leading-snug">{{ Str::limit(strip_tags($pq->question_text), 100) }}</p>
                        </a>
                    @endforeach
                </div>
                <a wire:navigate href="{{ route('exams.index', ['kind' => 'past']) }}" class="w-full mt-4 py-2 text-sm font-bold border border-sandstone rounded-lg hover:bg-sandstone transition-colors text-center block">Archive (2013-{{ date('Y') }})</a>
            </div>
        </div>

        <!-- Community Discussions -->
        <section class="mb-16">
            <div class="bg-white border border-sandstone rounded-3xl p-10 overflow-hidden relative shadow-sm">
                <div class="academic-pattern absolute inset-0 opacity-[0.03]"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-10 pb-6 border-b border-sandstone/50">
                        <div>
                            <h3 class="font-serif text-4xl mb-2 text-charcoal">UPSC Study Circles</h3>
                            <p class="text-olive-muted text-sm italic font-medium">Live peer interactions and expert-led discussions</p>
                        </div>
                        <a href="{{ route('community.index') }}" class="bg-primary text-white px-6 py-2.5 rounded-lg font-bold text-sm shadow-lg shadow-primary/20 hover:scale-105 transition-transform">Join a Circle</a>
                    </div>
                    <div class="grid md:grid-cols-2 gap-8">
                        @forelse($recentDiscussions as $discussion)
                            <a wire:navigate href="{{ route('community.show', $discussion->slug) }}" class="group bg-background-light border border-sandstone p-8 rounded-2xl hover:bg-white hover:shadow-xl hover:-translate-y-1 transition-all block">
                                <div class="flex items-center gap-2 text-[10px] text-primary font-black mb-4 uppercase tracking-[0.2em]">
                                    <span class="material-symbols-outlined text-sm">forum</span> Active Discussion
                                </div>
                                <h6 class="text-xl font-bold mb-3 text-charcoal group-hover:text-primary transition-colors leading-snug">{{ $discussion->title }}</h6>
                                <p class="text-sm text-olive-muted mb-6 line-clamp-2 leading-relaxed">
                                    Started by <span class="text-charcoal font-bold">{{ $discussion->author->name ?? 'Community Member' }}</span> • {{ $discussion->replies_count ?? 0 }} scholar responses
                                </p>
                                <div class="flex items-center gap-3">
                                    <div class="flex -space-x-2">
                                        <div class="h-8 w-8 rounded-full bg-sandstone border-2 border-white flex items-center justify-center text-[10px] text-charcoal font-bold shadow-sm">A</div>
                                        <div class="h-8 w-8 rounded-full bg-primary border-2 border-white flex items-center justify-center text-[10px] text-white font-bold shadow-sm">B</div>
                                        <div class="h-8 w-8 rounded-full bg-olive-muted border-2 border-white flex items-center justify-center text-[10px] text-white font-bold shadow-sm">C</div>
                                    </div>
                                    <span class="text-xs text-olive-muted font-bold tracking-wide group-hover:text-primary transition-colors flex items-center gap-1">
                                        Enter Discussion Circle <span class="material-symbols-outlined text-[16px]">arrow_right_alt</span>
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="col-span-2 text-center py-16 border border-dashed border-sandstone rounded-3xl bg-background-light">
                                <span class="material-symbols-outlined text-5xl text-primary/20 mb-4">group_off</span>
                                <p class="text-olive-muted font-serif text-xl">No active study circles found. Start one today!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
