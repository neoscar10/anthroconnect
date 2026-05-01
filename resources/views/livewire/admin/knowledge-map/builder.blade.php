<div class="km-builder" x-data="knowledgeMapBuilder({ 
        nodes: @js($nodes), 
        connections: @js($connections),
        zoom: @js($map->default_zoom),
        canvasWidth: @js($map->canvas_settings['width'] ?? 4000),
        canvasHeight: @js($map->canvas_settings['height'] ?? 3000)
     })" @mousemove.window="onMouseMove"
     @mouseup.window="endMove"
     @mouseleave.window="endMove">

    @push('styles')
        <link rel="stylesheet" href="{{ asset('admin/css/knowledge-map-builder.css') }}?v={{ filemtime(public_path('admin/css/knowledge-map-builder.css')) }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('admin/js/knowledge-map-builder.js') }}?v={{ filemtime(public_path('admin/js/knowledge-map-builder.js')) }}"></script>
    @endpush

    <div class="km-builder-shell"
         :class="{
            'left-collapsed': !leftPanelOpen,
            'right-collapsed': !rightPanelOpen,
            'right-expanded': rightPanelOpen,
            'km-focus-mode': focusMode
         }">

        <aside class="km-sidebar km-sidebar-left">
            <div class="km-sidebar-content">
                <div class="p-6 border-b border-stone-100 flex items-center justify-between">
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-stone-400">Node Library</h3>
                    <button type="button" @click="leftPanelOpen = false" class="text-stone-300 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-lg">first_page</span>
                    </button>
                </div>

                <div class="p-4 border-b border-stone-50 bg-stone-50/50">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                        <input type="text" placeholder="Search nodes..." class="w-full pl-9 pr-4 py-2 bg-white border border-stone-200 rounded-xl text-xs focus:ring-2 focus:ring-primary/20">
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-2">
                    @foreach($nodes as $node)
                        <button type="button"
                                draggable="true"
                                @dragstart="handleSidebarDragStart($event, {{ $node->id }})"
                                @click="selectNode({{ $node->id }}, true)"
                                class="w-full text-left p-3 rounded-xl hover:bg-stone-50 transition-all border border-transparent hover:border-stone-200 group">
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-lg flex items-center justify-center text-sm bg-stone-100 text-stone-500">
                                    <span class="material-symbols-outlined text-sm">description</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] font-bold text-stone-900 truncate">{{ $node->title }}</p>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>

                <div class="p-4 border-t border-stone-100">
                    <button type="button" wire:click="openAddNode" class="w-full py-3 bg-primary text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-all">
                        Create Node
                    </button>
                </div>
            </div>

            <div class="km-collapsed-icons">
                <button type="button" @click="leftPanelOpen = true" class="size-10 rounded-xl hover:bg-stone-100 flex items-center justify-center text-stone-400">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <div class="w-8 h-px bg-stone-100"></div>
                <button type="button" wire:click="openAddNode" class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center hover:bg-primary/20 transition-all">
                    <span class="material-symbols-outlined">add</span>
                </button>
                <button type="button" @click="resetZoom" class="size-10 rounded-xl hover:bg-stone-100 flex items-center justify-center text-stone-400">
                    <span class="material-symbols-outlined">restart_alt</span>
                </button>
            </div>
        </aside>

        <section class="km-canvas-shell" x-ref="shell">
            <div class="km-floating-toolbar">
                <div class="px-3 flex flex-col justify-center border-r border-stone-100 mr-2" x-show="!focusMode">
                    <span class="text-[10px] font-bold text-stone-900 leading-none">{{ $map->title }}</span>
                    <span class="text-[8px] font-bold uppercase text-stone-400 mt-1">{{ $map->status }}</span>
                </div>

                <button type="button" wire:click="openAddNode" class="km-toolbar-btn">
                    <span class="material-symbols-outlined text-sm">add_box</span>
                    <span x-show="!focusMode">Add Node</span>
                </button>

                <button type="button" @click="toggleConnectionMode()" class="km-toolbar-btn" :class="isConnectionMode ? 'text-white bg-[#0f8a45] shadow-lg shadow-emerald-500/20' : ''">
                    <span class="material-symbols-outlined text-sm">link</span>
                    <span x-show="!focusMode" x-text="isConnectionMode ? 'Connecting...' : 'Connect'"></span>
                </button>

                <div class="km-toolbar-divider"></div>

                <a href="{{ route('admin.knowledge-maps.edit') }}" class="km-toolbar-btn">
                    <span class="material-symbols-outlined text-sm">settings</span>
                    <span x-show="!focusMode">Settings</span>
                </a>

                <button type="button" @click="toggleFocus()" class="km-toolbar-btn" :class="focusMode ? 'text-primary' : ''">
                    <span class="material-symbols-outlined text-sm" x-text="focusMode ? 'fullscreen_exit' : 'fullscreen'"></span>
                    <span x-show="!focusMode">Focus</span>
                </button>

                <div class="km-toolbar-divider"></div>

                <div class="km-toolbar-status" x-show="!focusMode">
                    <span class="material-symbols-outlined">cloud_done</span>
                    <span>Saved</span>
                </div>
            </div>

            <div class="km-canvas-controls">
                <button type="button" @click="zoomIn" class="km-control-btn" title="Zoom In">
                    <span class="material-symbols-outlined text-lg">add</span>
                </button>
                <button type="button" @click="zoomOut" class="km-control-btn" title="Zoom Out">
                    <span class="material-symbols-outlined text-lg">remove</span>
                </button>
                <div class="w-px h-4 bg-stone-100 my-auto"></div>
                <button type="button" @click="fitView" class="km-control-btn" title="Fit to View">
                    <span class="material-symbols-outlined text-lg">fit_screen</span>
                </button>
                <button type="button" @click="resetZoom" class="km-control-btn" title="Reset Canvas">
                    <span class="material-symbols-outlined text-lg">restart_alt</span>
                </button>
            </div>

            <div class="km-canvas km-canvas-{{ $map->canvas_settings['background'] ?? 'grid' }}"
                 wire:ignore
                 x-ref="canvas"
                 :class="isConnectionMode ? 'cursor-crosshair' : (isPanning ? 'cursor-grabbing' : 'cursor-grab')"
                 :style="`transform: translate(${offsetX}px, ${offsetY}px) scale(${zoom})`"
                 @mousedown="handleCanvasPointerDown"
                 @click="handleCanvasClick"
                 @dragover="handleCanvasDragOver"
                 @drop="handleCanvasDrop">

                <template x-if="nodes.length === 0">
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                        <div class="text-center bg-white/80 backdrop-blur-md px-8 py-6 rounded-3xl border border-stone-200/50 shadow-sm">
                            <span class="material-symbols-outlined text-4xl text-stone-300 mb-2">account_tree</span>
                            <h3 class="text-lg font-headline font-bold text-stone-800">Empty Canvas</h3>
                            <p class="text-xs text-stone-500 max-w-xs mx-auto mt-1">Use the Add Node tool to start building your knowledge map.</p>
                        </div>
                    </div>
                </template>

                <template x-if="isConnectionMode">
                    <div class="absolute top-6 left-1/2 -translate-x-1/2 flex items-center justify-center pointer-events-none z-20 transition-all">
                        <div class="text-center bg-white/90 backdrop-blur-md px-6 py-3 rounded-full border border-[#0f8a45]/20 shadow-lg shadow-[#0f8a45]/5">
                            <p class="text-[11px] font-bold text-[#0f8a45] uppercase tracking-widest flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm animate-pulse" x-text="drawingFromNodeId ? 'target' : 'touch_app'"></span>
                                <span x-text="drawingFromNodeId ? 'Now click the target node.' : 'Click a source node.'"></span>
                                <span class="ml-2 px-2 py-0.5 bg-[#0f8a45]/10 rounded text-[9px]">ESC to cancel</span>
                            </p>
                        </div>
                    </div>
                </template>

                <svg class="km-connections-svg" x-ref="svg">
                    <defs>
                        <marker id="km-arrow" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto" markerUnits="strokeWidth">
                            <path d="M0,0 L0,6 L9,3 z" fill="#715b3e"></path>
                        </marker>
                        <marker id="km-arrow-selected" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto" markerUnits="strokeWidth">
                            <path d="M0,0 L0,6 L9,3 z" fill="#3b82f6"></path>
                        </marker>
                    </defs>

                    <g id="km-permanent-connections" x-ref="permanentConnectionsLayer"></g>

                    <g id="km-temp-connection-layer" x-ref="tempConnectionLayer">
                        <path x-show="isDrawingConnection" :d="getDrawingConnectionPath()" class="km-temp-connection" marker-end="url(#km-arrow)"></path>
                    </g>
                </svg>

                <template x-if="selectedConnectionId && getSelectedConnectionMidpoint()">
                    <div class="absolute z-30 transition-all duration-200 km-quick-actions"
                         :style="`left: ${getSelectedConnectionMidpoint().x}px; top: ${getSelectedConnectionMidpoint().y}px; transform: translate(-50%, -50%);`">
                        <button type="button" @click.stop="$wire.confirmDeleteConnection(selectedConnectionId)" class="size-8 bg-white border border-red-100 text-red-500 rounded-full flex items-center justify-center shadow-lg shadow-red-500/20 hover:bg-red-50 hover:scale-110 transition-all group">
                            <span class="material-symbols-outlined text-[16px] group-hover:text-red-600 transition-colors">delete</span>
                        </button>
                    </div>
                </template>

                <template x-for="node in nodes" :key="node.id">
                    <div class="km-node"
                         :data-node-id="node.id"
                         :class="{ 'is-selected': Number(selectedNodeId) === Number(node.id) }"
                         :style="`transform: translate(${node.position_x}px, ${node.position_y}px); cursor: ${isConnectionMode ? 'crosshair' : 'grab'}`"
                         @mousedown.stop="startDragNode($event, node)"
                         @mouseup.stop="handleNodeMouseUp($event, node)"
                         @click.stop="handleNodeClick($event, node)"
                         @dblclick.stop="handleNodeDblClick($event, node)">
                        <div class="km-node-card">
                            <div class="km-node-title" x-text="node.title"></div>
                            <template x-if="node.is_upsc_relevant">
                                <span class="km-node-upsc-badge">UPSC</span>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </section>

        <aside class="km-sidebar km-sidebar-right">
            <div class="km-sidebar-content">
                <div class="p-6 border-b border-stone-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-stone-400">Inspector</h3>
                        <p class="text-[11px] text-stone-400 mt-1">Configure selected canvas item</p>
                    </div>
                    <button type="button" @click="closeInspector()" class="text-stone-300 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-lg">last_page</span>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-5">
                    @if($selectedNode)
                        <div class="space-y-5">
                            <div class="p-5 rounded-2xl bg-stone-50 border border-stone-100">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Selected Node</p>
                                <h4 class="font-headline text-lg font-bold text-stone-900">{{ $selectedNode->title }}</h4>
                                <p class="text-xs text-stone-500 mt-2 leading-relaxed">{{ $selectedNode->short_description ?: 'No short description added yet.' }}</p>
                            </div>

                            <button type="button" wire:click="editNode({{ $selectedNode->id }})" class="w-full py-3 rounded-xl bg-primary text-white text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-primary/20">
                                Edit Node
                            </button>

                            <button type="button" wire:click="openAddConnection" class="w-full py-3 rounded-xl bg-white border border-stone-200 text-stone-700 text-[10px] font-bold uppercase tracking-widest">
                                Add Connection
                            </button>

                            <button type="button" wire:click="confirmDeleteNode({{ $selectedNode->id }})" class="w-full py-3 rounded-xl bg-red-50 border border-red-100 text-red-600 text-[10px] font-bold uppercase tracking-widest">
                                Delete Node
                            </button>
                        </div>
                    @elseif($selectedConnection)
                        <div class="space-y-5">
                            <div class="p-5 rounded-2xl bg-stone-50 border border-stone-100">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Selected Connection</p>
                                <h4 class="font-headline text-lg font-bold text-stone-900">
                                    {{ $selectedConnection->fromNode?->title }} → {{ $selectedConnection->toNode?->title }}
                                </h4>
                                <p class="text-xs text-stone-500 mt-2">
                                    Type: {{ str_replace('_', ' ', $selectedConnection->connection_type) }}
                                </p>
                            </div>

                            <button type="button" wire:click="confirmDeleteConnection({{ $selectedConnection->id }})" class="w-full py-3 rounded-xl bg-red-50 border border-red-100 text-red-600 text-[10px] font-bold uppercase tracking-widest">
                                Delete Connection
                            </button>
                        </div>
                    @else
                        <div class="h-full flex items-center justify-center text-center">
                            <div>
                                <div class="size-14 rounded-2xl bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-4">
                                    <span class="material-symbols-outlined">ads_click</span>
                                </div>
                                <h4 class="font-bold text-stone-800">Nothing selected</h4>
                                <p class="text-xs text-stone-400 mt-2 leading-relaxed">Click a node or connector line to open its actions here.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="km-collapsed-icons">
                <button type="button" @click="rightPanelOpen = true" class="size-10 rounded-xl hover:bg-stone-100 flex items-center justify-center text-stone-400">
                    <span class="material-symbols-outlined">tune</span>
                </button>
            </div>
        </aside>
    </div>

    @if($showNodeModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-sm">
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-5xl max-h-[92vh] overflow-hidden flex flex-col">
                <div class="p-8 border-b border-stone-100 bg-stone-50/50">
                    <h3 class="text-xl font-headline font-bold italic text-primary">{{ $editingNodeId ? 'Edit Node' : 'Create Node' }}</h3>
                    <p class="text-[9px] uppercase font-bold tracking-widest text-stone-400 mt-1">Configure knowledge map node details</p>
                </div>

                <div class="p-8 overflow-y-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Title</label>
                                <input type="text" wire:model.defer="nodeTitle" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                                @error('nodeTitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Short Description</label>
                                <textarea wire:model.defer="nodeShortDescription" rows="3" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20"></textarea>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Full Description</label>
                                <textarea wire:model.defer="nodeFullDescription" rows="7" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20"></textarea>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Node Type</label>
                                    <select wire:model.defer="nodeType" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm">
                                        <option value="concept">Concept</option>
                                        <option value="theory">Theory</option>
                                        <option value="person">Person</option>
                                        <option value="period">Period</option>
                                        <option value="topic">Topic</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Importance</label>
                                    <select wire:model.defer="nodeImportance" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm">
                                        <option value="primary">Primary</option>
                                        <option value="secondary">Secondary</option>
                                        <option value="supporting">Supporting</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Tags</label>
                                <select wire:model.defer="nodeTags" multiple class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm min-h-[140px]">
                                    @foreach($tagGroups as $group)
                                        <optgroup label="{{ $group->name }}">
                                            @foreach($group->tags as $tag)
                                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <select wire:model.defer="nodeConceptId" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm">
                                    <option value="">Link core concept...</option>
                                    @foreach($concepts as $concept)
                                        <option value="{{ $concept->id }}">{{ $concept->title }}</option>
                                    @endforeach
                                </select>

                                <select wire:model.defer="nodeAnthropologistId" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm">
                                    <option value="">Link anthropologist...</option>
                                    @foreach($anthropologists as $anthropologist)
                                        <option value="{{ $anthropologist->id }}">{{ $anthropologist->full_name }}</option>
                                    @endforeach
                                </select>

                                <select wire:model.defer="nodeTheoryId" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm">
                                    <option value="">Link theory...</option>
                                    @foreach($theories as $theory)
                                        <option value="{{ $theory->id }}">{{ $theory->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <label class="flex items-center justify-between p-4 bg-stone-50 rounded-2xl cursor-pointer">
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-stone-700">UPSC Relevant</span>
                                    <input type="checkbox" wire:model.defer="nodeIsUpsc" class="rounded text-primary">
                                </label>

                                <label class="flex items-center justify-between p-4 bg-stone-50 rounded-2xl cursor-pointer">
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-stone-700">Members Only</span>
                                    <input type="checkbox" wire:model.defer="nodeIsMembersOnly" class="rounded text-primary">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-stone-50/50 border-t border-stone-100 flex justify-end gap-4">
                    <button type="button" wire:click="closeNodeModal" class="px-8 py-3 bg-white text-stone-600 rounded-xl text-[10px] font-bold uppercase tracking-widest border border-stone-200 hover:bg-stone-50 transition-all">
                        Cancel
                    </button>
                    <button type="button" wire:click="saveNode" class="px-10 py-3 bg-primary text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-all">
                        {{ $editingNodeId ? 'Update Node' : 'Create Node' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showConnectionModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-sm">
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden">
                <div class="p-8 border-b border-stone-100 bg-stone-50/50">
                    <h3 class="text-xl font-headline font-bold italic text-primary">Create Connection</h3>
                    <p class="text-[9px] uppercase font-bold tracking-widest text-stone-400 mt-1">Define the relationship between nodes</p>
                </div>

                <div class="p-8 space-y-6">
                    @error('connection')
                        <div class="p-3 bg-red-50 text-red-600 rounded-xl text-[10px] font-bold uppercase border border-red-100">
                            {{ $message }}
                        </div>
                    @enderror

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">From Node</label>
                        <select wire:model="connFromId" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                            <option value="">Select source...</option>
                            @foreach($nodes as $node)
                                <option value="{{ $node->id }}">{{ $node->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">To Node</label>
                        <select wire:model="connToId" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                            <option value="">Select target...</option>
                            @foreach($nodes as $node)
                                <option value="{{ $node->id }}">{{ $node->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Type</label>
                            <select wire:model="connType" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                                <option value="relates_to">Relates To</option>
                                <option value="leads_to">Leads To</option>
                                <option value="depends_on">Depends On</option>
                                <option value="influenced_by">Influenced By</option>
                                <option value="example_of">Example Of</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Style</label>
                            <select wire:model="connLineStyle" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                                <option value="solid">Solid</option>
                                <option value="dashed">Dashed</option>
                                <option value="dotted">Dotted</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-stone-50/50 border-t border-stone-100 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showConnectionModal', false)" class="px-6 py-2.5 text-stone-600 text-[10px] font-bold uppercase tracking-widest hover:underline">
                        Cancel
                    </button>
                    <button type="button" wire:click="saveConnection" class="px-8 py-2.5 bg-primary text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-all">
                        Create Connection
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteConfirmModal)
        <div class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-md transition-all">
            <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden">
                <div class="p-8 text-center">
                    <div class="size-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-4xl">warning</span>
                    </div>

                    <h3 class="text-xl font-headline font-bold text-stone-900 mb-2">Are you absolutely sure?</h3>
                    <p class="text-xs text-stone-500 leading-relaxed">
                        This action cannot be undone.
                        {{ $nodeToDeleteId ? 'Deleting this node will also remove all its connections.' : 'This connection will be permanently removed.' }}
                    </p>
                </div>

                <div class="p-6 bg-stone-50 flex gap-3">
                    <button type="button" wire:click="cancelDelete" class="flex-1 py-3 bg-white border border-stone-200 text-stone-600 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-stone-100 transition-all">
                        Nevermind
                    </button>
                    <button type="button" wire:click="performDelete" class="flex-1 py-3 bg-red-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-red-600/20 hover:bg-red-700 hover:-translate-y-0.5 transition-all">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>