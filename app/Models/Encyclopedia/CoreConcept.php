<?php

namespace App\Models\Encyclopedia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreConcept extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'encyclopedia_core_concepts';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'body_markdown',
        'status',
        'featured_image',
    ];

    /**
     * Relationship with Anthropologists.
     */
    public function anthropologists()
    {
        return $this->belongsToMany(
            Anthropologist::class,
            'anthropologist_core_concept',
            'core_concept_id',
            'anthropologist_id'
        );
    }
}
