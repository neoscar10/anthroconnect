<?php

namespace App\Services\KnowledgeMap;

use App\Models\KnowledgeMap\KnowledgeMapLearningPath;
use Illuminate\Support\Str;

class KnowledgeMapLearningPathService
{
    public function createPath(array $data): KnowledgeMapLearningPath
    {
        $data['slug'] = Str::slug($data['title']);
        return KnowledgeMapLearningPath::create($data);
    }

    public function updatePath(KnowledgeMapLearningPath $path, array $data): bool
    {
        if (isset($data['title']) && $data['title'] !== $path->title) {
            $data['slug'] = Str::slug($data['title']);
        }
        return $path->update($data);
    }

    public function attachNode(KnowledgeMapLearningPath $path, int $nodeId, array $pivotData = []): void
    {
        $path->nodes()->attach($nodeId, $pivotData);
    }

    public function detachNode(KnowledgeMapLearningPath $path, int $nodeId): void
    {
        $path->nodes()->detach($nodeId);
    }

    public function reorderNodes(KnowledgeMapLearningPath $path, array $nodeOrders): void
    {
        foreach ($nodeOrders as $nodeId => $order) {
            $path->nodes()->updateExistingPivot($nodeId, ['sort_order' => $order]);
        }
    }

    public function deletePath(KnowledgeMapLearningPath $path): bool
    {
        return $path->delete();
    }
}
