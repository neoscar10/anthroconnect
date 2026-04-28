<div class="space-y-12 pb-20">
    <!-- Hero Section -->
    <section class="relative overflow-hidden rounded-3xl bg-orange-100/30 dark:bg-orange-950/20 border border-orange-200/50 dark:border-orange-800/30 py-20 px-8 sm:px-12 ethno-pattern">
        <div class="relative z-10 text-center max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-headline font-bold text-stone-900 dark:text-stone-50 mb-6 italic">Anthropology Encyclopedia</h1>
            <p class="text-lg md:text-xl text-stone-600 dark:text-stone-400 font-medium leading-relaxed">
                Explore the thinkers, theories, and key concepts that shaped the discipline of anthropology. A comprehensive reference for students and scholars.
            </p>
        </div>
    </section>

    <!-- Search & Filter Section -->
    <section class="space-y-6 py-4 px-4">
        <div class="relative max-w-5xl mx-auto group">
            <span class="absolute inset-y-0 left-5 flex items-center text-stone-400 group-focus-within:text-primary transition-colors">
                <span class="material-symbols-outlined text-3xl">search</span>
            </span>
            <input 
                wire:model.live.debounce.300ms="search"
                class="block w-full pl-16 pr-6 py-5 text-lg border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 rounded-2xl shadow-sm focus:ring-4 focus:ring-primary/10 transition-all outline-none" 
                placeholder="Search for anthropologists, theories, or concepts..." 
                type="text"
            />
            @if($search)
                <button wire:click="$set('search', '')" class="absolute inset-y-0 right-5 flex items-center text-stone-400 hover:text-error transition-colors">
                    <span class="material-symbols-outlined">cancel</span>
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:flex md:flex-wrap gap-4 justify-center items-center max-w-5xl mx-auto">
            <!-- Category Filter -->
            <div class="flex items-center gap-3 bg-white dark:bg-stone-900 px-5 py-3 rounded-xl border border-stone-200 dark:border-stone-800 shadow-sm group">
                <span class="text-[10px] font-extrabold text-primary uppercase tracking-widest">Category</span>
                <select wire:model.live="category" class="border-none bg-transparent text-sm font-bold focus:ring-0 p-0 pr-8 cursor-pointer text-stone-700 dark:text-stone-300">
                    <option>All Categories</option>
                    <option>Anthropologists</option>
                    <option>Concepts</option>
                    <option>Theories</option>
                </select>
            </div>

            @foreach($tagGroups as $group)
                <div class="flex items-center gap-3 bg-white dark:bg-stone-900 px-5 py-3 rounded-xl border border-stone-200 dark:border-stone-800 shadow-sm">
                    <span class="text-[10px] font-extrabold text-primary uppercase tracking-widest">{{ $group->name }}</span>
                    <select wire:change="setTag('{{ $group->id }}', $event.target.value)" class="border-none bg-transparent text-sm font-bold focus:ring-0 p-0 pr-8 cursor-pointer text-stone-700 dark:text-stone-300">
                        <option value="">All {{ $group->name }}</option>
                        @foreach($group->activeTags as $tag)
                            <option value="{{ $tag->slug }}" {{ ($tagFilters[$group->id] ?? null) === $tag->slug ? 'selected' : '' }}>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach

            <!-- Region Filter -->
            <div class="flex items-center gap-3 bg-white dark:bg-stone-900 px-5 py-3 rounded-xl border border-stone-200 dark:border-stone-800 shadow-sm">
                <span class="text-[10px] font-extrabold text-primary uppercase tracking-widest">Region</span>
                <select wire:model.live="region" class="border-none bg-transparent text-sm font-bold focus:ring-0 p-0 pr-8 cursor-pointer text-stone-700 dark:text-stone-300">
                    <option>Global</option>
                    @foreach($allRegions as $reg)
                        <option value="{{ $reg }}">{{ $reg }}</option>
                    @endforeach
                </select>
            </div>

            @if($isFiltered)
                <button wire:click="resetFilters" class="text-[10px] font-bold text-error uppercase tracking-widest hover:underline flex items-center gap-1 mt-2 sm:mt-0">
                    <span class="material-symbols-outlined text-sm">restart_alt</span>
                    Reset Filters
                </button>
            @endif
        </div>
    </section>

    <!-- Search Results / Landing Sections -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($isSearching)
        <div class="space-y-16">
            @if($anthropologists->count() > 0)
                <section>
                    <h2 class="text-2xl font-headline font-bold text-stone-900 dark:text-stone-100 mb-8 flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">groups</span>
                        Anthropologists ({{ $anthropologists->count() }})
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($anthropologists as $person)
                            @include('livewire.pages.encyclopedia.partials.anthropologist-card', ['person' => $person])
                        @endforeach
                    </div>
                </section>
            @endif

            @if($concepts->count() > 0)
                <section>
                    <h2 class="text-2xl font-headline font-bold text-stone-900 dark:text-stone-100 mb-8 flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">psychology</span>
                        Core Concepts ({{ $concepts->count() }})
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($concepts as $concept)
                            @include('livewire.pages.encyclopedia.partials.concept-card', ['concept' => $concept])
                        @endforeach
                    </div>
                </section>
            @endif

            @if($theories->count() > 0)
                <section>
                    <h2 class="text-2xl font-headline font-bold text-stone-900 dark:text-stone-100 mb-8 flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">hub</span>
                        Major Theories ({{ $theories->count() }})
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($theories as $theory)
                            @include('livewire.pages.encyclopedia.partials.theory-card', ['theory' => $theory])
                        @endforeach
                    </div>
                </section>
            @endif

            @if($anthropologists->isEmpty() && $concepts->isEmpty() && $theories->isEmpty())
                <div class="py-20 text-center">
                    <span class="material-symbols-outlined text-6xl text-stone-300 mb-4">search_off</span>
                    <h3 class="text-xl font-headline font-bold text-stone-900 dark:text-stone-100">No records found</h3>
                    <p class="text-stone-500 mt-2">Adjust your filters or try a different search word.</p>
                    <button wire:click="resetFilters" class="mt-6 text-primary font-bold hover:underline">Clear all filters</button>
                </div>
            @endif
        </div>
    @else
        <!-- Default Landing View -->
        <div class="space-y-20">
            <!-- Influential Anthropologists -->
            <section>
                <div class="flex justify-between items-end mb-10 border-b border-stone-200 dark:border-stone-800 pb-6">
                    <div class="space-y-2">
                        <h2 class="text-3xl font-headline font-bold text-stone-900 dark:text-stone-100 italic">Influential Anthropologists</h2>
                        <p class="text-stone-500 text-lg">The pioneers who shaped our understanding of human culture.</p>
                    </div>
                    <button wire:click="$set('category', 'Anthropologists')" class="text-primary text-sm font-bold flex items-center gap-1 hover:gap-2 transition-all group">
                        Explore All <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                </div>
                
                @if($anthropologists->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($anthropologists as $person)
                            @include('livewire.pages.encyclopedia.partials.anthropologist-card', ['person' => $person])
                        @endforeach
                    </div>
                @else
                    <p class="text-center py-10 text-stone-400 italic font-medium">No influential anthropologists featured currently.</p>
                @endif
            </section>

            <!-- Bifurcated Content: Concepts & Theories -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Core Concepts -->
                <section class="space-y-8">
                    <div class="space-y-2 flex justify-between items-end border-b border-stone-200 dark:border-stone-800 pb-4">
                        <div>
                            <h2 class="text-2xl font-headline font-bold text-stone-900 dark:text-stone-100 italic">Core Concepts</h2>
                            <p class="text-sm text-stone-500">Essential terminology and foundations.</p>
                        </div>
                        <button wire:click="$set('category', 'Concepts')" class="text-primary text-[10px] font-bold uppercase tracking-widest hover:underline">View All</button>
                    </div>
                    
                    @if($concepts->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @foreach($concepts as $concept)
                                @include('livewire.pages.encyclopedia.partials.concept-card', ['concept' => $concept])
                            @endforeach
                        </div>
                    @else
                        <p class="text-stone-400 italic text-sm">Check back soon for key concepts.</p>
                    @endif
                </section>

                <!-- Major Theories -->
                <section class="space-y-8">
                    <div class="space-y-2 flex justify-between items-end border-b border-stone-200 dark:border-stone-800 pb-4">
                        <div>
                            <h2 class="text-2xl font-headline font-bold text-stone-900 dark:text-stone-100 italic">Major Theories</h2>
                            <p class="text-sm text-stone-500">Theoretical frameworks used to analyze humanity.</p>
                        </div>
                        <button wire:click="$set('category', 'Theories')" class="text-primary text-[10px] font-bold uppercase tracking-widest hover:underline">View All</button>
                    </div>
                    
                    @if($theories->count() > 0)
                        <div class="space-y-6">
                            @foreach($theories as $theory)
                                @include('livewire.pages.encyclopedia.partials.theory-card', ['theory' => $theory])
                            @endforeach
                        </div>
                    @else
                        <p class="text-stone-400 italic text-sm">No theories summarized yet.</p>
                    @endif
                </section>
            </div>

            <!-- Research Methods (Static for now) -->
            <section class="space-y-12">
                <div class="text-center space-y-3">
                    <h2 class="text-3xl font-headline font-bold text-stone-900 dark:text-stone-100 italic">Research Methods</h2>
                    <p class="text-stone-500 max-w-2xl mx-auto">The tools of the anthropological trade, from traditional fieldwork to modern digital ethnography.</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach([
                        ['icon' => 'description', 'title' => 'Ethnography'],
                        ['icon' => 'visibility', 'title' => 'Participant Observation'],
                        ['icon' => 'history_edu', 'title' => 'Life History'],
                        ['icon' => 'travel_explore', 'title' => 'Fieldwork']
                    ] as $method)
                        <div class="bg-white dark:bg-stone-900 p-8 rounded-2xl border border-stone-200 dark:border-stone-800 text-center space-y-4 hover:shadow-xl hover:-translate-y-1 transition-all group">
                            <div class="h-16 w-16 bg-orange-100 dark:bg-orange-950 rounded-full flex items-center justify-center mx-auto group-hover:bg-primary group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-3xl">{{ $method['icon'] }}</span>
                            </div>
                            <h5 class="font-bold text-xs uppercase tracking-widest">{{ $method['title'] }}</h5>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Knowledge Map Promo -->
            <section class="relative bg-stone-900 text-white rounded-[40px] overflow-hidden p-8 md:p-16 border border-stone-800 shadow-2xl">
                <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-12">
                    <div class="max-w-xl space-y-6">
                        <h2 class="text-4xl md:text-5xl font-headline font-bold italic leading-tight">Interactive Knowledge Map</h2>
                        <p class="text-stone-400 text-lg leading-relaxed">
                            Visualize the connections between cultures, theories, and historical eras. See how ideas traveled across the globe and evolved through time.
                        </p>
                        <button class="bg-primary hover:bg-orange-800 text-white font-bold py-4 px-10 rounded-2xl transition-all flex items-center gap-2 shadow-xl shadow-primary/20 hover:-translate-y-1 active:translate-y-0">
                            Launch Interactive Map <span class="material-symbols-outlined">map</span>
                        </button>
                    </div>
                    <div class="shrink-0">
                        <div class="w-56 h-56 md:w-64 md:h-64 rounded-full border-4 border-primary/20 flex items-center justify-center bg-stone-800/80 backdrop-blur-sm shadow-inner relative overflow-hidden group">
                            <div class="absolute inset-0 bg-primary/5 group-hover:bg-primary/10 transition-colors animate-pulse"></div>
                            <span class="material-symbols-outlined text-8xl text-primary drop-shadow-2xl">language</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recommended for You (Intelligent Fallback) -->
            <section class="bg-white dark:bg-stone-900 p-10 rounded-3xl border border-stone-200 dark:border-stone-800 shadow-sm">
                <div class="flex items-center gap-3 mb-8">
                    <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                    <h2 class="text-2xl font-headline font-bold text-stone-900 dark:text-stone-100">Recommended for You</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Thinker recommendation -->
                    <div class="space-y-3 p-6 rounded-2xl bg-stone-50 dark:bg-stone-950 border border-stone-100 dark:border-stone-800 hover:border-primary/30 transition-all cursor-pointer">
                        <span class="text-[10px] font-extrabold text-primary uppercase tracking-[0.2em] px-2 py-0.5 bg-primary/10 rounded-full">Thinker</span>
                        @if($anthropologists->count() > 0)
                            <h4 class="font-headline font-bold text-lg text-stone-900 dark:text-stone-100">{{ $anthropologists->random()->full_name }}</h4>
                        @else
                            <h4 class="font-headline font-bold text-lg text-stone-900 dark:text-stone-100">Franz Boas</h4>
                        @endif
                        <p class="text-xs text-stone-500 italic">Historical deep-dive into cultural relativism.</p>
                    </div>

                    <!-- Concept recommendation -->
                    <div class="space-y-3 p-6 rounded-2xl bg-stone-50 dark:bg-stone-950 border border-stone-100 dark:border-stone-800 hover:border-primary/30 transition-all cursor-pointer">
                        <span class="text-[10px] font-extrabold text-primary uppercase tracking-[0.2em] px-2 py-0.5 bg-primary/10 rounded-full">Concept</span>
                        @if($concepts->count() > 0)
                            <h4 class="font-headline font-bold text-lg text-stone-900 dark:text-stone-100">{{ $concepts->random()->title }}</h4>
                        @else
                            <h4 class="font-headline font-bold text-lg text-stone-900 dark:text-stone-100">Cultural Hegemony</h4>
                        @endif
                        <p class="text-xs text-stone-500 italic">Power dynamics and social control mechanisms.</p>
                    </div>

                    <!-- Theory recommendation -->
                    <div class="space-y-3 p-6 rounded-2xl bg-stone-50 dark:bg-stone-950 border border-stone-100 dark:border-stone-800 hover:border-primary/30 transition-all cursor-pointer">
                        <span class="text-[10px] font-extrabold text-primary uppercase tracking-[0.2em] px-2 py-0.5 bg-primary/10 rounded-full">Theory</span>
                        @if($theories->count() > 0)
                            <h4 class="font-headline font-bold text-lg text-stone-900 dark:text-stone-100">{{ $theories->random()->title }}</h4>
                        @else
                            <h4 class="font-headline font-bold text-lg text-stone-900 dark:text-stone-100">Functionalism</h4>
                        @endif
                        <p class="text-xs text-stone-500 italic">The societal organ model and social stability.</p>
                    </div>
                </div>
            </section>
        </div>
        @endif
    </div>
</div>
