<?php

namespace App\Livewire\Admin\KnowledgeMap;

use App\Models\Encyclopedia\Anthropologist;
use App\Models\Encyclopedia\CoreConcept;
use App\Models\Encyclopedia\MajorTheory;
use App\Models\KnowledgeMap\KnowledgeMap;
use App\Models\KnowledgeMap\KnowledgeMapConnection;
use App\Models\KnowledgeMap\KnowledgeMapNode;
use App\Models\LibraryResource;
use App\Models\Lms\LmsLesson;
use App\Models\Lms\LmsModule;
use App\Models\Lms\LmsResource;
use App\Models\TagGroup;
use App\Services\KnowledgeMap\KnowledgeMapConnectionService;
use App\Services\KnowledgeMap\KnowledgeMapNodeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class KnowledgeMapForm extends Component
{
    public KnowledgeMap $map;

    public $selectedNodeId = null;
    public $selectedConnectionId = null;

    public $showNodeModal = false;
    public $editingNodeId = null;
    public $nodeTitle = '';
    public $nodePositionX = 2500;
    public $nodePositionY = 2500;
    public $nodeShortDescription = '';
    public $nodeFullDescription = '';
    public $nodeIsUpsc = false;
    public $nodeIsMembersOnly = false;
    public $nodeType = 'concept';
    public $nodeImportance = 'secondary';
    public $nodeTags = [];
    public $nodeConceptId = null;
    public $nodeAnthropologistId = null;
    public $nodeTheoryId = null;

    public $nodeLmsMaterialType = 'video';
    public $nodeLmsModuleId = null;
    public $nodeLmsLessonId = null;
    public $nodeLmsResourceId = null;
    public $nodeLibraryResourceId = null;

    public $nodeMetadata = [];
    public $selectedAttachments = [];
    public $connections = [];

    public $showConnectionModal = false;
    public $connFromId = null;
    public $connToId = null;
    public $connLabel = null;
    public $connType = 'relates_to';
    public $connDirection = 'one_way';
    public $connLineStyle = 'solid';

    public $showDeleteConfirmModal = false;
    public $nodeToDeleteId = null;
    public $connectionToDeleteId = null;

    protected $listeners = [
        'knowledge-map-node-position-updated' => 'updateNodePosition',
        'refresh-builder' => '$refresh',
    ];

    public function mount()
    {
        $this->map = KnowledgeMap::firstOrCreate(
            ['slug' => 'main-map'],
            [
                'title' => 'Anthropology Knowledge Map',
                'status' => 'published',
                'visibility' => 'public',
                'is_featured' => true,
                'default_zoom' => 1.0,
                'canvas_settings' => [
                    'background' => 'dotted',
                    'width' => 4000,
                    'height' => 3000,
                ],
            ]
        );

        $this->syncConnectionsState();
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

    public function getLmsLessonsProperty()
    {
        return $this->nodeLmsModuleId
            ? LmsLesson::where('lms_module_id', $this->nodeLmsModuleId)->orderBy('sort_order')->get()
            : collect();
    }

    public function getLmsResourcesProperty()
    {
        return $this->nodeLmsModuleId
            ? LmsResource::where('lms_module_id', $this->nodeLmsModuleId)->orderBy('sort_order')->get()
            : collect();
    }

    public function getLibraryResourcesProperty()
    {
        return LibraryResource::orderBy('title')->get();
    }

    public function openAddNode()
    {
        $this->resetNodeForm();
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
        $this->nodePositionX = (float) $x;
        $this->nodePositionY = (float) $y;
        $this->showNodeModal = true;
    }

    public function editNode($id)
    {
        $node = $this->map->nodes()->with(['tags', 'attachments.attachable'])->whereKey($id)->firstOrFail();

        $this->editingNodeId = $node->id;
        $this->nodeTitle = $node->title;
        $this->nodeShortDescription = $node->short_description;
        $this->nodeFullDescription = $node->full_description;
        $this->nodeIsUpsc = (bool) $node->is_upsc_relevant;
        $this->nodeIsMembersOnly = (bool) $node->is_members_only;
        $this->nodeType = $node->node_type ?? 'concept';
        $this->nodeImportance = $node->importance ?? 'secondary';
        $this->nodeTags = $node->tags->pluck('id')->toArray();
        $this->nodeConceptId = $node->encyclopedia_concept_id;
        $this->nodeAnthropologistId = $node->anthropologist_id;
        $this->nodeTheoryId = $node->theory_id;
        $this->nodeLmsModuleId = $node->lms_module_id;
        $this->nodeLmsLessonId = $node->lms_lesson_id;
        $this->nodeLmsResourceId = $node->lms_material_id;

        $this->selectedAttachments = $node->attachments->map(function ($attachment) {
            $type = match ($attachment->attachable_type) {
                LmsModule::class => 'module',
                LmsLesson::class => 'video',
                LmsResource::class => 'module_resource',
                LibraryResource::class => 'library_resource',
                default => 'other',
            };

            return [
                'type' => $type,
                'id' => $attachment->attachable_id,
                'title' => $attachment->attachable?->title ?? 'Deleted Resource',
            ];
        })->toArray();

        $this->showNodeModal = true;
    }

    public function saveNode(KnowledgeMapNodeService $service)
    {
        $this->validate([
            'nodeTitle' => 'required|string|max:255',
        ]);

        $duplicateQuery = KnowledgeMapNode::where('knowledge_map_id', $this->map->id)
            ->where('title', $this->nodeTitle);

        if ($this->editingNodeId) {
            $duplicateQuery->where('id', '!=', $this->editingNodeId);
        }

        if ($duplicateQuery->exists()) {
            $this->addError('nodeTitle', 'A node with this title already exists on the canvas.');
            return;
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
            'lms_module_id' => in_array($this->nodeLmsMaterialType, ['module', 'video', 'module_resource'], true) ? $this->nodeLmsModuleId : null,
            'lms_lesson_id' => $this->nodeLmsMaterialType === 'video' ? $this->nodeLmsLessonId : null,
            'lms_material_id' => $this->nodeLmsMaterialType === 'module_resource' ? $this->nodeLmsResourceId : null,
            'metadata' => [
                'material_type' => $this->nodeLmsMaterialType,
            ],
            'attachments' => array_values(array_filter(array_map(function ($attachment) {
                $type = match ($attachment['type'] ?? null) {
                    'module' => LmsModule::class,
                    'video' => LmsLesson::class,
                    'module_resource' => LmsResource::class,
                    'library_resource' => LibraryResource::class,
                    default => null,
                };

                if (!$type || empty($attachment['id'])) {
                    return null;
                }

                return [
                    'id' => $attachment['id'],
                    'type' => $type,
                ];
            }, $this->selectedAttachments))),
        ];

        if ($this->editingNodeId) {
            $node = $this->map->nodes()->whereKey($this->editingNodeId)->firstOrFail();
            $service->updateNode($node, $data);
            
            $this->showNodeModal = false;
            $this->resetNodeForm();
            $this->refreshCanvas();
        } else {
            $data['position_x'] = $this->nodePositionX;
            $data['position_y'] = $this->nodePositionY;
            $data['metadata'] = array_merge($data['metadata'] ?? [], ['is_on_canvas' => true]);
            $service->createNode($data);

            $this->showNodeModal = false;
            $this->resetNodeForm();
            return redirect(request()->header('Referer'));
        }
    }

    public function updateNodePosition($id, $x, $y, KnowledgeMapNodeService $service)
    {
        $node = $this->map->nodes()->whereKey($id)->first();

        if (!$node) {
            return;
        }

        $metadata = $node->metadata ?? [];
        $metadata['is_on_canvas'] = true;
        $node->metadata = $metadata;
        $node->save();

        $service->updatePosition($node, (float) $x, (float) $y);
    }

    public function removeNodeFromCanvas($id)
    {
        $node = $this->map->nodes()->whereKey($id)->first();
        if ($node) {
            $metadata = $node->metadata ?? [];
            $metadata['is_on_canvas'] = false;
            $node->metadata = $metadata;
            $node->save();

            \App\Models\KnowledgeMap\KnowledgeMapConnection::where('from_node_id', $id)
                ->orWhere('to_node_id', $id)
                ->delete();

            $this->selectedNodeId = null;
            return redirect(request()->header('Referer'));
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

    public function performDelete(KnowledgeMapNodeService $nodeService, KnowledgeMapConnectionService $connectionService)
    {
        if ($this->nodeToDeleteId) {
            $node = $this->map->nodes()->whereKey($this->nodeToDeleteId)->first();

            if ($node) {
                $nodeService->deleteNode($node);
            }

            $this->selectedNodeId = null;
        }

        if ($this->connectionToDeleteId) {
            $connection = $this->map->connections()->whereKey($this->connectionToDeleteId)->first();

            if ($connection) {
                $connectionService->deleteConnection($connection);
            }

            $this->selectedConnectionId = null;
        }

        $this->showDeleteConfirmModal = false;
        $this->nodeToDeleteId = null;
        $this->connectionToDeleteId = null;

        $this->refreshCanvas();
    }

    public function deleteNode($id, KnowledgeMapNodeService $service)
    {
        $node = $this->map->nodes()->whereKey($id)->first();

        if (!$node) {
            return;
        }

        $service->deleteNode($node);
        $this->selectedNodeId = null;
        $this->refreshCanvas();
    }

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
            $this->refreshCanvas();
        } catch (\Throwable $e) {
            $this->addError('connection', $e->getMessage());
        }
    }

    public function createVisualConnection($fromNodeId, $toNodeId)
    {
        try {
            $fromNode = $this->map->nodes()->whereKey($fromNodeId)->firstOrFail();
            $toNode = $this->map->nodes()->whereKey($toNodeId)->firstOrFail();
        } catch (\Throwable $e) {
            Log::error('Knowledge map connection failed because one or both nodes were not found.', [
                'map_id' => $this->map->id,
                'from_node_id' => $fromNodeId,
                'to_node_id' => $toNodeId,
            ]);

            return null;
        }

        if ((int) $fromNode->id === (int) $toNode->id) {
            throw ValidationException::withMessages([
                'connection' => 'A node cannot connect to itself.',
            ]);
        }

        $existing = $this->map->connections()
            ->where('from_node_id', $fromNode->id)
            ->where('to_node_id', $toNode->id)
            ->where('connection_type', 'relates_to')
            ->first();

        if ($existing) {
            $this->selectedConnectionId = $existing->id;
            $this->selectedNodeId = null;

            return $this->connectionPayload($existing);
        }

        $connection = KnowledgeMapConnection::create([
            'knowledge_map_id' => $this->map->id,
            'from_node_id' => $fromNode->id,
            'to_node_id' => $toNode->id,
            'label' => null,
            'connection_type' => 'relates_to',
            'direction' => 'one_way',
            'line_style' => 'solid',
            'color' => null,
        ]);

        $this->selectedConnectionId = $connection->id;
        $this->selectedNodeId = null;
        $this->syncConnectionsState();

        return $this->connectionPayload($connection);
    }

    public function addAttachment()
    {
        $item = null;

        if ($this->nodeLmsMaterialType === 'module' && $this->nodeLmsModuleId) {
            $item = LmsModule::find($this->nodeLmsModuleId);
        }

        if ($this->nodeLmsMaterialType === 'video' && $this->nodeLmsLessonId) {
            $item = LmsLesson::find($this->nodeLmsLessonId);
        }

        if ($this->nodeLmsMaterialType === 'module_resource' && $this->nodeLmsResourceId) {
            $item = LmsResource::find($this->nodeLmsResourceId);
        }

        if ($this->nodeLmsMaterialType === 'library_resource' && $this->nodeLibraryResourceId) {
            $item = LibraryResource::find($this->nodeLibraryResourceId);
        }

        if (!$item) {
            return;
        }

        foreach ($this->selectedAttachments as $attachment) {
            if (($attachment['type'] ?? null) === $this->nodeLmsMaterialType && (int) ($attachment['id'] ?? 0) === (int) $item->id) {
                return;
            }
        }

        $this->selectedAttachments[] = [
            'type' => $this->nodeLmsMaterialType,
            'id' => $item->id,
            'title' => $item->title,
        ];

        if (!in_array($this->nodeLmsMaterialType, ['module', 'video', 'module_resource'], true)) {
            $this->nodeLmsModuleId = null;
        }

        $this->nodeLmsLessonId = null;
        $this->nodeLmsResourceId = null;
        $this->nodeLibraryResourceId = null;
    }

    public function removeAttachment($index)
    {
        if (!isset($this->selectedAttachments[$index])) {
            return;
        }

        unset($this->selectedAttachments[$index]);
        $this->selectedAttachments = array_values($this->selectedAttachments);
    }

    public function deleteConnection($id, KnowledgeMapConnectionService $service)
    {
        $connection = $this->map->connections()->whereKey($id)->first();

        if (!$connection) {
            return;
        }

        $service->deleteConnection($connection);
        $this->selectedConnectionId = null;
        $this->refreshCanvas();
    }

    public function selectNode($id)
    {
        $node = $this->map->nodes()->whereKey($id)->first();

        if (!$node) {
            $this->deselect();
            return;
        }

        $this->selectedNodeId = (int) $node->id;
        $this->selectedConnectionId = null;
    }

    public function selectConnection($id)
    {
        $connection = $this->map->connections()->whereKey($id)->first();

        if (!$connection) {
            $this->deselect();
            return;
        }

        $this->selectedConnectionId = (int) $connection->id;
        $this->selectedNodeId = null;
    }

    public function deselect()
    {
        $this->selectedNodeId = null;
        $this->selectedConnectionId = null;
    }

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
        $this->nodeType = 'concept';
        $this->nodeImportance = 'secondary';
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

    protected function syncConnectionsState()
    {
        $this->connections = $this->map->connections()
            ->select('id', 'from_node_id', 'to_node_id', 'label', 'connection_type', 'direction', 'line_style', 'color')
            ->get()
            ->toArray();
    }

    protected function refreshCanvas()
    {
        $this->map->refresh();
        $this->syncConnectionsState();

        $this->dispatch('km-refresh', [
            'nodes' => $this->map->nodes()->with('tags')->get(),
            'connections' => $this->map->connections()
                ->select('id', 'from_node_id', 'to_node_id', 'label', 'connection_type', 'direction', 'line_style', 'color')
                ->get(),
        ]);
    }

    protected function connectionPayload(KnowledgeMapConnection $connection)
    {
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

    public function render()
    {
        $allNodes = KnowledgeMapNode::where('knowledge_map_id', $this->map->id)->with('tags')->get();
        
        $canvasNodes = $allNodes->filter(function($node) {
            return ($node->metadata['is_on_canvas'] ?? true) === true;
        })->values();

        return view('livewire.admin.knowledge-map.builder', [
            'nodes' => $allNodes,
            'canvasNodes' => $canvasNodes,
            'connections' => KnowledgeMapConnection::where('knowledge_map_id', $this->map->id)->get(),
            'tagGroups' => TagGroup::with('tags')->get(),
            'concepts' => CoreConcept::orderBy('title')->get(),
            'anthropologists' => Anthropologist::orderBy('full_name')->get(),
            'theories' => MajorTheory::orderBy('title')->get(),
            'lmsModules' => LmsModule::orderBy('title')->get(),
            'libraryResources' => LibraryResource::orderBy('title')->get(),
            'selectedNode' => $this->selectedNodeId
                ? KnowledgeMapNode::with(['tags', 'encyclopediaConcept', 'anthropologist', 'theory', 'lmsModule', 'attachments.attachable'])->find($this->selectedNodeId)
                : null,
            'selectedConnection' => $this->selectedConnectionId
                ? KnowledgeMapConnection::with(['fromNode', 'toNode'])->find($this->selectedConnectionId)
                : null,
        ])->layout('layouts.admin', ['title' => 'Canvas Builder - ' . $this->map->title]);
    }
}