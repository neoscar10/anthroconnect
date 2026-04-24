<div class="ac-library-page">
    @push('styles')
        @include('frontend.library.partials.styles')
    @endpush

    <section class="ac-library-hero">
        <div class="container mx-auto px-4">
            <div class="ac-library-hero-inner">
                <h1>Anthropology Research Library</h1>
                <p>
                    Access curated academic resources, scholarly papers, and foundational texts across all sub-disciplines of human study.
                </p>

                <div class="ac-library-search">
                    <span class="ac-search-icon">
                        <span class="material-symbols-outlined">search</span>
                    </span>
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Search papers, books, authors, or regional ethnographies..."
                    >
                    <button type="button" wire:click="$refresh">Search</button>
                </div>

                <div class="ac-library-filters">
                    <select wire:model.live="type">
                        <option value="">Resource Type</option>
                        @foreach($resourceTypes as $typeItem)
                            <option value="{{ $typeItem->slug }}">
                                {{ $typeItem->name }}
                            </option>
                        @endforeach
                    </select>

                    <select wire:model.live="region">
                        <option value="">Region</option>
                        @foreach($regions as $regionItem)
                            <option value="{{ $regionItem->slug }}">
                                {{ $regionItem->name }}
                            </option>
                        @endforeach
                    </select>

                    <select wire:model.live="year">
                        <option value="">Publication Year</option>
                        @foreach($publicationYears as $yearItem)
                            <option value="{{ $yearItem }}">
                                {{ $yearItem }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </section>

    @if($search || $type || $region || $year || $topic)
        <section class="ac-library-section">
            <div class="container mx-auto px-4">
                <div class="ac-section-head">
                    <h2>Search Results</h2>
                    <button type="button" wire:click="$set('search', ''); $set('type', ''); $set('region', ''); $set('year', ''); $set('topic', '');" class="text-primary font-bold text-sm hover:underline">Clear Filters</button>
                </div>

                @if($resources->count())
                    <div class="ac-resource-grid">
                        @foreach($resources as $resource)
                            <x-library.resource-card :resource="$resource" :access="$accessService->check(auth()->user(), $resource)" :key="'search-'.$resource->id" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $resources->links() }}
                    </div>
                @else
                    <div class="ac-empty-state py-20 text-center opacity-60">
                        <span class="material-symbols-outlined text-6xl mb-4">search_off</span>
                        <h3 class="text-2xl font-headline italic">No resources found</h3>
                        <p>Try adjusting your search or filters.</p>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <section class="ac-library-section">
        <div class="container mx-auto px-4">
            <div class="ac-section-head">
                <h2>Featured Academic Resources</h2>
                <button type="button" wire:click="$set('sort', 'featured')" class="text-primary font-bold text-sm">View All Collection →</button>
            </div>

            @if($featuredResources->count())
                <div class="ac-resource-grid ac-featured-grid">
                    @foreach($featuredResources as $resource)
                        <x-library.resource-card :resource="$resource" :access="$accessService->check(auth()->user(), $resource)" :key="'featured-'.$resource->id" />
                    @endforeach
                </div>
            @else
                <div class="ac-empty-state py-20 text-center opacity-60">
                    <h3 class="text-xl font-headline italic">No featured resources yet</h3>
                    <p>Featured academic resources will appear here once published.</p>
                </div>
            @endif
        </div>
    </section>

    <section class="ac-library-section ac-soft-section">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <div class="lg:col-span-2">
                    <h2 class="ac-block-title mb-8">Latest Research Resources</h2>

                    <div class="ac-latest-list">
                        @forelse($latestResources as $resource)
                            <x-library.resource-list-item :resource="$resource" :access="$accessService->check(auth()->user(), $resource)" :key="'latest-'.$resource->id" />
                        @empty
                            <div class="ac-empty-state py-10 text-center opacity-60">
                                <h3 class="text-lg font-headline italic">No latest resources yet</h3>
                                <p>Published research resources will appear here.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <aside class="ac-library-sidebar">
                        <div class="ac-sidebar-block">
                            <h3>Personalized for You</h3>

                            <div class="ac-recommend-box">
                                @forelse($recommendedResources as $resource)
                                    @php($access = $accessService->check(auth()->user(), $resource))
                                    <a
                                        href="{{ $access['allowed'] ? route('library.show', $resource) : '#' }}"
                                        class="ac-recommend-item {{ !$access['allowed'] ? 'js-library-locked' : '' }}"
                                        data-reason="{{ $access['reason'] }}"
                                        data-message="{{ $access['lock_message'] }}"
                                    >
                                        <span>{{ $loop->first ? 'Recommended' : 'Related to your History' }}</span>
                                        <strong>{{ $resource->title }}</strong>
                                        <small>By {{ $resource->author_display ?: 'Unknown Author' }}</small>
                                    </a>
                                @empty
                                    <p class="text-stone-400 text-sm italic">No recommendations available yet.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="ac-sidebar-block">
                            <h3>Browse by Topic</h3>

                            <div class="ac-topic-grid">
                                @forelse($topics as $topicItem)
                                    <button type="button" wire:click="setTopic('{{ $topicItem->slug }}')" class="ac-topic-card group {{ $topic === $topicItem->slug ? 'ring-2 ring-primary border-primary' : '' }}">
                                        <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">category</span>
                                        <span>{{ $topicItem->name }}</span>
                                    </button>
                                @empty
                                    <p class="text-stone-400 text-sm italic">No topics available yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <section class="ac-connect-section">
        <div class="container mx-auto px-4">
            <div class="ac-connect-card">
                <div class="ac-connect-content">
                    <h2>Connect Your Research</h2>
                    <p>
                        Integrate your findings with our holistic learning ecosystem. Transition from reading papers to exploring visual concept maps and interactive lessons.
                    </p>

                    <div class="ac-connect-links">
                        <a href="{{ route('modules.index') }}">
                            <span class="material-symbols-outlined">school</span>
                            <span>
                                <strong>Lessons</strong>
                                <small>Structured courses and modules</small>
                            </span>
                        </a>

                        <a href="#">
                            <span class="material-symbols-outlined">hub</span>
                            <span>
                                <strong>Knowledge Map</strong>
                                <small>Visual link between concepts</small>
                            </span>
                        </a>

                        <a href="{{ route('encyclopedia.index') }}">
                            <span class="material-symbols-outlined">menu_book</span>
                            <span>
                                <strong>Concept Encyclopedia</strong>
                                <small>Definitive terminology guide</small>
                            </span>
                        </a>

                        <a href="{{ route('community.index') }}">
                            <span class="material-symbols-outlined">forum</span>
                            <span>
                                <strong>Community</strong>
                                <small>Join the discussion</small>
                            </span>
                        </a>
                    </div>
                </div>

                <div class="ac-connect-art hidden lg:flex">
                    <div class="ac-orbit">
                        <img src="https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&q=80&w=400" alt="Research network">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-library.restriction-modal />

    @push('scripts')
        @include('frontend.library.partials.scripts')
    @endpush
</div>
