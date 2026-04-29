<div>
    <!-- Session Messages -->
    @if(session('error'))
    <div class="max-w-7xl mx-auto px-6 pt-8">
        <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
            <span class="material-symbols-outlined text-red-500">lock</span>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="max-w-7xl mx-auto px-6 pt-8">
        <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
            <span class="material-symbols-outlined text-green-500">check_circle</span>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif
    <!-- Hero Section -->
    <section class="relative h-[70vh] min-h-[500px] w-full overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-top transition-transform duration-700 hover:scale-105" 
             data-alt="Vibrant traditional cultural festival with people in ceremonial dress" 
             style="background-image: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.7)), url('https://lh3.googleusercontent.com/aida-public/AB6AXuBawWtNwzl9sMi8WPWBCDFvNPeKFMCVyM05exYM0o8RpAgoaUioIGOPTVBk05CNqnUF0st_N3p8ighepNHv8Uqz8j-ABivd6_8qtg1A4hkZ0JN7aqtjgaIiXIu3rhBvm21ES0i_kASvF4uCsf5OgrxhqD6xvffc5bsH_CzCagTsxnkoBCsPtuDgCXu_uYo5L8AEloAe-5WT3OuZktzXMSCJSu4UGMUkI5PR9DFLntkNNq2cdaZB-Ozx5hsrC-lJBkukkV71uzwg5vw')">
        </div>
        <div class="relative h-full max-w-7xl mx-auto px-6 flex flex-col items-center justify-center text-center">
            <span class="bg-primary/90 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-6">Editorial</span>
            <h1 class="font-serif text-5xl md:text-7xl font-bold text-white mb-6 leading-tight max-w-4xl">Explore Humanity</h1>
            <p class="text-white/90 text-lg md:text-xl max-w-2xl font-light leading-relaxed">
                Discover anthropology through immersive stories about culture, identity, traditions, and human societies around the world.
            </p>
            <a wire:navigate href="{{ route('modules.index') }}" class="mt-10 bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-xl font-bold transition-all transform hover:-translate-y-1 shadow-lg shadow-primary/20 inline-block">
                Start Learning
            </a>
        </div>
    </section>

    <!-- Theme Navigation Pills -->
    @if($tagGroups->count() > 0)
    <div class="bg-stone-200/20 dark:bg-stone-900 py-6 border-b border-stone-200/50 dark:border-primary/10">
        <div class="max-w-7xl mx-auto px-6 space-y-4">
            @foreach($tagGroups as $group)
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-stone-400 whitespace-nowrap min-w-[80px]">{{ $group->name }}</span>
                    <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1 flex-1">
                        @if($loop->first)
                        <button wire:click="setTag('')" 
                           class="whitespace-nowrap px-4 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-widest transition-colors {{ !$tagId ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-white dark:bg-primary/10 border border-stone-200 dark:border-primary/20 hover:bg-stone-200/30 text-stone-600 dark:text-stone-100' }}">
                            All
                        </button>
                        @endif
                        @foreach($group->activeTags as $tag)
                            <button wire:click="setTag({{ $tag->id }})" 
                               class="whitespace-nowrap px-4 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-widest transition-colors {{ $tagId == $tag->id ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-white dark:bg-primary/10 border border-stone-200 dark:border-primary/20 hover:bg-stone-200/30 text-stone-600 dark:text-stone-100' }}">
                                {{ $tag->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div>
        <!-- Featured Stories Slider -->
        <section class="max-w-7xl mx-auto px-6 py-16" 
                 x-data="{ 
                    active: 0, 
                    count: {{ $featuredArticles->count() }},
                    next() { this.active = (this.active + 1) % this.count },
                    prev() { this.active = (this.active - 1 + this.count) % this.count },
                    init() {
                        if(this.count > 1) {
                            setInterval(() => { this.next() }, 8000);
                        }
                    }
                 }">
            @if($featuredArticles->isNotEmpty())
                <div class="relative group">
                    <div class="overflow-hidden rounded-[32px] border border-stone-200 dark:border-primary/10 shadow-sm bg-white dark:bg-primary/5">
                        <div class="flex transition-transform duration-700 ease-in-out" 
                             :style="`transform: translateX(-${active * 100}%)`"
                             style="width: {{ $featuredArticles->count() * 100 }}%">
                            
                            @foreach($featuredArticles as $article)
                                @php 
                                    $isRestricted = !$article->canAccess(Auth::user()); 
                                    $restrictedClick = Auth::check() 
                                        ? "\$dispatch('open-upgrade-modal')" 
                                        : "window.location.href='" . route('login') . "'";
                                @endphp
                                
                                <div class="w-full flex-shrink-0 grid lg:grid-cols-2 gap-0 lg:gap-12 items-center relative min-h-[500px]">
                                    <!-- Image Column -->
                                    <div class="h-[400px] lg:h-[600px] bg-cover bg-center transition-all duration-700 {{ $isRestricted ? 'blur-xl grayscale opacity-50' : '' }}" 
                                         style="background-image: url('{{ $article->featured_image ? Storage::url($article->featured_image) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuB1KrzCmQu1SRay1g3xwXb34xOECNU7hsNulEoKpJxrQI33s6lcR1kxVQWaAqd2jWREeOYYJUKAQAYHlNJRTbkAABcQVSa9Q1WTvE-0M5SWd9xohvvS8_i0-mZ4FMzzSQwAvEA1L1y9wG4xD70lA7gKCrnCw1GprAFAKXceoz2eyK9Sj1sneWzVTyAxoOjH-9QnJtovBZQjNYfh505cm4BtDNZQQ1eqxonbhvV99UXuLrKo4vJLer0BJzVRkJZSnGPKy4ACpmz6Nak' }}')">
                                    </div>
                                    
                                    <!-- Content Column -->
                                    <div class="p-8 lg:p-12 transition-all duration-700 {{ $isRestricted ? 'blur-md opacity-30 select-none' : '' }}">
                                        <div class="flex items-center gap-3 mb-4">
                                            <span class="text-primary font-bold uppercase tracking-widest text-[10px] bg-primary/10 px-2 py-0.5 rounded">
                                                {{ $article->tags->first() ? $article->tags->first()->name : 'Feature Story' }}
                                            </span>
                                            @if($article->is_members_only)
                                                <span class="text-orange-800 font-bold uppercase tracking-widest text-[10px] bg-orange-100 px-2 py-0.5 rounded flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[12px]" style="font-variation-settings: 'FILL' 1">workspace_premium</span>
                                                    Members Only
                                                </span>
                                            @endif
                                        </div>

                                        <h2 class="font-headline italic text-4xl lg:text-5xl font-bold mb-6 leading-tight text-stone-900 dark:text-stone-100">
                                            {{ $article->title }}
                                        </h2>
                                        
                                        <p class="text-stone-600 dark:text-stone-400 text-lg mb-8 leading-relaxed line-clamp-3">
                                            {{ $article->excerpt }}
                                        </p>
                                        
                                        <div class="flex items-center gap-4 mb-8">
                                            <div class="size-12 rounded-full bg-cover bg-center border border-stone-200" 
                                                 style="background-image: url('{{ $article->creator->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($article->creator->name ?? 'A') }}')">
                                            </div>
                                            <div>
                                                <p class="font-bold text-stone-900 dark:text-stone-100">{{ $article->creator->name ?? 'AnthroConnect Editorial' }}</p>
                                                <p class="text-xs text-stone-500 flex items-center gap-2">
                                                    {{ $article->published_at ? $article->published_at->format('M d, Y') : 'Recently' }} • {{ $article->reading_time_minutes ?? 5 }} min read
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <a href="{{ route('explore.show', $article->slug) }}" 
                                           @if($isRestricted) @click.prevent="{!! $restrictedClick !!}" @endif
                                           class="inline-block bg-primary text-white px-10 py-4 rounded-xl font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                                            Read Story
                                        </a>
                                    </div>

                                    @if($isRestricted)
                                        <div class="absolute inset-0 z-10 flex flex-col items-center justify-center p-8 text-center bg-black/5 backdrop-blur-[2px]">
                                            <div class="bg-white/95 dark:bg-stone-900/95 p-10 rounded-[32px] shadow-2xl border border-stone-100 dark:border-stone-800 max-w-md flex flex-col items-center animate-in zoom-in-95 duration-500">
                                                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6">
                                                    <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                                                </div>
                                                <h3 class="font-headline italic text-2xl font-bold text-stone-900 dark:text-stone-100 mb-3">Premium Scholarly Content</h3>
                                                <p class="text-stone-500 text-sm mb-8 leading-relaxed">
                                                    This featured narrative is exclusively available to our community of registered scholars. Join us to unlock the full archive.
                                                </p>
                                                <button @click.stop="{!! $restrictedClick !!}" class="w-full bg-primary text-white px-8 py-4 rounded-xl font-bold hover:scale-[1.02] transition-all shadow-xl shadow-primary/25 flex items-center justify-center gap-2">
                                                    @auth
                                                        <span class="material-symbols-outlined text-sm">upgrade</span>
                                                        Upgrade to Access
                                                    @else
                                                        <span class="material-symbols-outlined text-sm">login</span>
                                                        Login to Read
                                                    @endauth
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Slider Navigation -->
                    @if($featuredArticles->count() > 1)
                        <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
                            @foreach($featuredArticles as $index => $article)
                                <button @click="active = {{ $index }}" 
                                        class="h-2 rounded-full transition-all duration-300"
                                        :class="active === {{ $index }} ? 'w-8 bg-primary' : 'w-2 bg-stone-300 hover:bg-stone-400'"></button>
                            @endforeach
                        </div>

                        <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/80 backdrop-blur border border-stone-200 flex items-center justify-center text-stone-900 shadow-lg hover:bg-white transition-all opacity-0 group-hover:opacity-100 -translate-x-4 group-hover:translate-x-0">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                        <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/80 backdrop-blur border border-stone-200 flex items-center justify-center text-stone-900 shadow-lg hover:bg-white transition-all opacity-0 group-hover:opacity-100 translate-x-4 group-hover:translate-x-0">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    @endif
                </div>
            @else
                <div class="bg-stone-100 p-12 text-center rounded-2xl border border-stone-200">
                    <span class="material-symbols-outlined text-4xl text-stone-400 mb-2">auto_stories</span>
                    <h2 class="font-headline italic text-2xl font-bold text-stone-900 mb-2">The archives are silent</h2>
                    <p class="text-stone-500">No featured narrative matches your current exploration.</p>
                </div>
            @endif
        </section>n>

        <!-- Story Grid -->
        <section class="max-w-7xl mx-auto px-6 py-16 border-t border-stone-200 dark:border-primary/10">
            <h3 class="font-serif text-3xl font-bold mb-10 text-stone-900">Latest Explorations</h3>
            
            @if($articles->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
                @foreach($articles as $article)
                @php 
                    $isRestricted = !$article->canAccess(Auth::user()); 
                    $restrictedClick = Auth::check() 
                        ? "\$dispatch('open-upgrade-modal')" 
                        : "window.location.href='" . route('login') . "'";
                @endphp
                
                <div @if($isRestricted) @click="{!! $restrictedClick !!}" @endif class="group block cursor-pointer">
                    @if(!$isRestricted)
                        <a href="{{ route('explore.show', $article->slug) }}" class="block">
                    @endif
                    
                    <div class="relative aspect-[4/3] rounded-xl overflow-hidden mb-6 bg-stone-100 shadow-sm">
                        @if($article->featured_image)
                            <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 {{ $isRestricted ? 'blur-md grayscale-[0.5]' : '' }}" 
                                 src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" />
                        @else
                            <div class="w-full h-full flex items-center justify-center opacity-10">
                                <span class="material-symbols-outlined text-4xl">image</span>
                            </div>
                        @endif

                        @if($isRestricted)
                            <div class="absolute inset-0 bg-black/20 flex flex-col items-center justify-center p-6 text-center backdrop-blur-[2px]">
                                <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg mb-3 group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">lock</span>
                                </div>
                                <span class="bg-primary text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-md">Members Only</span>
                            </div>
                        @endif
                    </div>

                    <h4 class="font-serif text-2xl font-bold mb-3 group-hover:text-primary transition-colors text-stone-900">
                        {{ $article->title }}
                    </h4>
                    <p class="text-stone-600 dark:text-stone-400 mb-4 line-clamp-3">
                        {{ $article->excerpt }}
                    </p>
                    <div class="flex items-center justify-between text-xs text-stone-500 font-medium">
                        <div class="flex items-center gap-2">
                            <span>{{ $article->published_at ? $article->published_at->format('M d, Y') : 'Unknown' }}</span> 
                            <span>•</span> 
                            <span>{{ $article->reading_time_minutes ?? 5 }} min read</span>
                        </div>
                        @if($article->is_members_only)
                            <span class="text-primary font-bold uppercase tracking-widest text-[9px] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[10px]" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                                Members Only
                            </span>
                        @elseif($article->tags->isNotEmpty())
                            <span class="text-stone-400 font-bold uppercase tracking-widest text-[10px]">{{ $article->tags->first()->name }}</span>
                        @endif
                    </div>

                    @if(!$isRestricted)
                        </a>
                    @endif
                </div>
                @endforeach
            </div>
            
            @if($articles->hasPages())
                <div class="mt-16 text-center">
                    {{ $articles->links() }}
                </div>
            @endif
            
            @else
            <div class="bg-stone-100 p-12 text-center rounded-2xl border border-stone-200">
                <p class="text-stone-500">End of the archive. No more articles to show right now.</p>
            </div>
            @endif
        </section>
    </div>
</div>
