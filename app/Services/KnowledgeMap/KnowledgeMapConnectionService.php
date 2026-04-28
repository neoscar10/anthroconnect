<?php

namespace App\Services\KnowledgeMap;

use App\Models\KnowledgeMap\KnowledgeMapConnection;
use Exception;

class KnowledgeMapConnectionService
{
    public function createConnection(array $data): KnowledgeMapConnection
    {
        $this->validateConnection($data);
        return KnowledgeMapConnection::create($data);
    }

    public function updateConnection(KnowledgeMapConnection $connection, array $data): bool
    {
        $this->validateConnection(array_merge($connection->toArray(), $data));
        return $connection->update($data);
    }

    public function deleteConnection(KnowledgeMapConnection $connection): bool
    {
        return $connection->delete();
    }

    protected function validateConnection(array $data): void
    {
        if ($data['from_node_id'] == $data['to_node_id']) {
            throw new Exception("A node cannot be connected to itself.");
        }

        // Check for duplicate connection in same direction
        $exists = KnowledgeMapConnection::where('knowledge_map_id', $data['knowledge_map_id'])
            ->where('from_node_id', $data['from_node_id'])
            ->where('to_node_id', $data['to_node_id'])
            ->where('id', '!=', $data['id'] ?? 0)
            ->exists();

        if ($exists) {
            throw new Exception("A connection between these nodes already exists in this direction.");
        }
    }
}
