<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingStep extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'description',
        'type',
        'content',
        'sort_order',
        'is_active',
        'upsc_integration',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
        'upsc_integration' => 'boolean',
    ];

    /**
     * Map 'content' to 'config' for the new UI.
     */
    public function getConfigAttribute()
    {
        return $this->content;
    }

    /**
     * Map 'description' to 'supporting_text' for the new UI.
     */
    public function getSupportingTextAttribute()
    {
        return $this->description;
    }

    /**
     * Map 'type' to 'step_type' for the new UI.
     */
    public function getStepTypeAttribute()
    {
        return $this->type;
    }
}
