<div class="pb-24">
    <!-- Hero Section -->
    <div class="relative bg-stone-900 overflow-hidden pt-24 pb-12 md:pt-32 md:pb-20 px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 opacity-20 ethno-pattern"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-orange-900/20 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-12">
                <div class="max-w-2xl space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 border border-primary/30 text-orange-200 text-[10px] font-bold uppercase tracking-widest">
                        <span class="material-symbols-outlined text-sm">public</span>
                        Global Anthropology Forum
                    </div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-headline font-bold text-white italic leading-tight">Connect with a Global Community of Scholars</h1>
                    <p class="text-lg text-stone-400 font-medium leading-relaxed">
                        Share your research, find collaborators, and engage in meaningful discussions across all domains of anthropology.
                    </p>
                    <div class="flex flex-wrap gap-4 pt-4">
                        @auth
                            <button wire:click="$dispatch('open-start-discussion')" class="bg-primary hover:bg-orange-800 text-white font-bold py-4 px-8 rounded-2xl shadow-xl shadow-primary/20 hover:-translate-y-1 transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined">add_comment</span>
                                Start New Discussion
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="bg-primary hover:bg-orange-800 text-white font-bold py-4 px-8 rounded-2xl shadow-xl shadow-primary/20 hover:-translate-y-1 transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined">login</span>
                                Login to Start Discussion
                            </a>
                        @endauth
                        <a href="#feed" class="bg-white/10 hover:bg-white/20 text-white font-bold py-4 px-8 rounded-2xl border border-white/10 backdrop-blur-md transition-all">
                            Browse Inquiries
                        </a>
                    </div>
                </div>
                
                <div class="w-full lg:w-80 shrink-0">
                    <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-3xl p-6 space-y-6 shadow-2xl">
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-primary uppercase tracking-widest leading-none">Scholarship Stats</p>
                            <p class="text-xs text-stone-500 italic">Real-time engagement across the platform.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                                <p class="text-2xl font-bold text-white">{{ number_format(\App\Models\Community\CommunityDiscussion::published()->count()) }}</p>
                                <p class="text-[9px] font-bold text-stone-400 uppercase tracking-tighter">Total Threads</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                                <p class="text-2xl font-bold text-white">{{ number_format(\App\Models\User::count()) }}</p>
                                <p class="text-[9px] font-bold text-stone-400 uppercase tracking-tighter">Active Scholars</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Topics Navigation -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">
        <div class="flex items-center gap-4 overflow-x-auto pb-4 scrollbar-hide py-2">
            @foreach($browseTopics as $bt)
                <button 
                    wire:click="selectTopic({{ $bt->id }})"
                    class="shrink-0 flex items-center gap-3 px-6 py-4 rounded-2xl border transition-all {{ $topicId == $bt->id ? 'bg-white shadow-xl scale-105 border-primary ring-4 ring-primary/5' : 'bg-stone-50/80 backdrop-blur-sm border-stone-200 hover:border-stone-300' }}"
                >
                    <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: {{ $bt->color ?? '#F97316' }}20; color: {{ $bt->color ?? '#F97316' }};">
                        <span class="material-symbols-outlined text-xl">{{ $bt->icon ?? 'category' }}</span>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold text-stone-900 leading-none">{{ $bt->name }}</p>
                        <p class="text-[9px] text-stone-400 font-bold uppercase mt-1">{{ $bt->community_discussions_count ?? 0 }} Threads</p>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Main Content Grid -->
    <div id="feed" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 md:mt-12 grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
        <!-- Feed Column -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Feed Tabs & Search -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-stone-200">
                <div class="flex items-center gap-2 p-1 bg-stone-100 rounded-xl w-fit">
                    @foreach(['all' => 'Discovery', 'hot' => 'Hot', 'newest' => 'Newest', 'unsolved' => 'Unsolved'] as $key => $label)
                        <button 
                            wire:click="selectTab('{{ $key }}')"
                            class="px-5 py-2 text-xs font-bold uppercase tracking-widest rounded-lg transition-all {{ $tab === $key ? 'bg-white text-primary shadow-sm' : 'text-stone-400 hover:text-stone-600' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                
                <div class="relative flex-1 max-w-sm">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                    <input 
                        wire:model.live.debounce.300ms="search"
                        type="text" 
                        placeholder="Search threads..." 
                        class="w-full bg-stone-50 border-stone-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:ring-primary focus:border-primary"
                    >
                </div>
            </div>

            <!-- Active Filters Badge -->
            @if($topicId || $tag || $search)
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">Filtering by:</span>
                    @if($topicId)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-stone-900 text-white text-[10px] font-bold rounded-full uppercase tracking-widest">
                            Topic: {{ $browseTopics->firstWhere('id', $topicId)?->name ?? 'Specified Domain' }}
                            <button wire:click="$set('topicId', '')" class="hover:text-red-400 transition-colors"><span class="material-symbols-outlined text-[10px]">close</span></button>
                        </span>
                    @endif
                    @if($tag)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-primary text-white text-[10px] font-bold rounded-full uppercase tracking-widest">
                            Tag: {{ $tag }}
                            <button wire:click="$set('tag', '')" class="hover:text-stone-200 transition-colors"><span class="material-symbols-outlined text-[10px]">close</span></button>
                        </span>
                    @endif
                    @if($search)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-stone-100 text-stone-600 border border-stone-200 text-[10px] font-bold rounded-full uppercase tracking-widest">
                            "{{ $search }}"
                            <button wire:click="$set('search', '')" class="hover:text-red-500 transition-colors"><span class="material-symbols-outlined text-[10px]">close</span></button>
                        </span>
                    @endif
                    <button wire:click="resetFilters" class="text-[10px] font-bold text-primary hover:underline uppercase tracking-widest">Clear All</button>
                </div>
            @endif

            <!-- Discussion List -->
            <div class="space-y-6">
                @forelse($discussions as $disc)
                    <div class="group relative bg-white rounded-3xl p-6 border border-stone-200 shadow-sm hover:shadow-xl hover:border-primary/20 transition-all">
                        <div class="flex items-start gap-6">
                            <!-- Engagement Score -->
                            <div class="hidden sm:flex flex-col items-center justify-center w-16 px-2 py-4 bg-stone-50 rounded-2xl border border-stone-100 text-stone-400 group-hover:bg-primary/5 group-hover:border-primary/10 transition-colors">
                                <span class="text-lg font-bold text-stone-800">{{ $disc->replies_count }}</span>
                                <span class="text-[9px] font-bold uppercase tracking-tighter">Replies</span>
                                <div class="w-full h-px bg-stone-200 my-2"></div>
                                <span class="text-xs font-bold text-stone-500">{{ $disc->likes_count }}</span>
                                <span class="text-[8px] font-bold uppercase tracking-tighter">Likes</span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="h-6 w-6 rounded-full bg-stone-100 flex items-center justify-center overflow-hidden border border-stone-200">
                                        <img src="{{ $disc->author?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($disc->author?->name ?? 'User') }}" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-widest">{{ $disc->author?->name ?? 'Anonymous Scholar' }}</span>
                                    <span class="text-[10px] text-stone-400">•</span>
                                    <span class="text-[10px] text-stone-400 font-medium">{{ $disc->published_at->diffForHumans() }}</span>
                                    @if($disc->is_featured)
                                        <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[9px] font-bold uppercase tracking-widest rounded-full">Featured</span>
                                    @endif
                                </div>
                                
                                <a href="{{ route('community.show', $disc->slug) }}" class="block">
                                    <h3 class="text-xl font-headline font-bold text-stone-900 group-hover:text-primary transition-colors leading-tight mb-2">{{ $disc->title }}</h3>
                                    <p class="text-sm text-stone-500 line-clamp-2 italic">{{ $disc->excerpt }}</p>
                                </a>
                                
                                <div class="flex flex-wrap items-center gap-3 sm:gap-4 mt-6">
                                    <button wire:click="selectTopic({{ $disc->topic_id }})" class="flex items-center gap-1.5 px-3 py-1 bg-stone-50 text-stone-600 rounded-full text-[9px] sm:text-[10px] font-bold uppercase tracking-widest border border-stone-200 hover:border-primary transition-colors">
                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $disc->topic?->color ?? '#F97316' }}"></span>
                                        {{ $disc->topic?->name ?? 'General' }}
                                    </button>
                                    
                                    <div class="flex gap-2">
                                        @foreach($disc->tags->take(2) as $tag_obj)
                                            <button wire:click="$set('tag', '{{ $tag_obj->slug }}')" class="text-[9px] sm:text-[10px] font-bold text-primary opacity-60 hover:opacity-100 italic transition-opacity">#{{ $tag_obj->name }}</button>
                                        @endforeach
                                    </div>
                                    
                                    <div class="ml-auto flex items-center gap-3 sm:gap-4 text-stone-400">
                                        <div class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                            <span class="text-[9px] sm:text-[10px] font-bold">{{ number_format($disc->views_count) }}</span>
                                        </div>
                                        <div class="flex sm:hidden items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">forum</span>
                                            <span class="text-[9px] font-bold">{{ $disc->replies_count }}</span>
                                        </div>
                                        <button class="hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-sm">bookmark</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center bg-stone-50 rounded-[40px] border-2 border-dashed border-stone-200">
                        <div class="w-20 h-20 bg-stone-100 rounded-full flex items-center justify-center mx-auto mb-6 text-stone-300">
                            <span class="material-symbols-outlined text-4xl">search_off</span>
                        </div>
                        <h3 class="text-2xl font-headline font-bold text-stone-900 italic">No inquiries found</h3>
                        <p class="text-stone-500 mt-2 max-w-sm mx-auto">Adjust your filters or be the first to start a discussion in this field.</p>
                        <button wire:click="resetFilters" class="mt-6 text-primary font-bold hover:underline uppercase text-xs tracking-widest">Clear all filters</button>
                    </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $discussions->links() }}
            </div>
        </div>

        <!-- Sidebar Column -->
        <aside class="space-y-12">
            <!-- Expert Spotlight Widget -->
            @if($expertSpotlight)
                <div class="relative bg-stone-900 rounded-[40px] p-8 border border-stone-800 shadow-2xl overflow-hidden group">
                    <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
                    <div class="relative z-10 space-y-6">
                        <div class="flex justify-between items-start">
                            <span class="px-3 py-1 bg-primary text-white text-[9px] font-bold uppercase tracking-widest rounded-full shadow-lg shadow-primary/20">Expert Spotlight</span>
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                        
                        <div class="space-y-4">
                            <h4 class="text-xl font-headline font-bold text-white italic leading-tight group-hover:text-primary transition-colors">{{ $expertSpotlight->title }}</h4>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full border border-primary/30 overflow-hidden">
                                    <img src="{{ $expertSpotlight->author?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($expertSpotlight->author?->name ?? 'Expert') }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-stone-200 leading-none">{{ $expertSpotlight->author?->name }}</p>
                                    <p class="text-[9px] text-stone-500 uppercase tracking-widest mt-1">Certified Researcher</p>
                                </div>
                            </div>
                        </div>
                        
                        <button class="w-full py-4 bg-white/5 hover:bg-white text-stone-300 hover:text-stone-900 rounded-2xl text-xs font-bold uppercase tracking-widest border border-white/10 transition-all group-hover:scale-[1.02]">
                            Read Analysis
                        </button>
                    </div>
                </div>
            @endif

            <!-- Popular Discussions Widget -->
            <section class="space-y-6">
                <h3 class="text-xs font-bold text-stone-900 uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    Popular Discussions
                </h3>
                <div class="space-y-6">
                    @foreach($popularDiscussions as $pd)
                        <div class="flex gap-4 group cursor-pointer">
                            <div class="shrink-0 w-8 text-2xl font-headline font-bold text-stone-200 italic group-hover:text-primary transition-colors">0{{ $loop->iteration }}</div>
                            <div class="space-y-1">
                                <h5 class="text-sm font-bold text-stone-800 leading-tight group-hover:underline line-clamp-2 decoration-primary underline-offset-4">{{ $pd->title }}</h5>
                                <p class="text-[10px] text-stone-400 font-bold uppercase tracking-widest">{{ $pd->replies_count }} Replies • {{ $pd->topic?->name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Trending Tags Widget -->
            <section class="bg-stone-50 rounded-[40px] p-8 border border-stone-200/60 h-fit">
                <h3 class="text-xs font-bold text-stone-900 uppercase tracking-widest mb-6">Trending Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($trendingTags as $tt)
                        <button 
                            wire:click="$set('tag', '{{ $tt->slug }}')"
                            class="px-4 py-2 bg-white border border-stone-200 rounded-xl text-[10px] font-bold text-stone-600 hover:border-primary hover:text-primary transition-all shadow-sm"
                        >
                            #{{ $tt->name }} <span class="ml-1 text-stone-300 font-medium">({{ $tt->discussions_count }})</span>
                        </button>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>
</div>
