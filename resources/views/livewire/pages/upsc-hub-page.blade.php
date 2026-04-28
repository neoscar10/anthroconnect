@push('styles')
    <!-- Bootstrap 5 CSS for this page specifically -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --ac-primary: #9a3412;
            --ac-primary-dark: #7c2d12;
            --ac-stone-50: #fafaf9;
            --ac-stone-200: #e7e5e4;
            --ac-stone-600: #57534e;
        }
        .upsc-hub-page {
            font-family: 'Public Sans', sans-serif;
            background-color: var(--ac-stone-50);
        }
        .upsc-hub-page h1, .upsc-hub-page h2, .upsc-hub-page h3, .upsc-hub-page h4, .upsc-hub-page h5, .upsc-hub-page h6 {
            font-family: 'Lora', serif;
            font-style: italic;
        }
        .upsc-hero {
            background-color: white;
            border: 1px solid var(--ac-stone-200);
            border-radius: 2rem;
            padding: 4rem;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
            background-image: radial-gradient(circle at 2px 2px, rgba(154, 52, 18, 0.05) 1px, transparent 0);
            background-size: 24px 24px;
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
        .btn-primary {
            background-color: var(--ac-primary);
            border-color: var(--ac-primary);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 20px rgba(154, 52, 18, 0.15);
        }
        .btn-primary:hover {
            background-color: var(--ac-primary-dark);
            border-color: var(--ac-primary-dark);
        }
        .btn-outline-primary {
            color: var(--ac-primary);
            border-color: var(--ac-primary);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-radius: 0.75rem;
        }
        .btn-outline-primary:hover {
            background-color: var(--ac-primary);
            border-color: var(--ac-primary);
        }
        .upsc-card {
            border: 1px solid var(--ac-stone-200);
            border-radius: 1.25rem;
            transition: all 0.3s ease;
            background: white;
            height: 100%;
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
        .search-container .form-control {
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            border: 1px solid var(--ac-stone-200);
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            font-size: 1.1rem;
        }
        .search-container .form-control:focus {
            border-color: var(--ac-primary);
            box-shadow: 0 10px 30px rgba(154, 52, 18, 0.1);
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
@endpush

<div class="upsc-hub-page py-5">
    <div class="container">
        
        <!-- HERO -->
        <div class="upsc-hero shadow-sm">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge-upsc mb-3 d-inline-block">Curated for Excellence</span>
                    <h1 class="display-4 fw-bold mb-3">UPSC Anthropology Hub</h1>
                    <p class="lead text-muted mb-4 font-body">
                        Your strategic command center for UPSC Anthropology preparation. We've aggregated every high-yield resource, thinker, and concept into a single, structured dashboard.
                    </p>

                    <div class="d-flex gap-3">
                        <a href="#modules" class="btn btn-primary px-5">Start Prep Journey</a>
                        <a href="{{ route('knowledge-map.show') }}" class="btn btn-light border px-4 rounded-3 fw-bold">Explore Knowledge Map</a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-center">
                    <span class="material-symbols-outlined display-1 text-primary opacity-25" style="font-size: 180px;">account_balance</span>
                </div>
            </div>
        </div>

        <!-- SEARCH -->
        <div class="search-container mb-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="position-relative">
                        <span class="material-symbols-outlined position-absolute top-50 translate-middle-y ms-3 text-muted">search</span>
                        <input type="text"
                               class="form-control ps-5"
                               placeholder="Search thinkers, modules, or core concepts..."
                               wire:model.live.debounce.400ms="search">
                    </div>
                </div>
            </div>
        </div>

        @if($modules->count() > 0)
        <!-- MODULES -->
        <div id="modules" class="mb-5">
            <h3 class="section-title fw-bold">Core UPSC Modules</h3>
            <div class="row g-4">
                @foreach($modules as $module)
                    <div class="col-md-6 col-lg-4">
                        <div class="upsc-card p-4">
                            <div class="mb-3">
                                @if($module->cover_image)
                                    <img src="{{ Storage::url($module->cover_image) }}" class="rounded-3 w-100 mb-3 object-cover" style="height: 180px;">
                                @else
                                    <div class="rounded-3 bg-light w-100 mb-3 d-flex align-items-center justify-center" style="height: 180px;">
                                        <span class="material-symbols-outlined text-muted opacity-50">school</span>
                                    </div>
                                @endif
                                <h5 class="fw-bold mb-2">{{ $module->title }}</h5>
                                <p class="text-muted small mb-3">
                                    {{ Str::limit($module->short_description ?: $module->overview, 100) }}
                                </p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="badge-upsc">Module</span>
                                <a href="{{ route('modules.show', $module->slug) }}" class="btn btn-sm btn-outline-primary">Enter Module</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($exploreItems->count() > 0)
        <!-- FEATURED COLLECTIONS (EXPLORE) -->
        <div class="mb-5">
            <h3 class="section-title fw-bold">High-Yield Collections</h3>
            <div class="row g-4">
                @foreach($exploreItems as $article)
                    <div class="col-md-6 col-lg-4">
                        <div class="upsc-card p-4">
                            <div class="mb-3">
                                @if($article->featured_image)
                                    <img src="{{ Storage::url($article->featured_image) }}" class="rounded-3 w-100 mb-3 object-cover" style="height: 180px;">
                                @else
                                    <div class="rounded-3 bg-light w-100 mb-3 d-flex align-items-center justify-center" style="height: 180px;">
                                        <span class="material-symbols-outlined text-muted opacity-50">explore</span>
                                    </div>
                                @endif
                                <h5 class="fw-bold mb-2">
                                    {{ $article->title }}
                                    @if($article->is_members_only)
                                        <span class="material-symbols-outlined small text-muted align-middle" title="Members Only" style="font-size: 1rem;">lock</span>
                                    @endif
                                </h5>
                                <p class="text-muted small mb-3">
                                    {{ Str::limit($article->excerpt, 100) }}
                                </p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="badge-upsc" style="background-color: rgba(96, 108, 56, 0.1); color: #606c38;">Collection</span>
                                <a href="{{ route('explore.show', $article->slug) }}" class="btn btn-sm btn-link text-primary p-0 fw-bold text-decoration-none">Read Collection →</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($anthropologists->count() > 0)
        <!-- THINKERS -->
        <div class="mb-5">
            <h3 class="section-title fw-bold">Foundational Thinkers</h3>
            <div class="row g-4">
                @foreach($anthropologists as $item)
                    <div class="col-md-6 col-lg-3">
                        <div class="upsc-card p-4 text-center">
                            @if($item->profile_image)
                                <img src="{{ Storage::url($item->profile_image) }}" class="thinker-img">
                            @else
                                <div class="thinker-img bg-light d-flex align-items-center justify-content-center">
                                    <span class="material-symbols-outlined text-muted">person</span>
                                </div>
                            @endif
                            <h6 class="fw-bold mb-1">{{ $item->name }}</h6>
                            <p class="text-muted small mb-3">Thinker / Theorist</p>
                            <a href="{{ route('encyclopedia.anthropologists.show', $item->slug) }}" class="btn btn-sm btn-outline-primary px-4">Biography</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="row">
            @if($concepts->count() > 0)
            <!-- CONCEPTS -->
            <div class="col-lg-6 mb-5">
                <h3 class="section-title fw-bold">UPSC Core Concepts</h3>
                <div class="row g-3">
                    @foreach($concepts as $item)
                        <div class="col-12">
                            <a href="{{ route('encyclopedia.concepts.show', $item->slug) }}" class="text-decoration-none">
                                <div class="upsc-card p-3 d-flex align-items-center gap-3">
                                    <div class="resource-icon mb-0">
                                        <span class="material-symbols-outlined">label_important</span>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ $item->title }}</h6>
                                        <p class="small text-muted mb-0">{{ Str::limit($item->description, 60) }}</p>
                                    </div>
                                    <span class="material-symbols-outlined ms-auto text-muted opacity-50">chevron_right</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($theories->count() > 0)
            <!-- THEORIES -->
            <div class="col-lg-6 mb-5">
                <h3 class="section-title fw-bold">Major Theories</h3>
                <div class="row g-3">
                    @foreach($theories as $item)
                        <div class="col-12">
                            <a href="{{ route('encyclopedia.theories.show', $item->slug) }}" class="text-decoration-none">
                                <div class="upsc-card p-3 d-flex align-items-center gap-3">
                                    <div class="resource-icon mb-0" style="color: #606c38;">
                                        <span class="material-symbols-outlined">psychology</span>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ $item->title }}</h6>
                                        <p class="small text-muted mb-0">{{ Str::limit($item->description, 60) }}</p>
                                    </div>
                                    <span class="material-symbols-outlined ms-auto text-muted opacity-50">chevron_right</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        @if($resources->count() > 0)
        <!-- LIBRARY -->
        <div class="mb-5">
            <h3 class="section-title fw-bold">UPSC Library Resources</h3>
            <div class="row g-4">
                @foreach($resources as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="upsc-card p-4">
                            <div class="d-flex gap-3 mb-3">
                                <div class="flex-shrink-0" style="width: 80px; height: 110px;">
                                    @if($item->cover_image_path)
                                        <img src="{{ Storage::url($item->cover_image_path) }}" class="w-100 h-100 rounded shadow-sm object-cover">
                                    @else
                                        <div class="w-100 h-100 bg-light rounded d-flex align-items-center justify-center">
                                            <span class="material-symbols-outlined text-muted opacity-50">menu_book</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">
                                        {{ $item->title }}
                                        @if($item->access_type === 'member_only')
                                            <span class="material-symbols-outlined small text-muted align-middle" title="Members Only" style="font-size: 0.9rem;">lock</span>
                                        @endif
                                    </h6>
                                    <p class="text-muted x-small mb-1">{{ $item->author_display }}</p>
                                    <p class="text-muted x-small italic">{{ Str::limit($item->abstract ?: $item->description, 60) }}</p>
                                </div>
                            </div>
                            <a href="{{ route('library.show', $item->slug) }}" class="btn btn-sm btn-outline-primary w-100">Access Resource</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($modules->count() == 0 && $exploreItems->count() == 0 && $anthropologists->count() == 0 && $concepts->count() == 0 && $theories->count() == 0 && $resources->count() == 0)
            <div class="text-center py-5">
                <span class="material-symbols-outlined display-1 text-muted opacity-25">search_off</span>
                <h3 class="mt-4 text-muted">No UPSC content found matching your search.</h3>
                <p class="text-muted">Try a different keyword or browse our general anthropology content.</p>
            </div>
        @endif

    </div>
</div>
