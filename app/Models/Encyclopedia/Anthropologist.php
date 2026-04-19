<?php

namespace App\Models\Encyclopedia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anthropologist extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'encyclopedia_anthropologists';

    protected $fillable = [
        'full_name',
        'slug',
        'summary',
        'biography_markdown',
        'birth_year',
        'death_year',
        'discipline_or_specialization',
        'nationality',
        'profile_image',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'birth_year' => 'integer',
        'death_year' => 'integer',
    ];

    /**
     * Relationship with Encyclopedia Topics.
     */
    public function topics()
    {
        return $this->belongsToMany(
            \App\Models\Topic::class,
            'anthropologist_encyclopedia_topic',
            'anthropologist_id',
            'topic_id'
        );
    }

    /**
     * Relationship with Core Concepts.
     */
    public function coreConcepts()
    {
        return $this->belongsToMany(
            CoreConcept::class,
            'anthropologist_core_concept',
            'anthropologist_id',
            'core_concept_id'
        );
    }
}
