<?php

namespace App\Models\KnowledgeMap;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class KnowledgeMapNodeAttachment extends Model
{
    protected $fillable = [
        'node_id', 'attachable_id', 'attachable_type', 'sort_order'
    ];

    public function node(): BelongsTo
    {
        return $this->belongsTo(KnowledgeMapNode::class, 'node_id');
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
