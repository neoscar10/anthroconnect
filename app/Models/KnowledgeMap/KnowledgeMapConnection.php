<?php

namespace App\Models\KnowledgeMap;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeMapConnection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'knowledge_map_id', 'from_node_id', 'to_node_id', 'label',
        'connection_type', 'direction', 'line_style', 'color',
        'sort_order', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'json'
    ];

    public function knowledgeMap(): BelongsTo
    {
        return $this->belongsTo(KnowledgeMap::class);
    }

    public function fromNode(): BelongsTo
    {
        return $this->belongsTo(KnowledgeMapNode::class, 'from_node_id');
    }

    public function toNode(): BelongsTo
    {
        return $this->belongsTo(KnowledgeMapNode::class, 'to_node_id');
    }
}
