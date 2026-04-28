<?php

namespace App\Services\KnowledgeMap;

use App\Models\KnowledgeMap\KnowledgeMap;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class KnowledgeMapService
{
    public function createMap(array $data): KnowledgeMap
    {
        $data['slug'] = Str::slug($data['title']);
        $data['created_by'] = Auth::id();
        
        return KnowledgeMap::create($data);
    }

    public function updateMap(KnowledgeMap $map, array $data): bool
    {
        if (isset($data['title']) && $data['title'] !== $map->title) {
            $data['slug'] = Str::slug($data['title']);
        }
        
        $data['updated_by'] = Auth::id();
        
        return $map->update($data);
    }

    public function publish(KnowledgeMap $map): bool
    {
        return $map->update([
            'status' => 'published',
            'published_at' => now(),
            'updated_by' => Auth::id()
        ]);
    }

    public function unpublish(KnowledgeMap $map): bool
    {
        return $map->update([
            'status' => 'draft',
            'updated_by' => Auth::id()
        ]);
    }

    public function duplicate(KnowledgeMap $map): KnowledgeMap
    {
        $newMap = $map->replicate();
        $newMap->title = $map->title . ' (Copy)';
        $newMap->slug = Str::slug($newMap->title);
        $newMap->status = 'draft';
        $newMap->created_by = Auth::id();
        $newMap->save();

        // Duplicate nodes
        foreach ($map->nodes as $node) {
            $newNode = $node->replicate();
            $newNode->knowledge_map_id = $newMap->id;
            $newNode->save();
            
            // Map old node ID to new node ID for connection duplicating
            $nodeMap[$node->id] = $newNode->id;
        }

        // Duplicate connections
        foreach ($map->connections as $connection) {
            $newConnection = $connection->replicate();
            $newConnection->knowledge_map_id = $newMap->id;
            $newConnection->from_node_id = $nodeMap[$connection->from_node_id];
            $newConnection->to_node_id = $nodeMap[$connection->to_node_id];
            $newConnection->save();
        }

        return $newMap;
    }

    public function deleteMap(KnowledgeMap $map): bool
    {
        return $map->delete();
    }
}
