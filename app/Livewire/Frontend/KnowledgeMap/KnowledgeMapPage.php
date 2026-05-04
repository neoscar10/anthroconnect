<?php

namespace App\Livewire\Frontend\KnowledgeMap;

use Livewire\Component;
use App\Models\KnowledgeMap\KnowledgeMap;
use App\Models\KnowledgeMap\KnowledgeMapNode;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class KnowledgeMapPage extends Component
{
    public KnowledgeMap $map;
    public string $search = '';
    public bool $upscOnly = false;
    public array $selectedTags = [];
    public array $selectedSingleTags = [];
    public ?int $selectedNodeId = null;
    public string $backUrl = '';

    public function mount()
    {
        $this->backUrl = request('from') ?: url()->previous();

        if (!$this->backUrl || $this->backUrl === request()->fullUrl()) {
            $this->backUrl = route('home');
        }

        $query = KnowledgeMap::query()->with([
            'nodes.tags.group',
            'nodes.encyclopediaConcept',
            'nodes.anthropologist',
            'nodes.theory',
            'nodes.lmsModule',
            'nodes.lmsLesson.module',
            'nodes.lmsLesson.class.module',
            'nodes.lmsMaterial.module',
            'nodes.lmsMaterial.class.module',
            'nodes.attachments.attachable.module',
            'nodes.attachments.attachable.class.module',
            'connections',
            'learningPaths.nodes',
        ]);

        $this->map = (clone $query)->where('slug', 'main-map')->where('status', 'published')->first() 
            ?? $query->where('status', 'published')->firstOrFail();

        $firstNode = $this->map->nodes->sortBy('sort_order')->first();
        $this->selectedNodeId = $firstNode?->id;
    }

    public function updatedSearch()
    {
        $this->syncSelectedNodeAfterFiltering();
        $this->dispatchUpdatedCanvasData();
    }

    public function updatedUpscOnly()
    {
        $this->syncSelectedNodeAfterFiltering();
        $this->dispatchUpdatedCanvasData();
    }

    public function toggleTag($tagId)
    {
        if (in_array($tagId, $this->selectedTags)) {
            $this->selectedTags = array_values(array_diff($this->selectedTags, [$tagId]));
        } else {
            $this->selectedTags[] = $tagId;
        }

        $this->syncSelectedNodeAfterFiltering();
        $this->dispatchUpdatedCanvasData();
    }

    public function updatedSelectedSingleTags()
    {
        $this->syncSelectedNodeAfterFiltering();
        $this->dispatchUpdatedCanvasData();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->upscOnly = false;
        $this->selectedTags = [];
        $this->selectedSingleTags = [];

        $this->syncSelectedNodeAfterFiltering();
        $this->dispatchUpdatedCanvasData();
    }

    public function selectNode($nodeId)
    {
        $visibleIds = collect($this->visibleNodes)->pluck('id')->map(fn ($id) => (int) $id)->toArray();

        if (in_array((int) $nodeId, $visibleIds, true)) {
            $this->selectedNodeId = (int) $nodeId;
        }
    }

    public function getVisibleNodesProperty()
    {
        return $this->map->nodes
            ->filter(function ($node) {
                if ($this->upscOnly && !$node->is_upsc_relevant) {
                    return false;
                }

                if ($this->search) {
                    $needle = strtolower($this->search);

                    $haystack = strtolower(collect([
                        $node->title,
                        $node->short_description,
                        $node->full_description,
                        $node->manual_concept_title,
                        $node->manual_concept_summary,
                    ])->filter()->implode(' '));

                    if (!str_contains($haystack, $needle)) {
                        return false;
                    }
                }

                $selectedTagIds = collect($this->selectedTags)
                    ->merge(array_filter($this->selectedSingleTags))
                    ->map(fn ($id) => (int) $id)
                    ->values();

                if ($selectedTagIds->isNotEmpty()) {
                    $nodeTagIds = $node->tags->pluck('id')->map(fn ($id) => (int) $id);

                    foreach ($selectedTagIds as $tagId) {
                        if (!$nodeTagIds->contains($tagId)) {
                            return false;
                        }
                    }
                }

                return true;
            })
            ->values()
            ->map(function ($node) {
                return [
                    'id' => $node->id,
                    'title' => $node->title,
                    'position_x' => (float) $node->position_x,
                    'position_y' => (float) $node->position_y,
                    'importance' => $node->importance,
                    'node_type' => $node->node_type,
                    'is_upsc_relevant' => (bool) $node->is_upsc_relevant,
                    'is_members_only' => (bool) $node->is_members_only,
                ];
            })
            ->toArray();
    }

    public function getVisibleConnectionsProperty()
    {
        $visibleIds = collect($this->visibleNodes)->pluck('id')->map(fn ($id) => (int) $id);

        return $this->map->connections
            ->filter(function ($connection) use ($visibleIds) {
                return $visibleIds->contains((int) $connection->from_node_id)
                    && $visibleIds->contains((int) $connection->to_node_id);
            })
            ->values()
            ->map(function ($connection) {
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
            })
            ->toArray();
    }

    public function getSelectedNodeProperty()
    {
        return $this->map->nodes->firstWhere('id', $this->selectedNodeId);
    }

    public function getTagGroupsProperty()
    {
        return $this->map->nodes
            ->flatMap(fn ($node) => $node->tags)
            ->filter()
            ->groupBy(fn ($tag) => optional($tag->group)->id ?: 0)
            ->map(function ($tags, $groupId) {
                $group = optional($tags->first()->group);

                return [
                    'id' => $groupId,
                    'name' => $group->name ?: 'Tags',
                    'selection_type' => $group->selection_type ?? $group->select_type ?? 'multi',
                    'tags' => $tags->unique('id')->values()->map(fn ($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ])->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }

    public function getLearningPathsProperty()
    {
        return $this->map->learningPaths()
            ->withCount('nodes')
            ->with('nodes')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get metadata for a specific attachment.
     */
    public function getAttachmentMeta($attachment)
    {
        if (!$attachment || !$attachment->attachable) {
            return ['icon' => 'attachment', 'label' => 'Material', 'url' => '#'];
        }

        switch ($attachment->attachable_type) {
            case \App\Models\Lms\LmsModule::class:
                return [
                    'icon' => 'school',
                    'label' => 'Course Module',
                    'url' => route('modules.show', $attachment->attachable->slug)
                ];
            case \App\Models\Lms\LmsLesson::class:
                $lesson = $attachment->attachable;
                $module = $lesson->module ?? optional($lesson->class)->module;
                $moduleSlug = $module?->slug ?: 'unknown';
                return [
                    'icon' => 'play_circle',
                    'label' => 'Video Lesson',
                    'url' => route('lessons.show', ['moduleSlug' => $moduleSlug, 'lessonSlug' => $lesson->slug])
                ];
            case \App\Models\Lms\LmsResource::class:
                $resource = $attachment->attachable;
                $module = $resource->module ?? optional($resource->class)->module;
                $moduleSlug = $module?->slug ?: 'unknown';
                return [
                    'icon' => 'description',
                    'label' => 'PDF Resource',
                    'url' => route('modules.show', $moduleSlug)
                ];
            case \App\Models\LibraryResource::class:
                return [
                    'icon' => 'menu_book',
                    'label' => 'Library Book',
                    'url' => route('library.show', $attachment->attachable->slug)
                ];
            default:
                return ['icon' => 'attachment', 'label' => 'Material', 'url' => '#'];
        }
    }

    /**
     * Get primary materials for a node.
     */
    public function getPrimaryMaterials($node)
    {
        $materials = [];
        
        if ($node->lmsModule) {
            $materials[] = [
                'icon' => 'school',
                'label' => 'Course Module',
                'title' => $node->lmsModule->title,
                'url' => route('modules.show', $node->lmsModule->slug)
            ];
        }

        if ($node->lmsLesson) {
            $lesson = $node->lmsLesson;
            $module = $lesson->module ?? optional($lesson->class)->module;
            $moduleSlug = $module?->slug ?: 'unknown';
            $materials[] = [
                'icon' => 'play_circle',
                'label' => 'Video Lesson',
                'title' => $lesson->title,
                'url' => route('lessons.show', ['moduleSlug' => $moduleSlug, 'lessonSlug' => $lesson->slug])
            ];
        }

        if ($node->lmsMaterial) {
            $resource = $node->lmsMaterial;
            $module = $resource->module ?? optional($resource->class)->module;
            $moduleSlug = $module?->slug ?: 'unknown';
            $materials[] = [
                'icon' => 'description',
                'label' => 'PDF Resource',
                'title' => $resource->title,
                'url' => route('modules.show', $moduleSlug)
            ];
        }
        
        return $materials;
    }

    protected function syncSelectedNodeAfterFiltering()
    {
        $visibleIds = collect($this->visibleNodes)->pluck('id');

        if (!$this->selectedNodeId || !$visibleIds->contains($this->selectedNodeId)) {
            $this->selectedNodeId = $visibleIds->first();
        }
    }

    protected function dispatchUpdatedCanvasData()
    {
        $this->dispatch('knowledge-map-data-updated', nodes: $this->visibleNodes, connections: $this->visibleConnections);
    }

    public function render()
    {
        return view('livewire.frontend.knowledge-map.knowledge-map-page', [
            'visibleNodes' => $this->visibleNodes,
            'visibleConnections' => $this->visibleConnections,
            'selectedNode' => $this->selectedNode,
            'tagGroups' => $this->tagGroups,
            'learningPaths' => $this->learningPaths,
        ])->layout('layouts.public');
    }
}
