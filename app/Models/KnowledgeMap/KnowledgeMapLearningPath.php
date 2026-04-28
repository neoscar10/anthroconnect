<?php

namespace App\Models\KnowledgeMap;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KnowledgeMapLearningPath extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'knowledge_map_id', 'title', 'slug', 'description',
        'difficulty', 'estimated_duration', 'icon', 'color',
        'is_featured', 'sort_order'
    ];

    protected $casts = [
        'is_featured' => 'boolean'
    ];

    public function knowledgeMap(): BelongsTo
    {
        return $this->belongsTo(KnowledgeMap::class);
    }

    public function nodes(): BelongsToMany
    {
        return $this->belongsToMany(KnowledgeMapNode::class, 'knowledge_map_learning_path_nodes', 'learning_path_id', 'node_id')
                    ->withPivot('sort_order', 'note')
                    ->withTimestamps();
    }
}
