<?php

namespace App\Livewire\Admin\KnowledgeMap;

use Livewire\Component;
use App\Models\KnowledgeMap\KnowledgeMap;
use App\Models\KnowledgeMap\KnowledgeMapNode;
use App\Models\KnowledgeMap\KnowledgeMapConnection;
use App\Services\KnowledgeMap\KnowledgeMapNodeService;
use App\Services\KnowledgeMap\KnowledgeMapConnectionService;
use App\Models\Encyclopedia\CoreConcept;
use App\Models\Encyclopedia\Anthropologist;
use App\Models\Encyclopedia\MajorTheory;
use App\Models\Lms\LmsModule;
use App\Models\Lms\LmsLesson;
use App\Models\Lms\LmsResource;
use App\Models\LibraryResource;
use App\Models\Tag;
use App\Models\TagGroup;

class KnowledgeMapBuilder extends Component
{
    public KnowledgeMap $map;
    public $selectedNodeId = null;
    public $selectedConnectionId = null;
    
    // Node Form State
    public $showNodeModal = false;
    public $editingNodeId = null;
    public $nodeTitle;
    public $nodePositionX = 2500;
    public $nodePositionY = 2500;
    public $nodeShortDescription;
    public $nodeFullDescription;
    public $nodeIsUpsc = false;
    public $nodeIsMembersOnly = false;
    public $nodeType = 'concept';
    public $nodeImportance = 'secondary';
    public $nodeTags = [];
    public $nodeConceptId;
    public $nodeAnthropologistId;
    public $nodeTheoryId;
    
    // LMS Material Selection
    public $nodeLmsMaterialType = 'video'; // 'module', 'video', 'module_resource', 'library_resource'
    public $nodeLmsModuleId;
    public $nodeLmsLessonId;
    public $nodeLmsResourceId; // For module-specific resources
    public $nodeLibraryResourceId; // For general library
    
    public $nodeMetadata = [];
    public $selectedAttachments = [];
    public $connections = [];

    // Connection Form State
    public $showConnectionModal = false;
    public $connFromId;
    public $connToId;
    public $connLabel;
    public $connType = 'relates_to';
    public $connDirection = 'one_way';
    public $connLineStyle = 'solid';

    // Delete Confirmation State
    public $showDeleteConfirmModal = false;
    public $nodeToDeleteId = null;
    public $connectionToDeleteId = null;

    protected $listeners = [
        'knowledge-map-node-position-updated' => 'updateNodePosition',
        'refresh-builder' => '$refresh'
    ];

    public function mount()
    {
        $this->map = KnowledgeMap::firstOrCreate([
            'slug' => 'main-map'
        ], [
            'title' => 'Anthropology Knowledge Map',
            'status' => 'published',
            'visibility' => 'public',
            'is_featured' => true,
            'default_zoom' => 1.0,
            'canvas_settings' => [
                'background' => 'dotted',
                'width' => 4000,
                'height' => 3000
            ]
        ]);
    }

    public function updatedNodeLmsMaterialType()
    {
        $this->nodeLmsModuleId = null;
        $this->nodeLmsLessonId = null;
        $this->nodeLmsResourceId = null;
        $this->nodeLibraryResourceId = null;
    }

    public function updatedNodeLmsModuleId()
    {
        $this->nodeLmsLessonId = null;
        $this->nodeLmsResourceId = null;
    }

    // Getters for dynamic dropdowns
    public function getLmsLessonsProperty()
    {
        return $this->nodeLmsModuleId ? LmsLesson::where('lms_module_id', $this->nodeLmsModuleId)->orderBy('sort_order')->get() : [];
    }

    public function getLmsResourcesProperty()
    {
        return $this->nodeLmsModuleId ? LmsResource::where('lms_module_id', $this->nodeLmsModuleId)->orderBy('sort_order')->get() : [];
    }

    public function getLibraryResourcesProperty()
    {
        return LibraryResource::orderBy('title')->get();
    }

    // Node Operations
    public function openAddNode()
    {
        $this->resetNodeForm();
        // default to center of the view? Actually JS should pass coords if possible.
        // For standard button click, we enter placement mode in JS instead of calling this directly.
        $this->showNodeModal = true;
    }

    public function closeNodeModal()
    {
        $this->showNodeModal = false;
        $this->resetNodeForm();
    }

    public function prepAddNodeAt($x, $y)
    {
        $this->resetNodeForm();
        $this->nodePositionX = $x;
        $this->nodePositionY = $y;
        $this->showNodeModal = true;
    }

