<?php

namespace App\Models\Encyclopedia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $table = 'encyclopedia_topics';

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Relationship with Anthropologists.
     */
    public function anthropologists()
    {
        return $this->belongsToMany(
            Anthropologist::class,
            'anthropologist_encyclopedia_topic',
            'topic_id',
            'anthropologist_id'
        );
    }
}
