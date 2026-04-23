<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryResourceRelatedLearning extends Model
{
    use HasFactory;

    protected $table = 'library_resource_related_learning';

    protected $fillable = [
        'library_resource_id',
        'linkable_id',
        'linkable_type',
        'label',
        'relation_type',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function libraryResource()
    {
        return $this->belongsTo(LibraryResource::class);
    }

    public function linkable()
    {
        return $this->morphTo();
    }
}