    public function editNode($id)
    {
        $node = KnowledgeMapNode::findOrFail($id);
        $this->editingNodeId = $node->id;
        $this->nodeTitle = $node->title;
        $this->nodeShortDescription = $node->short_description;
        $this->nodeFullDescription = $node->full_description;
        $this->nodeIsUpsc = $node->is_upsc_relevant;
        $this->nodeIsMembersOnly = $node->is_members_only;
        $this->nodeType = $node->node_type ?? 'concept';
        $this->nodeImportance = $node->importance ?? 'secondary';
        $this->nodeTags = $node->tags->pluck('id')->toArray();
        $this->nodeConceptId = $node->encyclopedia_concept_id;
        $this->nodeAnthropologistId = $node->anthropologist_id;
        $this->nodeTheoryId = $node->theory_id;
        
        $this->nodeLmsModuleId = $node->lms_module_id;
        $this->nodeLmsLessonId = $node->lms_lesson_id;
        $this->nodeLmsResourceId = $node->lms_material_id;
        
        $this->selectedAttachments = $node->attachments->map(function($att) {
            $type = match($att->attachable_type) {
                LmsModule::class => 'module',
                LmsLesson::class => 'video',
                LmsResource::class => 'module_resource',
                LibraryResource::class => 'library_resource',
                default => 'other'
            };
            
            return [
                'type' => $type,
                'id' => $att->attachable_id,
                'title' => $att->attachable?->title ?? 'Deleted Resource'
            ];
        })->toArray();

        $this->showNodeModal = true;
    }

    public function saveNode(KnowledgeMapNodeService $service)
    {
        $this->validate([
            'nodeTitle' => 'required|string|max:255',
        ]);

        if (!$this->editingNodeId) {
            $exists = KnowledgeMapNode::where('knowledge_map_id', $this->map->id)
                ->where('title', $this->nodeTitle)
                ->exists();
            if ($exists) {
                $this->addError('nodeTitle', 'A node with this title already exists on the canvas.');
                return;
            }
        } else {
            $exists = KnowledgeMapNode::where('knowledge_map_id', $this->map->id)
                ->where('title', $this->nodeTitle)
                ->where('id', '!=', $this->editingNodeId)
                ->exists();
            if ($exists) {
                $this->addError('nodeTitle', 'A node with this title already exists on the canvas.');
                return;
            }
        }

        $data = [
            'knowledge_map_id' => $this->map->id,
            'title' => $this->nodeTitle,
            'short_description' => $this->nodeShortDescription,
            'full_description' => $this->nodeFullDescription,
            'is_upsc_relevant' => $this->nodeIsUpsc,
            'is_members_only' => $this->nodeIsMembersOnly,
            'node_type' => $this->nodeType ?? 'concept',
            'importance' => $this->nodeImportance ?? 'secondary',
            'tags' => $this->nodeTags,
            'encyclopedia_concept_id' => $this->nodeConceptId,
            'anthropologist_id' => $this->nodeAnthropologistId,
            'theory_id' => $this->nodeTheoryId,
            'lms_module_id' => in_array($this->nodeLmsMaterialType, ['module', 'video', 'module_resource']) ? $this->nodeLmsModuleId : null,
            'lms_lesson_id' => ($this->nodeLmsMaterialType == 'video') ? $this->nodeLmsLessonId : null,
            'lms_material_id' => ($this->nodeLmsMaterialType == 'module_resource') ? $this->nodeLmsResourceId : null,
            'metadata' => [
                'material_type' => $this->nodeLmsMaterialType,
            ],
            'attachments' => array_map(function($att) {
                return [
                    'id' => $att['id'],
                    'type' => match($att['type']) {
                        'module' => LmsModule::class,
                        'video' => LmsLesson::class,
                        'module_resource' => LmsResource::class,
                        'library_resource' => LibraryResource::class,
                        default => null
                    }
                ];
            }, $this->selectedAttachments)
        ];

        if ($this->editingNodeId) {
            $node = KnowledgeMapNode::find($this->editingNodeId);
            $service->updateNode($node, $data);
        } else {
            $data['position_x'] = $this->nodePositionX;
            $data['position_y'] = $this->nodePositionY;
            $service->createNode($data);
        }

        $this->showNodeModal = false;
        $this->dispatch('km-refresh', [
            'nodes' => $this->map->nodes()->with('tags')->get(),
            'connections' => $this->map->connections()->get()
        ]);
    }

    public function updateNodePosition($id, $x, $y, KnowledgeMapNodeService $service)
    {
        $node = KnowledgeMapNode::find($id);
        if ($node) {
            $service->updatePosition($node, (float)$x, (float)$y);
        }
    }

    public function confirmDeleteNode($id)
    {
        $this->nodeToDeleteId = $id;
        $this->connectionToDeleteId = null;
        $this->showDeleteConfirmModal = true;
    }

