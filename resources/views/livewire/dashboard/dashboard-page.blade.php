<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 md:hidden">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
            <input
                class="w-full bg-sand/30 border-none rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-primary/50"
                placeholder="Search resources..."
                type="text"
            />
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-9 space-y-12">
            <!-- Profile Header -->
            <div class="relative overflow-hidden rounded-[2rem] bg-white dark:bg-white/5 shadow-sm border border-sand/50 mb-8 transition-all duration-500 hover:shadow-xl hover:shadow-primary/5">
                <div class="w-full h-40 bg-primary/10 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#9e5015 1px, transparent 1px); background-size: 20px 20px;"></div>
                    <!-- Cover Glow -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-primary/20 blur-[100px] rounded-full -mr-20 -mt-20"></div>
                </div>
                
                <div class="px-8 pb-8 flex flex-col md:flex-row items-end gap-6 -mt-16 relative z-10">
                    <div class="shrink-0 relative group">
                        <label for="dashboard-avatar-upload" class="cursor-pointer">
                            @if(auth()->user()->avatar)
                                <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-3xl size-32 border-4 border-white dark:border-background-dark shadow-xl" style="background-image: url('{{ str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : Storage::url(auth()->user()->avatar) }}');"></div>
                            @else
                                <div class="flex items-center justify-center rounded-3xl size-32 border-4 border-white dark:border-background-dark shadow-xl bg-sand text-primary text-4xl font-black font-headline">
                                    {{ $this->userInitials }}
                                </div>
                            @endif
                            
                            <!-- Camera Overlay -->
                            <div class="absolute inset-0 bg-black/40 rounded-3xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-[1px]">
                                <span class="material-symbols-outlined text-white text-2xl">photo_camera</span>
                            </div>
                        </label>
                        <input type="file" id="dashboard-avatar-upload" wire:model="new_avatar" class="hidden" accept="image/*">
                        
                        <div wire:loading wire:target="new_avatar" class="absolute inset-0 bg-white/60 rounded-3xl flex items-center justify-center">
                            <div class="animate-spin size-6 border-2 border-primary border-t-transparent rounded-full"></div>
                        </div>

                        @if(session('status') === 'profile-updated')
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.opacity class="absolute -bottom-10 left-0 right-0 px-3 py-1.5 bg-green-500 text-white rounded-lg text-[9px] font-bold uppercase tracking-widest flex items-center justify-center gap-1 shadow-lg shadow-green-500/20 z-50">
                                <span class="material-symbols-outlined text-xs">check_circle</span>
                                Updated
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex-1 pb-2 text-center md:text-left">
                        <h1 class="text-3xl font-headline italic font-bold text-slate-900 dark:text-white">
                            {{ auth()->user()->name }}
                        </h1>
                        <p class="text-primary font-bold text-xs uppercase tracking-[0.2em] mt-1">Scholarly Contributor | UPSC Aspirant</p>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 max-w-xl">Interested in {{ implode(', ', array_slice($userInterests, 0, 3)) }} & more • Researching from New Delhi</p>
                    </div>

                    <div class="pb-2">
                        <a wire:navigate href="{{ route('profile.edit') }}" class="flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 group">
                            <span class="material-symbols-outlined text-sm group-hover:rotate-12 transition-transform">edit</span>
                            Edit Profile
                        </a>
                    </div>
                </div>

                <div class="px-8 py-6 border-t border-sand/30 bg-sand/5 flex flex-wrap justify-center md:justify-start gap-12">
                    <div class="flex flex-col text-center md:text-left">
                        <span class="text-2xl font-headline italic font-bold text-primary">{{ $interestsCount }}</span>
                        <span class="text-[9px] uppercase tracking-[0.2em] text-slate-400 font-bold">Interests Followed</span>
                    </div>
                    <div class="flex flex-col text-center md:text-left">
                        <span class="text-2xl font-headline italic font-bold text-primary">{{ $modulesCompletedCount }}</span>
                        <span class="text-[9px] uppercase tracking-[0.2em] text-slate-400 font-bold">Modules Completed</span>
                    </div>
                    <div class="flex flex-col text-center md:text-left">
                        <span class="text-2xl font-headline italic font-bold text-primary">{{ $contributionsCount }}</span>
                        <span class="text-[9px] uppercase tracking-[0.2em] text-slate-400 font-bold">Contributions</span>
                    </div>
                </div>
            </div>
            
            <!-- Membership Status Section -->
            <section class="mt-8 transition-all duration-500">
                @if($isMember)
                    <!-- Active Member View -->
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary/5 via-sand/20 to-white border border-primary/20 p-6 sm:p-8 flex flex-col md:flex-row items-center gap-6 shadow-sm group">
                        <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                            <span class="material-symbols-outlined text-8xl text-primary">workspace_premium</span>
                        </div>
                        
                        <div class="shrink-0 flex items-center justify-center w-20 h-20 rounded-full bg-primary/10 border-4 border-white shadow-inner">
                            <span class="material-symbols-outlined text-4xl text-primary" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                        </div>
                        
                        <div class="flex-1 text-center md:text-left z-10">
                            <div class="flex flex-col md:flex-row md:items-center gap-2 mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-primary text-white mx-auto md:mx-0">
                                    Verified Scholar
                                </span>
                                @if($userMembership->started_at)
                                    <span class="text-[10px] text-on-surface-variant font-medium opacity-60">Member since {{ $userMembership->started_at->format('M Y') }}</span>
                                @endif
                            </div>
                            <h3 class="text-2xl font-bold text-on-surface mb-2">You're an Active AnthroConnect Member</h3>
                            <p class="text-sm text-on-surface-variant max-w-xl">You have full access to our curated research library, UPSC hub case studies, and advanced community tools. Welcome to the inner circle.</p>
                        </div>

                        <div class="shrink-0 flex flex-col items-center md:items-end gap-3 z-10">
                            <div class="flex -space-x-1">
                                <div class="w-8 h-8 rounded-full bg-sand border-2 border-white flex items-center justify-center text-[8px] font-bold text-primary">LI</div>
                                <div class="w-8 h-8 rounded-full bg-primary border-2 border-white flex items-center justify-center text-[8px] font-bold text-white">RE</div>
                                <div class="w-8 h-8 rounded-full bg-olive border-2 border-white flex items-center justify-center text-[8px] font-bold text-white">UP</div>
                            </div>
                            <button class="bg-primary/5 text-primary border border-primary/10 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-primary hover:text-white transition-all">
                                View Benefits
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Non-Member View -->
                    <div class="relative overflow-hidden rounded-2xl bg-white border border-sand p-6 sm:p-8 flex flex-col md:flex-row items-center gap-8 shadow-sm group">
                        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:rotate-12 transition-transform duration-700">
                            <span class="material-symbols-outlined text-9xl text-slate-900">loyalty</span>
                        </div>
                        
                        <div class="flex-1 text-center md:text-left">
                            <div class="mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-sand text-primary mx-auto md:mx-0">
                                    Standard Experience
                                </span>
                            </div>
                            <h3 class="text-2xl font-bold text-on-surface mb-2">Unlock the Full AnthroConnect Experience</h3>
                            <p class="text-sm text-on-surface-variant max-w-xl mb-6">Gain full access to our proprietary case study bank, previous year UPSC trends, and rare ethnographic field notes by becoming a member today.</p>
                            
                            @if($globalSetting)
                                <div class="flex flex-wrap justify-center md:justify-start gap-4">
                                    @foreach($globalSetting->privileges->take(3) as $privilege)
                                        <div class="flex items-center gap-2 text-[11px] font-medium text-slate-600">
                                            <span class="material-symbols-outlined text-primary text-sm">check_circle</span>
                                            {{ $privilege->privilege }}
                                        </div>
                                    @endforeach
                                    @if($globalSetting->privileges->count() > 3)
                                        <span class="text-[11px] text-slate-400 font-medium italic">and more...</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="shrink-0 flex flex-col items-center gap-3">
                            <div class="text-center mb-1">
                                <span class="text-xs text-slate-400 block uppercase tracking-widest font-bold">Invest in growth</span>
                                @if($globalSetting)
                                    <span class="text-2xl font-headline italic font-bold text-primary">₹ {{ number_format($globalSetting->price_inr, 2) }}</span>
                                @else
                                    <span class="text-sm font-medium text-slate-400">Membership coming soon</span>
                                @endif
                            </div>
                            <button wire:click="openCheckout" class="bg-primary text-white px-8 py-3 rounded-xl font-bold text-sm uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                                Become a member
                            </button>
                        </div>
                    </div>
                @endif
            </section>

            <!-- Continue Learning -->
            <section>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">school</span>
                        Continue Learning
                    </h2>
                    @if(count($continueLearning) === 0)
                        <a wire:navigate href="{{ route('modules.index') }}" class="text-xs font-bold text-primary uppercase tracking-widest hover:underline">Browse Modules</a>
                    @endif
                </div>
 
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($continueLearning as $item)
                        <div class="bg-white border border-sand rounded-[1.5rem] p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-all group">
                            <div>
                                <div class="flex justify-between items-start gap-4 mb-4">
                                    <span class="bg-primary/5 text-primary px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border border-primary/10">
                                        {{ $item['tag'] }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest shrink-0">
                                        {{ $item['progress'] }}%
                                    </span>
                                </div>
 
                                <h3 class="text-lg font-bold mb-2 group-hover:text-primary transition-colors">{{ $item['title'] }}</h3>
                                <p class="text-xs text-slate-500 mb-6 leading-relaxed">{{ $item['description'] }}</p>
                            </div>
 
                            <div class="w-full bg-sand/30 rounded-full h-1.5 mb-6 overflow-hidden">
                                <div class="bg-primary h-full transition-all duration-1000" style="width: {{ $item['progress'] }}%"></div>
                            </div>
 
                            @if($item['lesson_slug'])
                                <a wire:navigate href="{{ route('lessons.show', ['moduleSlug' => $item['slug'], 'lessonSlug' => $item['lesson_slug']]) }}" class="text-primary text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2 hover:translate-x-1 transition-transform">
                                    Resume Module
                                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                </a>
                            @else
                                <a wire:navigate href="{{ route('modules.show', $item['slug']) }}" class="text-primary text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2">
                                    View Module
                                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full py-12 flex flex-col items-center justify-center bg-white border border-dashed border-sand rounded-[2rem] text-center">
                            <span class="material-symbols-outlined text-4xl text-sand mb-4">local_library</span>
                            <h3 class="text-lg font-bold text-slate-900">Start Your Learning Journey</h3>
                            <p class="text-sm text-slate-500 max-w-xs mt-2">Explore our curated anthropology modules and track your progress here.</p>
                            <a wire:navigate href="{{ route('modules.index') }}" class="mt-6 px-6 py-2 bg-primary text-white rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-primary/20">Explore Modules</a>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Recommended -->
            <section>
                <div class="flex justify-between items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">bookmark</span>
                        Recommended for You
                    </h2>
                    <a wire:navigate class="text-xs font-bold text-primary uppercase tracking-widest hover:underline" href="{{ route('library.index') }}">View Library</a>
                </div>
 
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($recommendedItems as $item)
                        <a wire:navigate href="{{ route('library.show', $item['slug']) }}" class="flex gap-4 p-4 bg-white border border-sand rounded-[1.5rem] group cursor-pointer hover:border-primary transition-all hover:shadow-lg hover:shadow-primary/5">
                            <div class="w-24 h-32 bg-sand rounded-xl shrink-0 overflow-hidden shadow-inner border border-sand/50">
                                <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="{{ $item['image'] }}" alt="{{ $item['title'] }}">
                            </div>
 
                            <div class="flex flex-col justify-center min-w-0">
                                <h4 class="font-bold text-lg group-hover:text-primary transition-colors line-clamp-2">
                                    {{ $item['title'] }}
                                </h4>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-1 italic">{{ $item['description'] }}</p>
 
                                <div class="flex flex-wrap items-center gap-3 mt-4 text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                    <span class="flex items-center gap-1.5 px-2 py-1 bg-sand/20 rounded-md">
                                        <span class="material-symbols-outlined text-[14px] text-primary">{{ $item['meta_left_icon'] }}</span>
                                        {{ $item['meta_left'] }}
                                    </span>
                                    <span class="flex items-center gap-1.5 px-2 py-1 bg-sand/20 rounded-md">
                                        <span class="material-symbols-outlined text-[14px] text-primary">{{ $item['meta_right_icon'] }}</span>
                                        {{ $item['meta_right'] }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-8 text-center text-slate-400 italic text-sm">
                            More recommendations arriving as you explore the library.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Explore Humanity -->
            @if($featuredExploreArticle)
                <section>
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">explore</span>
                        Explore Humanity
                    </h2>
    
                    <div class="bg-sand/10 rounded-[2.5rem] border border-sand/50 overflow-hidden grid grid-cols-1 md:grid-cols-2 group hover:border-primary/30 transition-all">
                        <div class="h-64 md:h-auto overflow-hidden">
                            <img
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                src="{{ $featuredExploreArticle->featured_image ? Storage::url($featuredExploreArticle->featured_image) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuAdXPeFId6STTdfqjvR_7DOnmClBQwVMedR4giCsf1gejHnoeh3Lt4fzJ8e6tz_4SZ5JYnKann2Xc-qqzmE9KUmh9SKlthOJz-L1batBjJprKlltJ7vf0-OcwEalDCELeIJYbR_K3e5yxXvSRHivF_gjcIykzLqWZFrlXTxhrUYX03P_m4vDDq5yEy-Ts80MkDAghDkvmpnqURdls2K2JRTuBhyODgkyG3fK46xaI945IgjPM1ITA7qsJ7GGt4Dbx5VRzQ4nYEIRU8' }}"
                                alt="{{ $featuredExploreArticle->title }}"
                            >
                        </div>
    
                        <div class="p-8 sm:p-12 flex flex-col justify-center">
                            <span class="text-primary font-black text-[10px] uppercase tracking-[0.3em] mb-4">Featured Ethnography</span>
                            <h3 class="text-2xl sm:text-3xl font-headline italic font-bold mb-4 leading-tight group-hover:text-primary transition-colors">{{ $featuredExploreArticle->title }}</h3>
                            <p class="text-slate-600 mb-8 font-serif leading-relaxed opacity-80 line-clamp-3">
                                "{{ $featuredExploreArticle->excerpt ?: 'Explore our latest ethnographic findings and scholarly research.' }}"
                            </p>
                            <div class="flex flex-wrap items-center gap-6">
                                <a wire:navigate href="{{ route('explore.show', $featuredExploreArticle->slug) }}" class="bg-primary text-white px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">Read Article</a>
                                <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">timer</span>
                                    {{ $featuredExploreArticle->reading_time_minutes ?: 15 }} min read
                                </span>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <!-- Knowledge Map -->
            <section class="bg-olive/10 rounded-2xl p-6 sm:p-8 border border-olive/20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold mb-4">Interconnected Knowledge Map</h2>
                        <p class="text-slate-700 mb-6">
                            Our proprietary visualization tool allows you to see the cross-disciplinary links between Biological, Social, and Linguistic anthropology.
                        </p>

                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                Visual relationship tracking
                            </li>
                            <li class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                Dynamic branch navigation
                            </li>
                            <li class="flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                Gap analysis for exam prep
                            </li>
                        </ul>

                        <a href="{{ route('knowledge-map.show', ['from' => request()->fullUrl()]) }}" class="inline-block border-2 border-primary text-primary hover:bg-primary hover:text-white px-6 py-2 rounded-lg font-bold transition-all text-decoration-none">
                            Launch Map Viewer
                        </a>
                    </div>

                    <div class="bg-white/50 rounded-xl aspect-square flex items-center justify-center border border-olive/30 relative overflow-hidden">
                        <span class="material-symbols-outlined text-[100px] sm:text-[120px] text-olive/40">hub</span>
                        <div class="absolute top-1/4 left-1/4 h-3 w-3 bg-primary rounded-full animate-pulse"></div>
                        <div class="absolute bottom-1/3 right-1/4 h-3 w-3 bg-primary rounded-full animate-pulse"></div>
                        <div class="absolute top-1/2 right-1/2 h-3 w-3 bg-olive rounded-full"></div>
                    </div>
                </div>
            </section>

            <!-- Community Discussions -->
            <section>
                <div class="flex justify-between items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold">Community Discussions</h2>
                    <button class="text-primary font-medium text-sm whitespace-nowrap">New Topic</button>
                </div>

                <div class="space-y-4">
                    @foreach($discussions as $discussion)
                        <div class="bg-white p-5 rounded-xl border border-sand hover:shadow-md transition-shadow">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                                <h4 class="font-bold text-lg">{{ $discussion['title'] }}</h4>
                                <div class="flex items-center gap-1 text-slate-400 shrink-0">
                                    <span class="material-symbols-outlined text-sm">forum</span>
                                    <span class="text-xs">{{ $discussion['replies'] }}</span>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                <div class="flex -space-x-2">
                                    @foreach($discussion['avatars'] as $avatar)
                                        <div class="w-6 h-6 rounded-full border-2 border-white {{ $loop->first ? 'bg-sand text-slate-700' : 'bg-primary text-white' }} flex items-center justify-center text-[8px] font-bold">
                                            {{ $avatar }}
                                        </div>
                                    @endforeach
                                </div>

                                <span class="text-xs text-slate-400 italic">
                                    {{ $discussion['latest'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- UPSC Hub -->
            <section class="border-t border-sand pt-12">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h2 class="text-2xl font-bold">UPSC Anthropology Hub</h2>
                        <p class="text-sm text-slate-500">Curated resources for Optional Paper I & II</p>
                    </div>

                    <button class="bg-primary/10 text-primary px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined">description</span>
                        Download Syllabus & Trend Analysis
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($upscResources as $resource)
                        <div class="bg-white p-6 rounded-xl border-l-4 {{ $resource['border'] }}">
                            <h5 class="font-bold text-slate-900 mb-2">{{ $resource['title'] }}</h5>
                            <p class="text-xs text-slate-500 mb-4">{{ $resource['description'] }}</p>
                            <a class="text-primary text-xs font-bold uppercase tracking-widest" href="#">{{ $resource['cta'] }}</a>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <aside class="lg:col-span-3 space-y-8">
            <!-- My Interests -->
            <div class="bg-white p-6 rounded-2xl border border-sand shadow-sm">
                <h3 class="font-bold text-sm uppercase tracking-widest text-slate-900 mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-xl">interests</span>
                    My Interests
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($userInterests as $interest)
                        <span class="px-3 py-1.5 bg-primary/5 text-primary rounded-xl text-[10px] font-bold uppercase tracking-wider border border-primary/10 hover:bg-primary hover:text-white transition-all cursor-pointer">
                            {{ $interest }}
                        </span>
                    @endforeach
                    <button class="px-3 py-1.5 bg-sand/30 text-slate-500 rounded-xl text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 hover:bg-sand transition-all">
                        <span class="material-symbols-outlined text-xs">add</span>
                        Add More
                    </button>
                </div>
            </div>

            <!-- Knowledge Map Discovery -->
            <div class="bg-white p-6 rounded-2xl border border-sand shadow-sm overflow-hidden relative group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-colors"></div>
                <h3 class="font-bold text-sm uppercase tracking-widest text-slate-900 mb-6 flex items-center gap-2 relative z-10">
                    <span class="material-symbols-outlined text-primary text-xl">account_tree</span>
                    Discovery Map
                </h3>
                
                <div class="relative flex flex-col items-center py-4">
                    <div class="relative w-32 h-32 bg-sand/20 rounded-full border border-dashed border-primary/30 flex items-center justify-center">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-primary/20 border-2 border-primary/50 flex items-center justify-center text-primary animate-pulse">
                                <span class="material-symbols-outlined text-xl">hub</span>
                            </div>
                        </div>
                        <!-- Decorative nodes -->
                        <div class="absolute top-4 right-6 w-2 h-2 rounded-full bg-primary shadow-sm shadow-primary/50"></div>
                        <div class="absolute bottom-8 left-4 w-3 h-3 rounded-full bg-primary shadow-sm shadow-primary/50"></div>
                        <div class="absolute top-1/2 left-2 w-1.5 h-1.5 rounded-full bg-olive opacity-40"></div>
                    </div>
                    <div class="mt-6 text-center">
                        <p class="text-xs font-black text-slate-900">{{ $discoveredNodes }} Nodes Discovered</p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-1">Across {{ $majorBranches }} Major Branches</p>
                    </div>
                    <a href="{{ route('knowledge-map.show', ['from' => request()->fullUrl()]) }}" class="w-full mt-6 py-3 bg-primary text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all text-center text-decoration-none">
                        Explore Map
                    </a>
                </div>
            </div>

            <!-- Recent Badges -->
            <div class="bg-primary/5 rounded-2xl p-6 border border-primary/10">
                <h3 class="font-bold text-sm uppercase tracking-widest text-primary mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl">workspace_premium</span>
                    Recent Badges
                </h3>
                <div class="flex gap-4 justify-center">
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-primary shadow-sm border border-sand">
                            <span class="material-symbols-outlined text-xl">verified</span>
                        </div>
                        <span class="text-[8px] font-bold text-center uppercase tracking-tighter text-slate-500">Fieldwork<br/>Ready</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 opacity-50">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-slate-400 shadow-sm border border-sand">
                            <span class="material-symbols-outlined text-xl">history_edu</span>
                        </div>
                        <span class="text-[8px] font-bold text-center uppercase tracking-tighter text-slate-400">Theory<br/>Master</span>
                    </div>
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-primary shadow-sm border border-sand">
                            <span class="material-symbols-outlined text-xl">group_work</span>
                        </div>
                        <span class="text-[8px] font-bold text-center uppercase tracking-tighter text-slate-500">Contributor<br/>Lvl 2</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-sand shadow-sm">
                <h3 class="font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">bolt</span>
                    Quick Actions
                </h3>

                <div class="space-y-3">
                    @foreach($quickActions as $action)
                        <button class="w-full text-left p-3 rounded-lg hover:bg-sand/30 flex items-center gap-3 transition-colors">
                            <span class="material-symbols-outlined text-primary">{{ $action['icon'] }}</span>
                            <span class="text-sm font-medium">{{ $action['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-sand">
                <h3 class="font-bold mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">history</span>
                    Recent Activity
                </h3>

                <div class="space-y-6">
                    @foreach($recentActivities as $activity)
                        <div class="flex gap-4">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-sand flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-sm">{{ $activity['icon'] }}</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold">{{ $activity['text'] }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="relative overflow-hidden rounded-xl bg-olive p-6 text-white group cursor-pointer">
                <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-8xl opacity-10 group-hover:rotate-12 transition-transform">library_books</span>
                <h3 class="font-bold mb-2">Research Library</h3>
                <p class="text-xs opacity-80 mb-4">Access 50,000+ academic papers and rare ethnographic field notes.</p>
                <div class="flex items-center gap-2 text-xs font-bold">
                    Browse Collection
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </div>
            </div>
        </aside>
    </div>

</div>
