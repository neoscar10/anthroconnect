<div class="km-builder" 
     x-data="knowledgeMapBuilder({ 
        nodes: @js($nodes), 
        connections: @js($connections),
        zoom: @js($map->default_zoom)
     })"
     @mousemove="onMouseMove"
     @mouseup="endMove"
     @mouseleave="endMove">
    
    @push('styles')
        <link rel="stylesheet" href="{{ asset('admin/css/knowledge-map-builder.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('admin/js/knowledge-map-builder.js') }}"></script>
    @endpush

    <div class="km-builder-shell" :class="{ 
        'left-collapsed': !leftPanelOpen, 
        'right-collapsed': !rightPanelOpen,
        'right-expanded': rightPanelOpen,
        'km-focus-mode': focusMode
    }">
        <!-- Left Sidebar: Node Library -->
        <aside class="km-sidebar km-sidebar-left">
            <div class="km-sidebar-content">
                <div class="p-6 border-b border-stone-100 flex items-center justify-between">
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-stone-400">Node Library</h3>
                    <button @click="leftPanelOpen = false" class="text-stone-300 hover:text-primary transition-colors">
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
                        <button draggable="true" 
                                @dragstart="handleSidebarDragStart($event, {{ $node->id }})"
                                @click="selectNode({{ $node->id }}, true);" 
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
                    <button wire:click="openAddNode" class="w-full py-3 bg-primary text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-all">
                        Create Node
                    </button>
                </div>
            </div>

            <!-- Collapsed Icons -->
            <div class="km-collapsed-icons">
                <button @click="leftPanelOpen = true" class="size-10 rounded-xl hover:bg-stone-100 flex items-center justify-center text-stone-400">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <div class="w-8 h-px bg-stone-100"></div>
                <button class="size-10 rounded-xl hover:bg-stone-100 flex items-center justify-center text-stone-400">
                    <span class="material-symbols-outlined">search</span>
                </button>
                <button wire:click="openAddNode" class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center hover:bg-primary/20 transition-all">
                    <span class="material-symbols-outlined">add</span>
                </button>
                <button @click="resetZoom" class="size-10 rounded-xl hover:bg-stone-100 flex items-center justify-center text-stone-400">
                    <span class="material-symbols-outlined">restart_alt</span>
                </button>
            </div>
        </aside>

        <!-- Main Canvas -->
        <section class="km-canvas-shell" x-ref="shell" @mousedown="startPan">
            <!-- Floating Top Toolbar -->
            <div class="km-floating-toolbar">
                <div class="px-3 flex flex-col justify-center border-r border-stone-100 mr-2" x-show="!focusMode">
                    <span class="text-[10px] font-bold text-stone-900 leading-none">{{ $map->title }}</span>
                    <span class="text-[8px] font-bold uppercase text-stone-400 mt-1">{{ $map->status }}</span>
                </div>

                <button wire:click="openAddNode" class="km-toolbar-btn">
                    <span class="material-symbols-outlined text-sm">add_box</span>
                    <span x-show="!focusMode">Add Node</span>
                </button>
                <button @click="toggleConnectionMode()" class="km-toolbar-btn" :class="isConnectionMode ? 'text-white bg-[#0f8a45] shadow-lg shadow-emerald-500/20' : ''">
                    <span class="material-symbols-outlined text-sm">link</span>
                    <span x-show="!focusMode" x-text="isConnectionMode ? 'Connecting...' : 'Connect'"></span>
                </button>
                
                <div class="km-toolbar-divider"></div>
                
                <a href="{{ route('admin.knowledge-maps.edit') }}" class="km-toolbar-btn">
                    <span class="material-symbols-outlined text-sm">settings</span>
                    <span x-show="!focusMode">Settings</span>
                </a>

                <button @click="toggleFocus()" class="km-toolbar-btn" :class="focusMode ? 'text-primary' : ''">
                    <span class="material-symbols-outlined text-sm" x-text="focusMode ? 'fullscreen_exit' : 'fullscreen'"></span>
                    <span x-show="!focusMode">Focus</span>
                </button>

                <div class="km-toolbar-divider"></div>
                
                <div class="km-toolbar-status" x-show="!focusMode">
                    <span class="material-symbols-outlined">cloud_done</span>
                    <span>Saved</span>
                </div>
            </div>

            <!-- Canvas Controls -->
            <div class="km-canvas-controls">
                <button @click="zoomIn" class="km-control-btn" title="Zoom In">
                    <span class="material-symbols-outlined text-lg">add</span>
                </button>
                <button @click="zoomOut" class="km-control-btn" title="Zoom Out">
                    <span class="material-symbols-outlined text-lg">remove</span>
                </button>
                <div class="w-px h-4 bg-stone-100 my-auto"></div>
                <button @click="fitView" class="km-control-btn" title="Fit to View">
                    <span class="material-symbols-outlined text-lg">fit_screen</span>
                </button>
                <button @click="resetZoom" class="km-control-btn" title="Reset Canvas">
                    <span class="material-symbols-outlined text-lg">restart_alt</span>
                </button>
            </div>

            <!-- The actual draggable canvas -->
            <div class="km-canvas km-canvas-grid" 
                 wire:ignore
                 x-ref="canvas"
                 :style="`transform: translate(${offsetX}px, ${offsetY}px) scale(${zoom})`"
                 @mousedown="startPan"
                 @click="handleCanvasClick"
                 @dragover="handleCanvasDragOver"
                 @drop="handleCanvasDrop">
                
                <template x-if="nodes.length === 0">
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                        <div class="text-center bg-white/80 backdrop-blur-md px-8 py-6 rounded-3xl border border-stone-200/50 shadow-sm">
                            <span class="material-symbols-outlined text-4xl text-stone-300 mb-2">account_tree</span>
                            <h3 class="text-lg font-headline font-bold text-stone-800">Empty Canvas</h3>
                            <p class="text-xs text-stone-500 max-w-xs mx-auto mt-1">Use the "Add Node" tool to start building your knowledge map.</p>
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
                
                <!-- Connections Layer -->
                <svg class="km-connections-svg" x-ref="svg">
                    <defs>
                        <marker id="km-arrow" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto" markerUnits="strokeWidth">
                            <path d="M0,0 L0,6 L9,3 z" fill="#715b3e"></path>
                        </marker>
                    </defs>

                    <!-- permanent saved connections -->
                    <g id="km-permanent-connections" x-ref="permanentConnectionsLayer"></g>

                    <!-- temporary drawing preview -->
                    <g id="km-temp-connection-layer" x-ref="tempConnectionLayer">
                        <path x-show="isDrawingConnection" :d="getDrawingConnectionPath()" class="km-temp-connection" marker-end="url(#km-arrow)"></path>
                    </g>
                </svg>

                <!-- Nodes Layer -->
                <template x-for="node in nodes" :key="node.id">
                    <div class="km-node"
                         :data-node-id="node.id"
                         :class="{ 'is-selected': selectedNodeId === node.id }"
                         :style="`transform: translate(${node.position_x}px, ${node.position_y}px); cursor: ${isConnectionMode ? 'crosshair' : 'grab'}`"
                         @mousedown.stop="startDragNode($event, node)"
                         @mouseup.stop="handleNodeMouseUp($event, node)"
                         @click.stop="handleNodeClick($event, node)" @dblclick.stop="handleNodeDblClick($event, node)">
                        
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

        <!-- Right Sidebar: Inspector -->
        <aside class="km-sidebar km-sidebar-right">
            <!-- Collapsed Tab -->
            <div class="km-collapsed-icons">
                <button @click="rightPanelOpen = true" class="size-10 rounded-xl hover:bg-stone-100 flex items-center justify-center text-stone-400">
                    <span class="material-symbols-outlined">info</span>
                </button>
            </div>

            <div class="km-sidebar-content" wire:key="sidebar-content-{{ $selectedNodeId ?? ($selectedConnectionId ?? 'empty') }}">
                @if($selectedNode)
                    <div class="flex flex-col h-full" wire:key="inspector-node-{{ $selectedNode->id }}">
                        <div class="p-6 border-b border-stone-100">
                            <div class="flex items-center justify-between mb-6">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-stone-400">Node Inspector</span>
                                <button @click="closeInspector()" class="size-8 rounded-full hover:bg-stone-50 transition-colors flex items-center justify-center text-stone-400">
                                    <span class="material-symbols-outlined text-lg">last_page</span>
                                </button>
                            </div>
                            <div class="flex items-center gap-4 mb-6">
                                <div class="size-14 rounded-[1.25rem] bg-primary/5 flex items-center justify-center text-primary shadow-inner">
                                    <span class="material-symbols-outlined text-2xl">description</span>
                                </div>
                                <div>
                                    <h4 class="text-lg font-headline font-bold text-stone-900 leading-tight">{{ $selectedNode->title }}</h4>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="editNode({{ $selectedNode->id }})" class="flex-1 py-2.5 bg-stone-100 text-stone-600 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-stone-200 transition-all flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-xs">edit_note</span> Edit
                                </button>
                                <button wire:click="confirmDeleteNode({{ $selectedNode->id }})" class="flex-1 py-2.5 bg-red-50 text-red-600 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-red-100 transition-all flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-xs">delete</span> Delete
                                </button>
                            </div>
                        </div>
                        <div class="flex-1 overflow-y-auto p-6 space-y-8">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-3">Description</p>
                                <p class="text-xs text-stone-600 leading-relaxed">{{ $selectedNode->short_description ?: 'No description provided.' }}</p>
                            </div>

                            @if($selectedNode->tags->count() > 0)
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-3">Scholarly Tags</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($selectedNode->tags as $tag)
                                            <span class="bg-stone-50 text-stone-500 border border-stone-100 px-3 py-1 rounded-full text-[9px] font-bold">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="space-y-4">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-3">Linked Resources</p>
                                
                                @if($selectedNode->encyclopediaConcept)
                                    <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100 flex items-center gap-4">
                                        <div class="size-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-lg">local_library</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-bold text-blue-900 truncate">{{ $selectedNode->encyclopediaConcept->title }}</p>
                                            <p class="text-[8px] uppercase font-bold text-blue-400 tracking-widest mt-0.5">Core Concept</p>
                                        </div>
                                    </div>
                                @endif

                                @if($selectedNode->anthropologist)
                                    <div class="p-4 bg-amber-50/50 rounded-2xl border border-amber-100 flex items-center gap-4">
                                        <div class="size-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-lg">person</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-bold text-amber-900 truncate">{{ $selectedNode->anthropologist->full_name }}</p>
                                            <p class="text-[8px] uppercase font-bold text-amber-400 tracking-widest mt-0.5">Anthropologist</p>
                                        </div>
                                    </div>
                                @endif

                                @if($selectedNode->lmsModule)
                                    <div class="p-4 bg-green-50/50 rounded-2xl border border-green-100 flex items-center gap-4">
                                        <div class="size-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-lg">school</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-bold text-green-900 truncate">{{ $selectedNode->lmsModule->title }}</p>
                                            <p class="text-[8px] uppercase font-bold text-green-400 tracking-widest mt-0.5">Primary Module</p>
                                        </div>
                                    </div>
                                @endif

                                @foreach($selectedNode->attachments as $attachment)
                                    @php
                                        $attType = match($attachment->attachable_type) {
                                            \App\Models\Lms\LmsModule::class => ['label' => 'Module', 'icon' => 'school', 'color' => 'bg-green-50 text-green-600 border-green-100'],
                                            \App\Models\Lms\LmsLesson::class => ['label' => 'Video', 'icon' => 'play_circle', 'color' => 'bg-red-50 text-red-600 border-red-100'],
                                            \App\Models\Lms\LmsResource::class => ['label' => 'Resource', 'icon' => 'description', 'color' => 'bg-blue-50 text-blue-600 border-blue-100'],
                                            \App\Models\LibraryResource::class => ['label' => 'Library', 'icon' => 'menu_book', 'color' => 'bg-amber-50 text-amber-600 border-amber-100'],
                                            default => ['label' => 'Material', 'icon' => 'attachment', 'color' => 'bg-stone-50 text-stone-600 border-stone-100']
                                        };
                                    @endphp
                                    <div class="p-4 {{ $attType['color'] }} rounded-2xl border flex items-center gap-4">
                                        <div class="size-10 rounded-xl bg-white/50 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-lg">{{ $attType['icon'] }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-bold truncate">{{ $attachment->attachable?->title ?? 'Deleted Resource' }}</p>
                                            <p class="text-[8px] uppercase font-bold opacity-60 tracking-widest mt-0.5">{{ $attType['label'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @elseif($selectedConnection)
                    <div class="flex flex-col h-full">
                        <div class="p-6 border-b border-stone-100">
                            <div class="flex items-center justify-between mb-8">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-stone-400">Relationship Inspector</span>
                                <button @click="closeInspector()" class="size-8 rounded-full hover:bg-stone-50 transition-colors flex items-center justify-center text-stone-400">
                                    <span class="material-symbols-outlined text-lg">last_page</span>
                                </button>
                            </div>
                            <div class="bg-stone-50 p-6 rounded-[2rem] mb-8 border border-stone-100 shadow-inner">
                                <div class="flex items-center justify-between text-[10px] font-black text-stone-400 uppercase tracking-[0.15em] mb-4">
                                    <span class="truncate max-w-[80px]">{{ $selectedConnection->fromNode->title }}</span>
                                    <span class="material-symbols-outlined text-primary">arrow_forward</span>
                                    <span class="truncate max-w-[80px]">{{ $selectedConnection->toNode->title }}</span>
                                </div>
                                <p class="text-sm font-bold text-primary">{{ str_replace('_', ' ', $selectedConnection->connection_type) }}</p>
                            </div>
                            <button wire:click="confirmDeleteConnection({{ $selectedConnection->id }})" class="w-full py-3 bg-red-50 text-red-600 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-red-100 transition-all border border-red-100">
                                Sever Connection
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </aside>
    </div>

    <!-- Add/Edit Node Modal -->
    @if($showNodeModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-sm">
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-8 border-b border-stone-100 flex justify-between items-center bg-stone-50/50">
                    <div>
                        <h3 class="text-2xl font-headline font-bold italic text-primary">{{ $editingNodeId ? 'Edit Node' : 'Add New Node' }}</h3>
                        <p class="text-[10px] uppercase font-bold tracking-widest text-stone-400 mt-1">Configure node properties and scholarly links</p>
                    </div>
                    <button wire:click="closeNodeModal" class="size-10 rounded-full hover:bg-white transition-colors flex items-center justify-center text-stone-400">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <!-- Left: Basic Config -->
                        <div class="space-y-6">
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">settings</span> Basic Configuration
                                </h4>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Title</label>
                                    <input wire:model="nodeTitle" type="text" class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 placeholder:text-stone-300" placeholder="Enter node title...">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Description</label>
                                    <textarea wire:model="nodeShortDescription" rows="3" class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 placeholder:text-stone-300" placeholder="Briefly describe this node..."></textarea>
                                </div>
                            </div>

                            <div class="space-y-4 pt-4 border-t border-stone-100">
                                <h4 class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">sell</span> Categorization
                                </h4>
                                <div class="space-y-3">
                                    @foreach($tagGroups as $group)
                                         <div x-data="{ open: false }" class="border border-stone-100 rounded-2xl overflow-hidden bg-stone-50/50 mb-3 last:mb-0 transition-all" :class="open ? 'ring-1 ring-primary/10 shadow-sm' : ''">
                                             <button @click="open = !open" type="button" class="w-full flex items-center justify-between p-4 hover:bg-stone-50 transition-colors">
                                                 <div class="flex items-center gap-3">
                                                     <div class="size-2 rounded-full shadow-sm" style="background-color: {{ $group->color ?? '#6366f1' }}"></div>
                                                     <span class="text-[10px] font-bold text-stone-600 uppercase tracking-widest">{{ $group->name }}</span>
                                                 </div>
                                                 <div class="flex items-center gap-2">
                                                     <span class="text-[8px] font-black text-stone-400 bg-stone-100 px-2 py-0.5 rounded-full">{{ $group->tags->count() }} tags</span>
                                                     <span class="material-symbols-outlined text-stone-300 text-sm transition-transform duration-300" :class="{ 'rotate-180': open }">expand_more</span>
                                                 </div>
                                             </button>
                                             <div x-show="open" 
                                                  x-collapse
                                                  class="p-4 pt-1 flex flex-wrap gap-2">
                                                 <div class="w-full h-px bg-stone-100 mb-3 mt-1 opacity-50"></div>
                                                 @foreach($group->tags as $tag)
                                                     <label class="cursor-pointer group">
                                                         <input type="checkbox" wire:model="nodeTags" value="{{ $tag->id }}" class="hidden peer">
                                                         <span class="px-3 py-1.5 rounded-xl border border-stone-200 text-[10px] font-bold uppercase tracking-widest bg-white text-stone-500 peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary group-hover:border-primary/30 transition-all inline-block shadow-sm">
                                                             {{ $tag->name }}
                                                         </span>
                                                     </label>
                                                 @endforeach
                                             </div>
                                         </div>
                                     @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Right: Scholarly Links -->
                        <div class="space-y-6">
                            <h4 class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">link</span> Scholarly Links
                            </h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Encyclopedia Concept</label>
                                    <select wire:model="nodeConceptId" class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                                        <option value="">None</option>
                                        @foreach($concepts as $concept)
                                            <option value="{{ $concept->id }}">{{ $concept->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Influential Thinker</label>
                                    <select wire:model="nodeAnthropologistId" class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                                        <option value="">None</option>
                                        @foreach($anthropologists as $thinker)
                                            <option value="{{ $thinker->id }}">{{ $thinker->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Major Theory</label>
                                    <select wire:model="nodeTheoryId" class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                                        <option value="">None</option>
                                        @foreach($theories as $theory)
                                            <option value="{{ $theory->id }}">{{ $theory->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Multi-step Material Selection -->
                                <div class="pt-4 space-y-4">
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-primary mb-2">Linked Study Material</label>
                                    
                                    <div class="space-y-4 bg-stone-50/50 p-4 rounded-2xl border border-stone-100">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">1. Material Type</label>
                                                <select wire:model.live="nodeLmsMaterialType" class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20">
                                                    <option value="module">Course Module</option>
                                                    <option value="video">Module Video</option>
                                                    <option value="module_resource">Module Resource (PDF)</option>
                                                    <option value="library_resource">Library Resource</option>
                                                </select>
                                            </div>
                                            <div class="flex items-end">
                                                <button type="button" wire:click="addAttachment" class="w-full py-3 bg-primary text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-all">
                                                    Add to List
                                                </button>
                                            </div>
                                        </div>

                                        @if($nodeLmsMaterialType === 'module')
                                            <div class="animate-in fade-in slide-in-from-top-2 duration-300">
                                                <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">2. Select LMS Module</label>
                                                <select wire:model.live="nodeLmsModuleId" class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20">
                                                    <option value="">Choose a module...</option>
                                                    @foreach($lmsModules as $module)
                                                        <option value="{{ $module->id }}">{{ $module->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @elseif($nodeLmsMaterialType === 'video')
                                            <div class="space-y-4 animate-in fade-in slide-in-from-top-2 duration-300">
                                                <div>
                                                    <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">2. Select Module</label>
                                                    <select wire:model.live="nodeLmsModuleId" class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20">
                                                        <option value="">Choose module...</option>
                                                        @foreach($lmsModules as $module)
                                                            <option value="{{ $module->id }}">{{ $module->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">3. Select Video</label>
                                                    <select wire:model.live="nodeLmsLessonId" class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20" {{ empty($nodeLmsModuleId) ? 'disabled' : '' }}>
                                                        <option value="">Choose video...</option>
                                                        @if(!empty($nodeLmsModuleId))
                                                            @foreach($this->lmsLessons as $lesson)
                                                                <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        @elseif($nodeLmsMaterialType === 'module_resource')
                                            <div class="space-y-4 animate-in fade-in slide-in-from-top-2 duration-300">
                                                <div>
                                                    <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">2. Select Module</label>
                                                    <select wire:model.live="nodeLmsModuleId" class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20">
                                                        <option value="">Choose module...</option>
                                                        @foreach($lmsModules as $module)
                                                            <option value="{{ $module->id }}">{{ $module->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">3. Select PDF Resource</label>
                                                    <select wire:model.live="nodeLmsResourceId" class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20" {{ empty($nodeLmsModuleId) ? 'disabled' : '' }}>
                                                        <option value="">Choose resource...</option>
                                                        @if(!empty($nodeLmsModuleId))
                                                            @foreach($this->lmsResources as $lmsResource)
                                                                <option value="{{ $lmsResource->id }}">{{ $lmsResource->title }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        @elseif($nodeLmsMaterialType === 'library_resource')
                                            <div class="animate-in fade-in slide-in-from-top-2 duration-300">
                                                <label class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">2. Select Book / Paper</label>
                                                <select wire:model.live="nodeLibraryResourceId" class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-xs font-bold shadow-sm focus:ring-2 focus:ring-primary/20">
                                                    <option value="">Choose a resource...</option>
                                                    @foreach($libraryResources as $libraryResource)
                                                        <option value="{{ $libraryResource->id }}">{{ $libraryResource->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>

                                    @if(count($selectedAttachments) > 0)
                                        <div class="mt-4 space-y-2">
                                            <p class="text-[9px] uppercase font-bold text-stone-400 tracking-widest px-1">Current Attachments</p>
                                            @foreach($selectedAttachments as $index => $att)
                                                <div class="flex items-center justify-between p-3 bg-white border border-stone-200 rounded-xl shadow-sm group">
                                                    <div class="flex items-center gap-3">
                                                        <div class="size-6 rounded-lg bg-stone-100 flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-[14px]">
                                                                {{ match($att['type']) {
                                                                    'module' => 'school',
                                                                    'video' => 'play_circle',
                                                                    'module_resource' => 'description',
                                                                    'library_resource' => 'menu_book',
                                                                    default => 'attachment'
                                                                } }}
                                                            </span>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-[11px] font-bold text-stone-900 truncate">{{ $att['title'] }}</p>
                                                            <p class="text-[8px] uppercase font-bold text-stone-400 tracking-widest">{{ $att['type'] }}</p>
                                                        </div>
                                                    </div>
                                                    <button type="button" wire:click="removeAttachment({{ $index }})" class="size-8 rounded-lg hover:bg-red-50 text-stone-300 hover:text-red-500 transition-colors flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-sm">delete</span>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-6 border-t border-stone-100 space-y-4">
                                <div class="flex items-center justify-between p-4 bg-stone-50 rounded-2xl">
                                    <div class="flex items-center gap-3">
                                        <div class="size-8 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-sm">school</span>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-stone-700">UPSC Relevant</span>
                                    </div>
                                    <button @click="$wire.set('nodeIsUpsc', !@js($nodeIsUpsc))" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" :class="@js($nodeIsUpsc) ? 'bg-orange-600' : 'bg-stone-200'">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="@js($nodeIsUpsc) ? 'translate-x-4' : 'translate-x-0'"></span>
                                    </button>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-stone-50 rounded-2xl">
                                    <div class="flex items-center gap-3">
                                        <div class="size-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-sm">lock</span>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-stone-700">Members Only</span>
                                    </div>
                                    <button @click="$wire.set('nodeIsMembersOnly', !@js($nodeIsMembersOnly))" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" :class="@js($nodeIsMembersOnly) ? 'bg-blue-600' : 'bg-stone-200'">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="@js($nodeIsMembersOnly) ? 'translate-x-4' : 'translate-x-0'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-stone-50/50 border-t border-stone-100 flex justify-end gap-4">
                    <button wire:click="closeNodeModal" class="px-8 py-3 bg-white text-stone-600 rounded-xl text-[10px] font-bold uppercase tracking-widest border border-stone-200 hover:bg-stone-50 transition-all">Cancel</button>
                    <button wire:click="saveNode" class="px-10 py-3 bg-primary text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-all">
                        {{ $editingNodeId ? 'Update Node' : 'Create Node' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Connection Modal -->
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
                    <button wire:click="$set('showConnectionModal', false)" class="px-6 py-2.5 text-stone-600 text-[10px] font-bold uppercase tracking-widest hover:underline">Cancel</button>
                    <button wire:click="saveConnection" class="px-8 py-2.5 bg-primary text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-all">Create Connection</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteConfirmModal)
        <div class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-md transition-all">
            <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden animate-in zoom-in duration-200">
                <div class="p-8 text-center">
                    <div class="size-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-4xl">warning</span>
                    </div>
                    <h3 class="text-xl font-headline font-bold text-stone-900 mb-2">Are you absolutely sure?</h3>
                    <p class="text-xs text-stone-500 leading-relaxed">
                        This action cannot be undone. {{ $nodeToDeleteId ? 'Deleting this node will also remove all its connections.' : 'This connection will be permanently severed.' }}
                    </p>
                </div>
                <div class="p-6 bg-stone-50 flex gap-3">
                    <button wire:click="cancelDelete" class="flex-1 py-3 bg-white border border-stone-200 text-stone-600 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-stone-100 transition-all">
                        Nevermind
                    </button>
                    <button wire:click="performDelete" class="flex-1 py-3 bg-red-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-red-600/20 hover:bg-red-700 hover:-translate-y-0.5 transition-all">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