    public function confirmDeleteConnection($id)
    {
        $this->connectionToDeleteId = $id;
        $this->nodeToDeleteId = null;
        $this->showDeleteConfirmModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteConfirmModal = false;
        $this->nodeToDeleteId = null;
        $this->connectionToDeleteId = null;
    }

    public function performDelete(KnowledgeMapNodeService $nodeService, KnowledgeMapConnectionService $connService)
    {
        if ($this->nodeToDeleteId) {
            $node = KnowledgeMapNode::find($this->nodeToDeleteId);
            if ($node) {
                $nodeService->deleteNode($node);
                $this->selectedNodeId = null;
            }
        } elseif ($this->connectionToDeleteId) {
            $conn = KnowledgeMapConnection::find($this->connectionToDeleteId);
            if ($conn) {
                $connService->deleteConnection($conn);
                $this->selectedConnectionId = null;
            }
        }

        $this->showDeleteConfirmModal = false;
        $this->nodeToDeleteId = null;
        $this->connectionToDeleteId = null;

        $this->dispatch('km-refresh', [
            'nodes' => $this->map->nodes()->with('tags')->get(),
            'connections' => $this->map->connections()->get()
        ]);
    }

    public function deleteNode($id, KnowledgeMapNodeService $service)
    {
        $node = KnowledgeMapNode::find($id);
        if ($node) {
            $service->deleteNode($node);
            $this->selectedNodeId = null;
            $this->dispatch('km-refresh', [
                'nodes' => $this->map->nodes()->with('tags')->get(),
                'connections' => $this->map->connections()->get()
            ]);
        }
    }

    // Connection Operations
    public function openAddConnection()
    {
        $this->connFromId = $this->selectedNodeId;
        $this->showConnectionModal = true;
    }

    public function saveConnection(KnowledgeMapConnectionService $service)
    {
        try {
            $data = [
                'knowledge_map_id' => $this->map->id,
                'from_node_id' => $this->connFromId,
                'to_node_id' => $this->connToId,
                'label' => $this->connLabel,
                'connection_type' => $this->connType,
                'direction' => $this->connDirection,
                'line_style' => $this->connLineStyle,
            ];

            $service->createConnection($data);
            $this->showConnectionModal = false;
            $this->dispatch('km-refresh', [
                'nodes' => $this->map->nodes()->with('tags')->get(),
                'connections' => $this->map->connections()->get()
            ]);
        } catch (\Exception $e) {
            $this->addError('connection', $e->getMessage());
        }
    }

