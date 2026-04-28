<div class="knowledge-map-page">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('frontend/css/knowledge-map.css') }}?v={{ filemtime(public_path('frontend/css/knowledge-map.css')) }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('frontend/js/knowledge-map-user.js') }}?v={{ filemtime(public_path('frontend/js/knowledge-map-user.js')) }}" defer></script>
    @endpush

    <section class="km-hero">
        <div class="container-fluid px-4 px-lg-5">
            <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                <div>
                    <a href="{{ $backUrl }}" class="km-back-btn">
                        <span class="material-symbols-outlined">arrow_back</span>
                        Back
                    </a>

                    <h1 class="km-title">
                        {{ $map->title ?? 'Anthropology Knowledge Map' }}
                    </h1>

                    <p class="km-subtitle">
                        {{ $map->subtitle ?? 'Explore anthropology concepts, thinkers, and themes through an interactive knowledge network designed for scholarly exploration.' }}
                    </p>
                </div>

                <div class="km-hero-actions">
                    <div class="km-search-box">
                        <span class="material-symbols-outlined">search</span>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search concepts..."
                        >
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="km-workspace-wrap">
        <div class="container-fluid px-4 px-lg-5">
            <div class="km-workspace">

                <aside class="km-filter-panel">
                    <div class="km-panel-header">
                        <h3>Filter Visualization</h3>
                        <p>Refine your exploration</p>
                    </div>

                    <div class="km-panel-body">

                        @foreach($tagGroups as $group)
                            <div class="km-filter-group">
                                <p class="km-filter-title">
                                    <span class="material-symbols-outlined">label</span>
                                    {{ $group['name'] }}
                                </p>

                                @if(($group['selection_type'] ?? 'multi') === 'single')
                                    <select
                                        class="form-select km-select"
                                        wire:model.live="selectedSingleTags.{{ $group['id'] }}"
                                    >
                                        <option value="">All {{ $group['name'] }}</option>
                                        @foreach($group['tags'] as $tag)
                                            <option value="{{ $tag['id'] }}">{{ $tag['name'] }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="km-chip-list">
                                        @foreach($group['tags'] as $tag)
                                            <button
                                                type="button"
                                                wire:click="toggleTag({{ $tag['id'] }})"
                                                class="km-chip {{ in_array($tag['id'], $selectedTags ?? []) ? 'active' : '' }}"
                                            >
                                                {{ $tag['name'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <div class="km-upsc-toggle">
                            <span>UPSC Relevance</span>

                            <label class="km-switch">
                                <input type="checkbox" wire:model.live="upscOnly">
                                <span></span>
                            </label>
                        </div>


                    </div>
                </aside>

                <main
                    class="km-canvas-area"
                    x-data="knowledgeMapUserCanvas({
                        nodes: @js($visibleNodes),
                        connections: @js($visibleConnections),
                        selectedNodeId: @entangle('selectedNodeId').live,
                        canvasWidth: @js($map->canvas_settings['width'] ?? 4000),
                        canvasHeight: @js($map->canvas_settings['height'] ?? 3000)
                    })"
                    x-init="init()"
                    wire:ignore
                >
                    <div class="km-canvas" x-ref="canvas">
                        <svg class="km-svg" x-ref="svg">
                            <defs>
                                <marker id="km-user-arrow" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto" markerUnits="strokeWidth">
                                    <path d="M0,0 L0,6 L9,3 z" fill="#9e5015"></path>
                                </marker>
                            </defs>

                            <g x-ref="connectionsLayer"></g>
                        </svg>

                        <div class="km-nodes-layer" x-ref="nodesLayer">
                            <template x-for="node in nodes" :key="node.id">
                                <button
                                    type="button"
                                    class="km-map-node"
                                    :class="{
                                        'selected': Number(selectedNodeId) === Number(node.id),
                                        'core': node.importance === 'core',
                                        'primary-node': node.importance === 'primary',
                                        'upsc-node': node.is_upsc_relevant
                                    }"
                                    :data-node-id="node.id"
                                    :style="`transform: translate(${node.position_x}px, ${node.position_y}px)`"
                                    @click="selectNode(node.id)"
                                >
                                    <span class="km-node-title" x-text="node.title"></span>

                                    <span
                                        class="km-node-upsc"
                                        x-show="node.is_upsc_relevant"
                                    >
                                        UPSC
                                    </span>
                                </button>
                            </template>
                        </div>

                        <div class="km-canvas-empty" x-show="nodes.length === 0">
                            <span class="material-symbols-outlined">map</span>
                            <h4>No matching nodes</h4>
                            <p>Try clearing filters or disabling UPSC relevance.</p>
                        </div>

                        <div class="km-controls">
                            <button type="button" @click="zoomIn()" title="Zoom in">
                                <span class="material-symbols-outlined">zoom_in</span>
                            </button>
                            <button type="button" @click="zoomOut()" title="Zoom out">
                                <span class="material-symbols-outlined">zoom_out</span>
                            </button>
                            <button type="button" @click="resetView()" title="Reset view">
                                <span class="material-symbols-outlined">location_searching</span>
                            </button>
                            <button type="button" @click="fitView()" title="Fit view">
                                <span class="material-symbols-outlined">fit_screen</span>
                            </button>
                        </div>
                    </div>
                </main>

                <aside class="km-detail-panel">
                    @if($selectedNode)
                        <div class="km-selected-label">Selected Node</div>

                        <h2>{{ $selectedNode->title }}</h2>

                        <div class="km-node-meta">
                            <span class="km-dot"></span>
                            <span>{{ Str::headline($selectedNode->node_type) }}</span>

                            @if($selectedNode->is_upsc_relevant)
                                <span class="km-upsc-pill">UPSC</span>
                            @endif

                            @if($selectedNode->is_members_only)
                                <span class="km-member-pill">Members</span>
                            @endif
                        </div>

                        @if($selectedNode->is_members_only && !auth()->user()?->isMember())
                            <div class="km-locked-content">
                                <div class="km-locked-overlay">
                                    <span class="material-symbols-outlined">lock</span>
                                    <p>This node contains member-only learning material.</p>
                                    @auth
                                        <button @click="$dispatch('open-upgrade-modal')" class="btn btn-primary btn-sm mt-2">Become a Member</button>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm mt-2">Login to Access</a>
                                    @endauth
                                </div>
                                @if($selectedNode->short_description)
                                    <p class="km-node-summary blurred">{{ $selectedNode->short_description }}</p>
                                @endif
                            </div>
                        @else
                            @if($selectedNode->short_description)
                                <p class="km-node-summary">{{ $selectedNode->short_description }}</p>
                            @endif

                            @if($selectedNode->full_description || $selectedNode->manual_concept_summary)
                                <div class="km-node-description">
                                    {!! nl2br(e($selectedNode->full_description ?: $selectedNode->manual_concept_summary)) !!}
                                </div>
                            @endif

                            @if($selectedNode->tags && $selectedNode->tags->count())
                                <div class="km-detail-section">
                                    <h4>Tags</h4>
                                    <div class="km-chip-list">
                                        @foreach($selectedNode->tags as $tag)
                                            <span class="km-chip active">
                                                {{ optional($tag->group)->name ? optional($tag->group)->name . ': ' : '' }}{{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="km-detail-section">
                                <h4>Study Materials & Lessons</h4>

                                @forelse($selectedNode->attachments as $attachment)
                                    @php
                                        $attMeta = match($attachment->attachable_type) {
                                            \App\Models\Lms\LmsModule::class => [
                                                'icon' => 'school',
                                                'label' => 'Course Module',
                                                'url' => route('modules.show', $attachment->attachable->slug)
                                            ],
                                            \App\Models\Lms\LmsLesson::class => [
                                                'icon' => 'play_circle',
                                                'label' => 'Video Lesson',
                                                'url' => route('lessons.show', ['moduleSlug' => $attachment->attachable->module->slug, 'lessonSlug' => $attachment->attachable->slug])
                                            ],
                                            \App\Models\Lms\LmsResource::class => [
                                                'icon' => 'description',
                                                'label' => 'PDF Resource',
                                                'url' => route('modules.show', $attachment->attachable->module->slug)
                                            ],
                                            \App\Models\LibraryResource::class => [
                                                'icon' => 'menu_book',
                                                'label' => 'Library Book',
                                                'url' => route('library.show', $attachment->attachable->slug)
                                            ],
                                            default => ['icon' => 'attachment', 'label' => 'Material', 'url' => '#']
                                        };
                                    @endphp
                                    <a href="{{ $attMeta['url'] }}" class="km-related-item">
                                        <span class="material-symbols-outlined">{{ $attMeta['icon'] }}</span>
                                        <div>
                                            <strong>{{ $attachment->attachable?->title ?? 'Deleted Resource' }}</strong>
                                            <span>{{ $attMeta['label'] }}</span>
                                        </div>
                                    </a>
                                @empty
                                    <p class="km-muted">No specific study materials attached yet.</p>
                                @endforelse
                            </div>

                            <div class="km-detail-section">
                                <h4>Influential Thinkers / References</h4>

                                @if($selectedNode->anthropologist)
                                    <a href="{{ route('encyclopedia.anthropologists.show', $selectedNode->anthropologist->slug) }}" class="km-thinker-link">
                                        <span class="km-avatar">
                                            @if($selectedNode->anthropologist->image)
                                                <img src="{{ Storage::url($selectedNode->anthropologist->image) }}" alt="{{ $selectedNode->anthropologist->name }}">
                                            @else
                                                <span class="material-symbols-outlined">person</span>
                                            @endif
                                        </span>
                                        {{ $selectedNode->anthropologist->name }}
                                    </a>
                                @endif

                                @if($selectedNode->theory)
                                    <a href="{{ route('encyclopedia.theories.show', $selectedNode->theory->slug) }}" class="km-thinker-link">
                                        <span class="km-avatar"><span class="material-symbols-outlined">lightbulb</span></span>
                                        {{ $selectedNode->theory->title }}
                                    </a>
                                @endif

                                @unless($selectedNode->anthropologist || $selectedNode->theory)
                                    <p class="km-muted">No thinker or theory linked yet.</p>
                                @endunless
                            </div>

                            @if($selectedNode->encyclopediaConcept)
                                <a href="{{ route('encyclopedia.concepts.show', $selectedNode->encyclopediaConcept->slug) }}" class="km-profile-btn">
                                    Open Full Concept Profile
                                </a>
                            @endif
                        @endif
                    @else
                        <div class="km-no-selection">
                            <span class="material-symbols-outlined">ads_click</span>
                            <h3>Select a node</h3>
                            <p>Click any concept on the map to view its details, lessons, thinkers, and related materials.</p>
                        </div>
                    @endif
                </aside>

            </div>
        </div>
    </section>

    <section class="km-learning-paths">
        <div class="container-fluid px-4 px-lg-5">
            <div class="mb-4">
                <h3>Curated Learning Paths</h3>
                <p>Guided journeys through interconnected anthropology nodes.</p>
            </div>

            <div class="row g-4">
                @forelse($learningPaths as $path)
                    <div class="col-md-4">
                        <div class="km-path-card">
                            <div class="km-path-icon">
                                <span class="material-symbols-outlined">route</span>
                            </div>
                            <h4>{{ $path->title }}</h4>
                            <p>{{ $path->description }}</p>
                            <div class="km-path-footer">
                                <span>{{ $path->nodes_count ?? $path->nodes->count() }} Concepts</span>
                                <i class="mdi mdi-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="km-empty-paths">
                            No curated learning paths have been published yet.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="km-context-section">
        <div class="container">
            <span class="material-symbols-outlined">military_tech</span>
            <h2>“To understand man, we must look beyond the immediate.”</h2>
            <p>
                The AnthroConnect Knowledge Map is built on the philosophy that anthropology is not a collection of isolated facts,
                but a deeply interwoven tapestry of human experience.
            </p>
        </div>
    </section>

</div>
