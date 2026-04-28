<div class="ac-library-detail pb-24">
    @push('styles')
        @include('frontend.library.partials.styles')
    @endpush

    <div class="container mx-auto px-4">

        <nav class="ac-breadcrumb">
            <a href="{{ route('library.index') }}">Library</a>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            @if($resource->resourceType)
                <a href="{{ route('library.index', ['type' => $resource->resourceType->slug]) }}">{{ $resource->resourceType->name }}</a>
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            @endif
            <span class="truncate max-w-xs">{{ $resource->title }}</span>
        </nav>

        <section class="ac-resource-hero">
            <div class="ac-detail-cover">
                <img src="{{ $resource->cover_url }}" alt="{{ $resource->title }}">
                @if(!$access['allowed'])
                    <div class="ac-cover-lock">
                        <span class="material-symbols-outlined text-4xl text-white">lock</span>
                    </div>
                @endif
            </div>

            <div class="ac-detail-heading">
                <div class="ac-badge-row">
                    @if($resource->resourceType)
                        <a href="{{ route('library.index', ['type' => $resource->resourceType->slug]) }}" class="ac-badge">
                            {{ $resource->resourceType->name }}
                        </a>
                    @endif

                    @if($access['is_member_only'])
                        <span class="ac-badge ac-badge-lock">
                            <span class="material-symbols-outlined text-[10px] mr-1">lock</span> Member Only
                        </span>
                    @endif
                </div>

                <h1>{{ $resource->title }}</h1>

                <p class="ac-author-line">
                    {{ $resource->author_display ?: 'Unknown Author' }}
                    @if($resource->publication_year)
                        <span>•</span>
                        <em>{{ $resource->publication_year }}</em>
                    @endif
                </p>

                <div class="ac-detail-actions">
                    @if($access['allowed'] && $resource->preview_url)
                        <a href="#document-preview" class="ac-btn ac-btn-primary">
                            <span class="material-symbols-outlined">menu_book</span>
                            Read Full Resource
                        </a>
                    @else
                        <button type="button" class="ac-btn ac-btn-primary js-library-locked" data-reason="{{ $access['reason'] }}" data-message="{{ $access['lock_message'] }}">
                            <span class="material-symbols-outlined">lock</span>
                            {{ $access['cta_label'] }}
                        </button>
                    @endif

                    @if($resource->allow_download && $resource->file_path)
                        @if($access['allowed'])
                            <a href="{{ route('library.download', $resource) }}" class="ac-btn ac-btn-outline">
                                <span class="material-symbols-outlined">download</span>
                                Download PDF
                            </a>
                        @else
                            <button type="button" class="ac-btn ac-btn-outline js-library-locked" data-reason="{{ $access['reason'] }}" data-message="{{ $access['lock_message'] }}">
                                <span class="material-symbols-outlined">lock</span>
                                Download PDF
                            </button>
                        @endif
                    @endif

                    <button type="button" class="ac-btn ac-btn-icon" aria-label="Bookmark">
                        <span class="material-symbols-outlined">bookmark_add</span>
                    </button>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2">
                <div class="ac-detail-main">
                    <section class="ac-detail-section">
                        <h2>Abstract</h2>
                        <p>{{ $resource->abstract ?: $resource->description ?: 'No abstract is available for this resource yet.' }}</p>
                    </section>

                    <section id="document-preview" class="ac-document-preview">
                        <div class="ac-preview-head">
                            <span>Document Preview</span>
                            <div>
                                <button type="button"><span class="material-symbols-outlined text-sm">remove</span></button>
                                <span class="text-xs font-bold text-stone-500">100%</span>
                                <button type="button"><span class="material-symbols-outlined text-sm">add</span></button>
                                <button type="button" id="btn-preview-fullscreen"><span class="material-symbols-outlined text-sm">fullscreen</span></button>
                            </div>
                        </div>

                        @if($access['allowed'] && $resource->preview_url)
                            <div class="ac-preview-frame">
                                <iframe src="{{ $resource->preview_url }}" title="{{ $resource->title }}"></iframe>
                            </div>
                        @elseif(!$access['allowed'])
                            <div class="ac-preview-locked">
                                <span class="material-symbols-outlined text-6xl text-stone-300 mb-4">lock</span>
                                <h3>Members-only resource</h3>
                                <p class="max-w-md mx-auto mb-6 text-stone-500">{{ $access['lock_message'] }}</p>
                                <button type="button" class="ac-btn ac-btn-primary js-library-locked" data-reason="{{ $access['reason'] }}" data-message="{{ $access['lock_message'] }}">
                                    {{ $access['cta_label'] }}
                                </button>
                            </div>
                        @else
                            <div class="ac-preview-empty">
                                <span class="material-symbols-outlined text-6xl text-stone-300 mb-4">description</span>
                                <p>Preview not available for this resource.</p>
                            </div>
                        @endif
                    </section>

                    <section class="ac-detail-section">
                        <h2>Key Concepts & Tags</h2>

                        <div class="ac-chip-row">
                            @foreach($resource->topics as $topic)
                                <a href="{{ route('library.index', ['topic' => $topic->slug]) }}">{{ $topic->name }}</a>
                            @endforeach

                            @foreach($resource->tags as $tag)
                                <a href="{{ route('library.index', ['search' => $tag->name]) }}">{{ $tag->name }}</a>
                            @endforeach
                        </div>
                    </section>

                    <section class="ac-detail-section">
                        <div class="ac-section-head ac-section-head-small">
                            <h2>Community Discussion</h2>
                            <a href="{{ route('community.index') }}">View All Threads</a>
                        </div>

                        <div class="ac-discussion-card bg-stone-50 border-stone-200 border-dashed border-2 text-center p-12">
                            <span class="material-symbols-outlined text-4xl text-stone-300 mb-4">forum</span>
                            <h3 class="font-headline text-xl mb-2">Start a discussion around this resource</h3>
                            <p class="text-stone-500 mb-6">Connect with other learners and researchers through the AnthroConnect community.</p>
                            <a href="{{ route('community.index') }}" class="ac-btn ac-btn-outline inline-flex">Go to Community</a>
                        </div>
                    </section>
                </div>
            </div>

            <div>
                <aside class="ac-detail-sidebar">
                    <section class="ac-meta-card shadow-sm border-stone-200">
                        <h3>Resource Details</h3>

                        <x-library.meta-row label="Author" :value="$resource->author_display" />
                        <x-library.meta-row label="Publication Year" :value="$resource->publication_year" />
                        <x-library.meta-row label="Publisher" :value="$resource->publisher" />
                        <x-library.meta-row label="Type" :value="optional($resource->resourceType)->name" />
                        <x-library.meta-row label="Language" :value="$resource->language" />
                        <x-library.meta-row label="ISBN" :value="$resource->isbn" />
                        <x-library.meta-row label="Pages" :value="$resource->pages_count" />
                    </section>

                    <x-library.citation-box :citation="$resource->apa_citation" />

                    @if($relatedLearningItems->count())
                        <section class="ac-side-block">
                            <h3>Related Learning</h3>

                            @foreach($relatedLearningItems as $item)
                                <a href="{{ $item->url ?? '#' }}" class="ac-learning-item bg-white shadow-sm border border-stone-200">
                                    <span><span class="material-symbols-outlined">school</span></span>
                                    <strong>{{ $item->label ?? $item->title ?? 'Learning Item' }}</strong>
                                </a>
                            @endforeach
                        </section>
                    @endif

                    <section class="ac-side-block">
                        <h3>More Resources</h3>

                        @forelse($relatedResources as $related)
                            @php($relatedAccess = app(\App\Services\Library\LibraryAccessService::class)->check(auth()->user(), $related))
                            <a
                                href="{{ $relatedAccess['allowed'] ? route('library.show', $related) : '#' }}"
                                class="ac-more-resource bg-white shadow-sm border border-stone-200 {{ !$relatedAccess['allowed'] ? 'js-library-locked' : '' }}"
                                data-reason="{{ $relatedAccess['reason'] }}"
                                data-message="{{ $relatedAccess['lock_message'] }}"
                                :key="'related-'.$related->id"
                            >
                                <img src="{{ $related->cover_url }}" alt="{{ $related->title }}">
                                <span>
                                    <strong>{{ $related->title }}</strong>
                                    <small>{{ $related->author_display ?: 'Unknown Author' }} @if($related->publication_year) • {{ $related->publication_year }} @endif</small>
                                </span>
                            </a>
                        @empty
                            <p class="ac-muted text-sm italic">No related resources found.</p>
                        @endforelse
                    </section>
                </aside>
            </div>
        </section>
    </div>

    <x-library.restriction-modal />

    @push('scripts')
        @include('frontend.library.partials.scripts')
    @endpush
</div>
