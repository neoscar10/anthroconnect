<div class="km-builder" x-data="knowledgeMapBuilder({ 
        nodes: @js($canvasNodes), 
        connections: @js($connections),
        zoom: @js($map->default_zoom),
        canvasWidth: @js($map->canvas_settings['width'] ?? 4000),
        canvasHeight: @js($map->canvas_settings['height'] ?? 3000),
        isConnectionMode: @entangle('isConnectionMode')
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
                        <p class="text-[11px] text-stone-400 mt-1">Configure selected item</p>
                    </div>
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
                                Delete Node Permanently
                            </button>

                            <button type="button" wire:click="removeNodeFromCanvas({{ $selectedNode->id }})" class="w-full py-3 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 text-[10px] font-bold uppercase tracking-widest">
                                Remove from Canvas
                            </button>
                        </div>
                    @elseif($selectedConnection)
                        <div class="space-y-5">
                            <div class="p-5 rounded-2xl bg-stone-50 border border-stone-100">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Selected Connection</p>
                                <h4 class="font-headline text-lg font-bold text-stone-900">
                                    {{ $selectedConnection->fromNode?->title }} → {{ $selectedConnection->toNode?->title }}
                                </h4>

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


        </aside>
    </div>

    @if($showNodeModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-12">
            <div class="fixed inset-0 bg-stone-900/60 backdrop-blur-md" wire:click="closeNodeModal"></div>
            <div class="bg-white rounded-[48px] shadow-2xl w-full max-w-6xl relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
                <!-- Modal Header -->
                <div class="px-8 py-5 border-b border-outline-variant/10 bg-surface-container-low/30 flex justify-between items-center shrink-0">
                    <div>
                        <h2 class="font-headline text-2xl italic font-bold text-primary">{{ $editingNodeId ? 'Edit Knowledge Node' : 'Catalog New Node' }}</h2>
                        <p class="text-[9px] uppercase tracking-widest text-on-surface-variant font-bold">Knowledge Map Entry Portal</p>
                    </div>
                    <button wire:click="closeNodeModal" class="w-10 h-10 rounded-xl hover:bg-stone-100 transition-colors flex items-center justify-center text-stone-400 hover:text-on-surface">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto p-8 md:p-10 space-y-10 bg-stone-50/50">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        <!-- Left Column: Content -->
                        <div class="space-y-8">
                            <div class="space-y-5">
                                <h3 class="text-[9px] font-bold uppercase tracking-widest text-primary flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                    Core Metadata
                                </h3>
                                <div class="space-y-4">
                                    <div class="space-y-1">
                                        <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Node Title</label>
                                        <input type="text" wire:model.defer="nodeTitle" class="w-full bg-white border border-outline-variant/20 rounded-xl p-3.5 text-base font-headline italic font-bold focus:ring-2 focus:ring-primary outline-none transition-all">
                                        @error('nodeTitle') <p class="text-[9px] text-error mt-1 font-bold">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="space-y-1">
                                        <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Short Description</label>
                                        <textarea wire:model.defer="nodeShortDescription" rows="2" class="w-full bg-white border border-outline-variant/20 rounded-xl p-3.5 text-sm font-medium focus:ring-2 focus:ring-primary outline-none resize-none"></textarea>
                                    </div>

                                    <div class="space-y-1">
                                        <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Full Narrative / Description</label>
                                        <textarea wire:model.defer="nodeFullDescription" rows="8" class="w-full bg-white border border-outline-variant/20 rounded-2xl p-4 text-sm leading-relaxed focus:ring-2 focus:ring-primary outline-none transition-all resize-none"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Settings & Linkages -->
                        <div class="space-y-8">
                            <div class="space-y-5">
                                <h3 class="text-[9px] font-bold uppercase tracking-widest text-secondary flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                    Classification & Linkages
                                </h3>
                                


                                <div class="space-y-1">
                                    <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Knowledge Taxonomy (Tags)</label>
                                    <x-admin.tag-selector id="node-tag-selector" wire:model="nodeTags" />
                                </div>

                                <div class="space-y-4 pt-6 border-t border-outline-variant/10">
                                    <h3 class="text-[9px] font-bold uppercase tracking-widest text-secondary flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                        Reference Linkages
                                    </h3>
                                    <div class="space-y-2">
                                        <select wire:model.defer="nodeConceptId" class="w-full bg-white border border-outline-variant/10 rounded-xl p-3 text-[10px] font-bold uppercase tracking-widest focus:ring-1 focus:ring-primary cursor-pointer">
                                            <option value="">Link core concept...</option>
                                            @foreach($concepts as $concept)
                                                <option value="{{ $concept->id }}">{{ $concept->title }}</option>
                                            @endforeach
                                        </select>

                                        <select wire:model.defer="nodeAnthropologistId" class="w-full bg-white border border-outline-variant/10 rounded-xl p-3 text-[10px] font-bold uppercase tracking-widest focus:ring-1 focus:ring-primary cursor-pointer">
                                            <option value="">Link anthropologist...</option>
                                            @foreach($anthropologists as $anthropologist)
                                                <option value="{{ $anthropologist->id }}">{{ $anthropologist->full_name }}</option>
                                            @endforeach
                                        </select>

                                        <select wire:model.defer="nodeTheoryId" class="w-full bg-white border border-outline-variant/10 rounded-xl p-3 text-[10px] font-bold uppercase tracking-widest focus:ring-1 focus:ring-primary cursor-pointer">
                                            <option value="">Link theory...</option>
                                            @foreach($theories as $theory)
                                                <option value="{{ $theory->id }}">{{ $theory->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-4 pt-6 border-t border-outline-variant/10">
                                    <h3 class="text-[9px] font-bold uppercase tracking-widest text-orange-700 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-700"></span>
                                        Study Materials & Attachments
                                    </h3>
                                    
                                    <div class="space-y-4 bg-white border border-outline-variant/10 p-5 rounded-2xl">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Material Type</label>
                                                <select wire:model.live="nodeLmsMaterialType" class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-[10px] font-bold uppercase tracking-widest focus:ring-1 focus:ring-primary cursor-pointer">
                                                    <option value="video">LMS Video/Lesson</option>
                                                    <option value="module">LMS Module</option>
                                                    <option value="module_resource">Module Resource</option>
                                                    <option value="library_resource">Library Resource</option>
                                                </select>
                                            </div>

                                            @if(in_array($nodeLmsMaterialType, ['video', 'module', 'module_resource']))
                                                <div class="space-y-1">
                                                    <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">LMS Module</label>
                                                    <select wire:model.live="nodeLmsModuleId" class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-[10px] font-bold uppercase tracking-widest focus:ring-1 focus:ring-primary cursor-pointer">
                                                        <option value="">Select Module...</option>
                                                        @foreach($lmsModules as $module)
                                                            <option value="{{ $module->id }}">{{ $module->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 gap-4">
                                            @if($nodeLmsMaterialType === 'video')
                                                <div class="space-y-1">
                                                    <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Select Lesson/Video</label>
                                                    <select wire:model="nodeLmsLessonId" class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-[10px] font-bold uppercase tracking-widest focus:ring-1 focus:ring-primary cursor-pointer">
                                                        <option value="">Select Lesson...</option>
                                                        @foreach($this->lmsLessons as $lesson)
                                                            <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @elseif($nodeLmsMaterialType === 'module_resource')
                                                <div class="space-y-1">
                                                    <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Select Module Resource</label>
                                                    <select wire:model="nodeLmsResourceId" class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-[10px] font-bold uppercase tracking-widest focus:ring-1 focus:ring-primary cursor-pointer">
                                                        <option value="">Select Resource...</option>
                                                        @foreach($this->lmsResources as $res)
                                                            <option value="{{ $res->id }}">{{ $res->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @elseif($nodeLmsMaterialType === 'library_resource')
                                                <div class="space-y-1">
                                                    <label class="text-[8px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Select Library Resource</label>
                                                    <select wire:model="nodeLibraryResourceId" class="w-full bg-stone-50 border border-stone-200 rounded-xl p-2.5 text-[10px] font-bold uppercase tracking-widest focus:ring-1 focus:ring-primary cursor-pointer">
                                                        <option value="">Select Resource...</option>
                                                        @foreach($libraryResources as $res)
                                                            <option value="{{ $res->id }}">{{ $res->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>

                                        <button type="button" wire:click="addAttachment" class="w-full py-2.5 bg-stone-900 text-white rounded-xl text-[9px] font-bold uppercase tracking-widest shadow-md hover:bg-stone-800 transition-all flex items-center justify-center gap-2">
                                            <span class="material-symbols-outlined text-sm">attach_file</span>
                                            Attach Material
                                        </button>
                                    </div>

                                    @if(count($selectedAttachments))
                                        <div class="space-y-2 max-h-[200px] overflow-y-auto pr-2">
                                            @foreach($selectedAttachments as $index => $att)
                                                <div class="flex items-center justify-between p-3 bg-white border border-stone-100 rounded-xl group hover:border-primary/20 transition-all">
                                                    <div class="flex items-center gap-3">
                                                        <div class="size-7 rounded-lg bg-stone-50 flex items-center justify-center text-stone-400">
                                                            <span class="material-symbols-outlined text-sm">
                                                                @if($att['type'] === 'video') play_circle
                                                                @elseif($att['type'] === 'module') category
                                                                @elseif($att['type'] === 'module_resource') description
                                                                @else auto_stories
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <p class="text-[10px] font-bold text-stone-900 line-clamp-1">{{ $att['title'] }}</p>
                                                            <p class="text-[8px] text-stone-400 uppercase font-bold tracking-widest">{{ str_replace('_', ' ', $att['type']) }}</p>
                                                        </div>
                                                    </div>
                                                    <button type="button" wire:click="removeAttachment({{ $index }})" class="size-7 rounded-lg hover:bg-red-50 text-stone-300 hover:text-red-500 transition-all flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-sm">close</span>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="space-y-4 pt-6 border-t border-outline-variant/10">
                                    <div class="flex flex-wrap gap-6">
                                        <label class="flex items-center gap-3 cursor-pointer group w-fit">
                                            <div class="relative inline-flex items-center">
                                                <input wire:model.defer="nodeIsUpsc" type="checkbox" class="sr-only peer">
                                                <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                            </div>
                                            <span class="text-[8px] font-bold text-on-surface uppercase tracking-widest">UPSC Relevant</span>
                                        </label>

                                        <label class="flex items-center gap-3 cursor-pointer group w-fit">
                                            <div class="relative inline-flex items-center">
                                                <input wire:model.defer="nodeIsMembersOnly" type="checkbox" class="sr-only peer">
                                                <div class="w-10 h-5 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                            </div>
                                            <span class="text-[8px] font-bold text-on-surface uppercase tracking-widest">Members Only</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Modal Footer -->
                <div class="px-8 py-5 bg-white border-t border-outline-variant/10 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2.5 opacity-50">
                        <span class="material-symbols-outlined text-base">lock</span>
                        <span class="text-[8px] font-bold uppercase tracking-widest">Knowledge Entry Secure</span>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" wire:click="closeNodeModal" class="px-6 py-3 rounded-xl text-[9px] font-bold uppercase tracking-widest text-on-surface-variant hover:bg-stone-100 transition-all">Cancel</button>
                        <button type="button" wire:click="saveNode" wire:loading.attr="disabled" class="bg-primary text-on-primary px-10 py-3 rounded-xl font-bold text-[9px] uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all">
                            <span wire:loading.remove wire:target="saveNode">{{ $editingNodeId ? 'Update Node' : 'Catalog Node' }}</span>
                            <span wire:loading wire:target="saveNode">Processing...</span>
                        </button>
                    </div>
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

                    <div class="grid grid-cols-2 gap-4" style="display: none;">
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