    public function createVisualConnection($fromNodeId, $toNodeId)
    {
        try {
            $fromNode = $this->map->nodes()->whereKey($fromNodeId)->firstOrFail();
            $toNode = $this->map->nodes()->whereKey($toNodeId)->firstOrFail();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Connection failed: Nodes not found in current map context.', ['from' => $fromNodeId, 'to' => $toNodeId]);
            return null;
        }

        if ((int) $fromNode->id === (int) $toNode->id) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'connection' => 'A node cannot connect to itself.',
            ]);
        }

        $existing = $this->map->connections()
            ->where('from_node_id', $fromNodeId)
            ->where('to_node_id', $toNodeId)
            ->where('connection_type', 'relates_to')
            ->first();

        if ($existing) {
            return [
                'id' => $existing->id,
                'from_node_id' => $existing->from_node_id,
                'to_node_id' => $existing->to_node_id,
                'label' => $existing->label,
                'connection_type' => $existing->connection_type,
                'direction' => $existing->direction,
                'line_style' => $existing->line_style,
                'color' => $existing->color,
            ];
        }

        $connection = $this->map->connections()->create([
            'from_node_id' => $fromNodeId,
            'to_node_id' => $toNodeId,
            'label' => null,
            'connection_type' => 'relates_to',
            'direction' => 'one_way',
            'line_style' => 'solid',
            'color' => null,
        ]);

        $this->connections = $this->map->connections()
            ->select(
                'id',
                'from_node_id',
                'to_node_id',
                'label',
                'connection_type',
                'direction',
                'line_style',
                'color'
            )
            ->get()
            ->toArray();

        // Broadcast km-refresh optionally for other components, but frontend handles itself now
        // $this->dispatch('km-refresh', [ ... ]);

        return [
            'id' => $connection->id,
            'from_node_id' => $connection->from_node_id,
            'to_node_id' => $connection->to_node_id,
            'label' => $connection->label,
            'connection_type' => $connection->connection_type,
            'direction' => $connection->direction,
            'line_style' => $connection->line_style,
            'color' => $connection->color,
        ];
    }

    public function addAttachment()
    {
        $id = null;
        $title = '';
        
        if ($this->nodeLmsMaterialType === 'module' && $this->nodeLmsModuleId) {
            $item = LmsModule::find($this->nodeLmsModuleId);
            $id = $item->id;
            $title = $item->title;
        } elseif ($this->nodeLmsMaterialType === 'video' && $this->nodeLmsLessonId) {
            $item = LmsLesson::find($this->nodeLmsLessonId);
            $id = $item->id;
            $title = $item->title;
        } elseif ($this->nodeLmsMaterialType === 'module_resource' && $this->nodeLmsResourceId) {
            $item = LmsResource::find($this->nodeLmsResourceId);
            $id = $item->id;
            $title = $item->title;
        } elseif ($this->nodeLmsMaterialType === 'library_resource' && $this->nodeLibraryResourceId) {
            $item = LibraryResource::find($this->nodeLibraryResourceId);
            $id = $item->id;
            $title = $item->title;
        }

        if ($id) {
            // Check for duplicates
            foreach ($this->selectedAttachments as $att) {
                if ($att['type'] === $this->nodeLmsMaterialType && $att['id'] == $id) {
                    return;
                }
            }

            $this->selectedAttachments[] = [
                'type' => $this->nodeLmsMaterialType,
                'id' => $id,
                'title' => $title
            ];
            
            // Reset selection but keep module if it's needed for context
            if (!in_array($this->nodeLmsMaterialType, ['module', 'video', 'module_resource'])) {
                $this->nodeLmsModuleId = null;
            }
            $this->nodeLmsLessonId = null;
            $this->nodeLmsResourceId = null;
            $this->nodeLibraryResourceId = null;
        }
    }

    public function removeAttachment($index)
    {
        if (isset($this->selectedAttachments[$index])) {
            unset($this->selectedAttachments[$index]);
            $this->selectedAttachments = array_values($this->selectedAttachments);
        }
    }

    public function deleteConnection($id, KnowledgeMapConnectionService $service)
    {
        $conn = KnowledgeMapConnection::find($id);
        if ($conn) {
            $service->deleteConnection($conn);
            $this->selectedConnectionId = null;
            $this->dispatch('km-refresh', [
                'nodes' => $this->map->nodes()->with('tags')->get(),
                'connections' => $this->map->connections()->get()
            ]);
        }
    }

    // Selection
    public function selectNode($id) { $this->selectedNodeId = $id; $this->selectedConnectionId = null; }
    public function selectConnection($id) { $this->selectedConnectionId = $id; $this->selectedNodeId = null; }
    public function deselect() { $this->selectedNodeId = null; $this->selectedConnectionId = null; }

    protected function resetNodeForm()
    {
        $this->editingNodeId = null;
        $this->nodeTitle = '';
        $this->nodePositionX = 2500;
        $this->nodePositionY = 2500;
        $this->nodeShortDescription = '';
        $this->nodeFullDescription = '';
        $this->nodeIsUpsc = false;
        $this->nodeIsMembersOnly = false;
        $this->nodeTags = [];
        $this->nodeConceptId = null;
        $this->nodeAnthropologistId = null;
        $this->nodeTheoryId = null;
        $this->nodeLmsModuleId = null;
        $this->nodeLmsLessonId = null;
        $this->nodeLmsResourceId = null;
        $this->nodeLibraryResourceId = null;
        $this->nodeLmsMaterialType = 'video';
        $this->selectedAttachments = [];
    }

    public function render()
    {
        return view('livewire.admin.knowledge-map.builder', [
            'nodes' => $this->map->nodes()->with('tags')->get(),
            'connections' => $this->map->connections()->get(),
            'tagGroups' => TagGroup::with('tags')->get(),
            'concepts' => CoreConcept::orderBy('title')->get(),
            'anthropologists' => Anthropologist::orderBy('full_name')->get(),
            'theories' => MajorTheory::orderBy('title')->get(),
            'lmsModules' => LmsModule::orderBy('title')->get(),
            'libraryResources' => LibraryResource::orderBy('title')->get(),
            'selectedNode' => $this->selectedNodeId ? KnowledgeMapNode::with(['tags', 'encyclopediaConcept', 'anthropologist', 'theory', 'lmsModule', 'attachments.attachable'])->find($this->selectedNodeId) : null,
            'selectedConnection' => $this->selectedConnectionId ? KnowledgeMapConnection::with(['fromNode', 'toNode'])->find($this->selectedConnectionId) : null,
        ])->layout('layouts.admin', ['title' => 'Canvas Builder - ' . $this->map->title]);
    }
}
