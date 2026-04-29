<div class="upsc-hub-page min-h-screen py-8">
    <style>
        :root {
            --ac-primary: #9a3412;
            --ac-primary-dark: #7c2d12;
            --ac-stone-50: #fafaf9;
            --ac-stone-200: #e7e5e4;
            --ac-stone-600: #57534e;
        }
        .upsc-hub-page h1, .upsc-hub-page h2, .upsc-hub-page h3, .upsc-hub-page h4, .upsc-hub-page h5, .upsc-hub-page h6 {
            font-family: 'Lora', serif;
            font-style: italic;
        }
        .upsc-hero {
            background-color: white;
            border: 1px solid var(--ac-stone-200);
            border-radius: 2rem;
            padding: 3rem;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
            background-image: radial-gradient(circle at 2px 2px, rgba(154, 52, 18, 0.05) 1px, transparent 0);
            background-size: 24px 24px;
        }
        @media (min-width: 1024px) {
            .upsc-hero {
                padding: 4rem;
            }
        }
        .upsc-hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(154, 52, 18, 0.03) 0%, transparent 70%);
            z-index: 0;
        }
        .upsc-card {
            border: 1px solid var(--ac-stone-200);
            border-radius: 1.25rem;
            transition: all 0.3s ease;
            background: white;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .upsc-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            border-color: var(--ac-primary);
        }
        .section-title {
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--ac-stone-200);
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 80px;
            height: 2px;
            background-color: var(--ac-primary);
        }
        .badge-upsc {
            background-color: rgba(154, 52, 18, 0.1);
            color: var(--ac-primary);
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.35rem 0.65rem;
            border-radius: 0.5rem;
        }
        .thinker-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin: 0 auto 1rem;
            border: 3px solid var(--ac-stone-50);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .resource-icon {
            width: 40px;
            height: 40px;
            background: var(--ac-stone-50);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ac-primary);
            margin-bottom: 1rem;
        }
    </style>

    <div class="max-w-[1400px] mx-auto px-6 lg:px-20">
        
        <!-- HERO -->
        <div class="upsc-hero shadow-sm">
            <div class="flex flex-col lg:flex-row items-center gap-8">
                <div class="lg:w-2/3">
                    <span class="badge-upsc mb-4 inline-block">Curated for Excellence</span>
                    <h1 class="text-4xl lg:text-6xl font-bold mb-4 text-gray-900">UPSC Anthropology Hub</h1>
                    <p class="text-lg lg:text-xl text-gray-600 mb-8 font-sans">
                        Your strategic command center for UPSC Anthropology preparation. We've aggregated every high-yield resource, thinker, and concept into a single, structured dashboard.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="#modules" class="bg-[#9a3412] hover:bg-[#7c2d12] text-white font-bold uppercase tracking-wider text-xs px-8 py-4 rounded-xl shadow-lg shadow-orange-900/20 transition-all">
                            Start Prep Journey
                        </a>
                        <a wire:navigate href="{{ route('exams.index') }}" class="bg-stone-900 text-white font-bold uppercase tracking-wider text-xs px-8 py-4 rounded-xl shadow-lg shadow-stone-900/20 transition-all">
                            Practice Exams
                        </a>
                        <a wire:navigate href="{{ route('knowledge-map.show') }}" class="bg-white border border-gray-200 text-gray-700 font-bold uppercase tracking-wider text-xs px-8 py-4 rounded-xl hover:bg-gray-50 transition-all">
                            Knowledge Map
                        </a>
                    </div>
                </div>
                <div class="hidden lg:block lg:w-1/3 text-center">
                    <span class="material-symbols-outlined text-[#9a3412] opacity-10" style="font-size: 200px;">account_balance</span>
                </div>
            </div>
        </div>

        <!-- SEARCH -->
        <div class="mb-12">
            <div class="max-w-3xl mx-auto">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                    <input type="text"
                           class="w-full bg-white border border-gray-200 rounded-2xl pl-12 pr-6 py-4 text-lg focus:ring-2 focus:ring-[#9a3412] focus:border-transparent transition-all shadow-sm outline-none"
                           placeholder="Search thinkers, modules, or core concepts..."
                           wire:model.live.debounce.400ms="search">
                </div>
            </div>
        </div>

        @if($modules->count() > 0)
        <!-- MODULES -->
        <div id="modules" class="mb-16">
            <h3 class="section-title text-2xl font-bold text-gray-900">Core UPSC Modules</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($modules as $module)
                    <div class="upsc-card p-6">
                        <div class="mb-6">
                            @if($module->cover_image)
                                <img src="{{ Storage::url($module->cover_image) }}" class="rounded-2xl w-full mb-4 object-cover h-48">
                            @else
                                <div class="rounded-2xl bg-gray-50 w-full mb-4 flex items-center justify-center h-48 border border-gray-100">
                                    <span class="material-symbols-outlined text-gray-300 text-4xl">school</span>
                                </div>
                            @endif
                            <h5 class="text-xl font-bold mb-2 text-gray-900">{{ $module->title }}</h5>
                            <p class="text-gray-500 text-sm leading-relaxed">
                                {{ Str::limit($module->short_description ?: $module->overview, 120) }}
                            </p>
                        </div>
                        <div class="flex justify-between items-center mt-auto pt-4 border-t border-gray-50">
                            <span class="badge-upsc">Module</span>
                            <a href="{{ route('modules.show', $module->slug) }}" class="text-[#9a3412] font-bold text-xs uppercase tracking-widest hover:underline">
                                Enter Module
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($exploreItems->count() > 0)
        <!-- FEATURED COLLECTIONS (EXPLORE) -->
        <div class="mb-16">
            <h3 class="section-title text-2xl font-bold text-gray-900">High-Yield Collections</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($exploreItems as $article)
                    <div class="upsc-card p-6">
                        <div class="mb-6">
                            @if($article->featured_image)
                                <img src="{{ Storage::url($article->featured_image) }}" class="rounded-2xl w-full mb-4 object-cover h-48">
                            @else
                                <div class="rounded-2xl bg-gray-50 w-full mb-4 flex items-center justify-center h-48 border border-gray-100">
                                    <span class="material-symbols-outlined text-gray-300 text-4xl">explore</span>
                                </div>
                            @endif
                            <h5 class="text-xl font-bold mb-2 text-gray-900 flex items-center gap-2">
                                {{ $article->title }}
                                @if($article->is_members_only)
                                    <span class="material-symbols-outlined text-gray-400 text-sm" title="Members Only">lock</span>
                                @endif
                            </h5>
                            <p class="text-gray-500 text-sm leading-relaxed">
                                {{ Str::limit($article->excerpt, 120) }}
                            </p>
                        </div>
                        <div class="flex justify-between items-center mt-auto pt-4 border-t border-gray-50">
                            <span class="badge-upsc" style="background-color: rgba(96, 108, 56, 0.1); color: #606c38;">Collection</span>
                            <a href="{{ route('explore.show', $article->slug) }}" class="text-[#9a3412] font-bold text-xs uppercase tracking-widest hover:underline">
                                Read Collection →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($anthropologists->count() > 0)
        <!-- THINKERS -->
        <div class="mb-16">
            <h3 class="section-title text-2xl font-bold text-gray-900">Foundational Thinkers</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($anthropologists as $item)
                    <div class="upsc-card p-8 text-center">
                        @if($item->profile_image)
                            <img src="{{ Storage::url($item->profile_image) }}" class="thinker-img">
                        @else
                            <div class="thinker-img bg-gray-50 d-flex flex items-center justify-center border border-gray-100 mx-auto mb-4">
                                <span class="material-symbols-outlined text-gray-300 text-4xl">person</span>
                            </div>
                        @endif
                        <h6 class="text-lg font-bold mb-1 text-gray-900">{{ $item->name }}</h6>
                        <p class="text-gray-400 text-xs uppercase tracking-widest mb-6">Thinker / Theorist</p>
                        <a href="{{ route('encyclopedia.anthropologists.show', $item->slug) }}" class="inline-block border border-[#9a3412] text-[#9a3412] hover:bg-[#9a3412] hover:text-white font-bold text-[10px] uppercase tracking-widest px-6 py-2.5 rounded-lg transition-all">
                            Biography
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
            @if($concepts->count() > 0)
            <!-- CONCEPTS -->
            <div>
                <h3 class="section-title text-2xl font-bold text-gray-900">UPSC Core Concepts</h3>
                <div class="space-y-4">
                    @foreach($concepts as $item)
                        <a href="{{ route('encyclopedia.concepts.show', $item->slug) }}" class="block group">
                            <div class="upsc-card p-4 flex items-center gap-4 group-hover:border-[#9a3412] transition-colors">
                                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-[#9a3412]">
                                    <span class="material-symbols-outlined">label_important</span>
                                </div>
                                <div class="flex-1">
                                    <h6 class="font-bold text-gray-900 mb-0">{{ $item->title }}</h6>
                                    <p class="text-gray-400 text-xs">{{ Str::limit($item->description, 80) }}</p>
                                </div>
                                <span class="material-symbols-outlined text-gray-300 group-hover:text-[#9a3412] transition-colors">chevron_right</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($theories->count() > 0)
            <!-- THEORIES -->
            <div>
                <h3 class="section-title text-2xl font-bold text-gray-900">Major Theories</h3>
                <div class="space-y-4">
                    @foreach($theories as $item)
                        <a href="{{ route('encyclopedia.theories.show', $item->slug) }}" class="block group">
                            <div class="upsc-card p-4 flex items-center gap-4 group-hover:border-[#9a3412] transition-colors">
                                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-[#606c38]">
                                    <span class="material-symbols-outlined">psychology</span>
                                </div>
                                <div class="flex-1">
                                    <h6 class="font-bold text-gray-900 mb-0">{{ $item->title }}</h6>
                                    <p class="text-gray-400 text-xs">{{ Str::limit($item->description, 80) }}</p>
                                </div>
                                <span class="material-symbols-outlined text-gray-300 group-hover:text-[#9a3412] transition-colors">chevron_right</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        @if($resources->count() > 0)
        <!-- LIBRARY -->
        <div class="mb-16">
            <h3 class="section-title text-2xl font-bold text-gray-900">UPSC Library Resources</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($resources as $item)
                    <div class="upsc-card p-6">
                        <div class="flex gap-4 mb-4">
                            <div class="w-20 h-28 flex-shrink-0">
                                @if($item->cover_image_path)
                                    <img src="{{ Storage::url($item->cover_image_path) }}" class="w-full h-full rounded shadow-sm object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-50 rounded flex items-center justify-center border border-gray-100">
                                        <span class="material-symbols-outlined text-gray-200">menu_book</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h6 class="font-bold text-gray-900 mb-1 leading-tight flex items-center gap-2">
                                    {{ $item->title }}
                                    @if($item->access_type === 'member_only')
                                        <span class="material-symbols-outlined text-gray-400 text-xs" title="Members Only">lock</span>
                                    @endif
                                </h6>
                                <p class="text-gray-400 text-[10px] uppercase tracking-wider mb-2">{{ $item->author_display }}</p>
                                <p class="text-gray-500 text-xs italic line-clamp-2">{{ $item->abstract ?: $item->description }}</p>
                            </div>
                        </div>
                        <a href="{{ route('library.show', $item->slug) }}" class="mt-auto block text-center border border-[#9a3412] text-[#9a3412] hover:bg-[#9a3412] hover:text-white font-bold text-[10px] uppercase tracking-widest px-6 py-3 rounded-xl transition-all">
                            Access Resource
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($modules->count() == 0 && $exploreItems->count() == 0 && $anthropologists->count() == 0 && $concepts->count() == 0 && $theories->count() == 0 && $resources->count() == 0)
            <div class="text-center py-20">
                <span class="material-symbols-outlined text-8xl text-gray-200 mb-6">search_off</span>
                <h3 class="text-2xl font-bold text-gray-400">No UPSC content found matching your search.</h3>
                <p class="text-gray-500 mt-2">Try a different keyword or browse our general anthropology content.</p>
            </div>
        @endif

    </div>
</div>

