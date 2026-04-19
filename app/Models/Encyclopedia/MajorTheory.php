<?php

namespace App\Models\Encyclopedia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MajorTheory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'encyclopedia_major_theories';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'body_markdown',
        'key_thinkers_text',
        'status',
    ];
}
