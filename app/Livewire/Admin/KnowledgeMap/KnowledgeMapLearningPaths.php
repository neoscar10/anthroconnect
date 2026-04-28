<?php

namespace App\Livewire\Admin\KnowledgeMap;

use Livewire\Component;
use App\Models\KnowledgeMap\KnowledgeMap;
use App\Models\KnowledgeMap\KnowledgeMapLearningPath;
use App\Models\KnowledgeMap\KnowledgeMapNode;
use App\Services\KnowledgeMap\KnowledgeMapLearningPathService;

class KnowledgeMapLearningPaths extends Component
{
    public KnowledgeMap $map;
    public $selectedPathId = null;
    
    // Path Form
    public $showPathModal = false;
    public $pathTitle;
    public $pathDescription;
    public $pathDifficulty = 'beginner';
    public $pathDuration;
    public $isFeatured = false;

    // Node Attachment
    public $selectedNodeIdToAdd;

    public function mount()
    {
        $this->map = KnowledgeMap::where('slug', 'main-map')->firstOrFail();
    }

    public function selectPath($id)
    {
        $this->selectedPathId = $id;
    }

    public function openAddPath()
    {
        $this->pathTitle = '';
        $this->pathDescription = '';
        $this->pathDifficulty = 'beginner';
        $this->showPathModal = true;
    }

    public function savePath(KnowledgeMapLearningPathService $service)
    {
        $data = [
            'knowledge_map_id' => $this->map->id,
            'title' => $this->pathTitle,
            'description' => $this->pathDescription,
            'difficulty' => $this->pathDifficulty,
            'estimated_duration' => $this->pathDuration,
            'is_featured' => $this->isFeatured,
        ];

        $path = $service->createPath($data);
        $this->selectedPathId = $path->id;
        $this->showPathModal = false;
    }

    public function addNodeToPath(KnowledgeMapLearningPathService $service)
    {
        if (!$this->selectedPathId || !$this->selectedNodeIdToAdd) return;

        $path = KnowledgeMapLearningPath::find($this->selectedPathId);
        $order = $path->nodes()->count() + 1;
        
        $service->attachNode($path, $this->selectedNodeIdToAdd, ['sort_order' => $order]);
        $this->selectedNodeIdToAdd = null;
    }

    public function removeNodeFromPath($nodeId, KnowledgeMapLearningPathService $service)
    {
        $path = KnowledgeMapLearningPath::find($this->selectedPathId);
        $service->detachNode($path, $nodeId);
    }

    public function deletePath($id, KnowledgeMapLearningPathService $service)
    {
        $path = KnowledgeMapLearningPath::find($id);
        $service->deletePath($path);
        if ($this->selectedPathId == $id) $this->selectedPathId = null;
    }

    public function render()
    {
        return view('livewire.admin.knowledge-map.learning-paths', [
            'paths' => $this->map->learningPaths()->with('nodes')->get(),
            'availableNodes' => $this->map->nodes()->orderBy('title')->get(),
            'selectedPath' => $this->selectedPathId ? KnowledgeMapLearningPath::with('nodes')->find($this->selectedPathId) : null,
        ])->layout('layouts.admin', ['title' => 'Learning Paths']);
    }
}
