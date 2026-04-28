<?php

namespace App\Models\KnowledgeMap;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Concerns\HasTags;
use App\Models\User;
use App\Models\Encyclopedia\CoreConcept;
use App\Models\Encyclopedia\Anthropologist;
use App\Models\Encyclopedia\MajorTheory;
use App\Models\Lms\LmsModule;
use App\Models\Lms\LmsLesson;
use App\Models\Lms\LmsResource;

class KnowledgeMapNode extends Model
{
    use SoftDeletes, HasTags;

    protected $fillable = [
        'knowledge_map_id', 'title', 'slug', 'node_type', 'importance',
        'short_description', 'full_description', 'position_x', 'position_y',
        'width', 'height', 'color', 'icon', 'is_upsc_relevant', 'is_members_only',
        'encyclopedia_concept_id', 'anthropologist_id', 'theory_id',
        'lms_module_id', 'lms_lesson_id', 'lms_material_id',
        'manual_concept_title', 'manual_concept_summary',
        'estimated_read_time', 'sort_order', 'metadata',
        'created_by', 'updated_by'
    ];

    protected $casts = [
        'is_upsc_relevant' => 'boolean',
        'is_members_only' => 'boolean',
        'position_x' => 'decimal:2',
        'position_y' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'metadata' => 'json'
    ];

    public function knowledgeMap(): BelongsTo
    {
        return $this->belongsTo(KnowledgeMap::class);
    }

    public function encyclopediaConcept(): BelongsTo
    {
        return $this->belongsTo(CoreConcept::class, 'encyclopedia_concept_id');
    }

    public function anthropologist(): BelongsTo
    {
        return $this->belongsTo(Anthropologist::class, 'anthropologist_id');
    }

    public function theory(): BelongsTo
    {
        return $this->belongsTo(MajorTheory::class, 'theory_id');
    }

    public function lmsModule(): BelongsTo
    {
        return $this->belongsTo(LmsModule::class, 'lms_module_id');
    }

    public function lmsLesson(): BelongsTo
    {
        return $this->belongsTo(LmsLesson::class, 'lms_lesson_id');
    }

    public function lmsMaterial(): BelongsTo
    {
        return $this->belongsTo(LmsResource::class, 'lms_material_id');
    }

    public function outgoingConnections(): HasMany
    {
        return $this->hasMany(KnowledgeMapConnection::class, 'from_node_id');
    }

    public function incomingConnections(): HasMany
    {
        return $this->hasMany(KnowledgeMapConnection::class, 'to_node_id');
    }

    public function learningPaths(): BelongsToMany
    {
        return $this->belongsToMany(KnowledgeMapLearningPath::class, 'knowledge_map_learning_path_nodes', 'node_id', 'learning_path_id')
                    ->withPivot('sort_order', 'note')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeUpscRelevant($query)
    {
        return $query->where('is_upsc_relevant', true);
    }

    public function scopeMembersOnly($query)
    {
        return $query->where('is_members_only', true);
    }

    /**
     * Get all attachments for the node.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(KnowledgeMapNodeAttachment::class, 'node_id')->orderBy('sort_order');
    }
}
