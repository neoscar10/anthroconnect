<?php

namespace App\Services\KnowledgeMap;

use App\Models\KnowledgeMap\KnowledgeMapNode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class KnowledgeMapNodeService
{
    public function createNode(array $data): KnowledgeMapNode
    {
        $data['slug'] = Str::slug($data['title']);
        $data['created_by'] = Auth::id();
        
        $node = KnowledgeMapNode::create($data);
        
        if (isset($data['tags'])) {
            $node->syncTags($data['tags']);
        }
        
        return $node;
    }

    public function updateNode(KnowledgeMapNode $node, array $data): bool
    {
        if (isset($data['title']) && $data['title'] !== $node->title) {
            $data['slug'] = Str::slug($data['title']);
        }
        
        $data['updated_by'] = Auth::id();
        
        $updated = $node->update($data);
        
        if ($updated && isset($data['tags'])) {
            $node->syncTags($data['tags']);
        }
        
        return $updated;
    }

    public function updatePosition(KnowledgeMapNode $node, float $x, float $y): bool
    {
        return $node->update([
            'position_x' => $x,
            'position_y' => $y,
            'updated_by' => Auth::id()
        ]);
    }

    public function duplicateNode(KnowledgeMapNode $node): KnowledgeMapNode
    {
        $newNode = $node->replicate();
        $newNode->title = $node->title . ' (Copy)';
        $newNode->position_x += 20;
        $newNode->position_y += 20;
        $newNode->created_by = Auth::id();
        $newNode->save();
        
        // Copy tags if trait supports it or manually
        if ($node->tags) {
            $newNode->syncTags($node->tags->pluck('id')->toArray());
        }

        return $newNode;
    }

    public function deleteNode(KnowledgeMapNode $node): bool
    {
        return $node->delete();
    }
}
