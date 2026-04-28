<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'selection_type',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship with Tags.
     */
    public function tags()
    {
        return $this->hasMany(Tag::class)->orderBy('display_order')->orderBy('name');
    }

    /**
     * Scope for active tags within this group.
     */
    public function activeTags()
    {
        return $this->tags()->where('is_active', true);
    }

    /**
     * Scope for active tag groups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Retrieve active tag groups that have at least one tag used by a specific model type.
     */
    public static function getGroupsWithUsage(string $modelClass)
    {
        $groupIds = \Illuminate\Support\Facades\DB::table('taggables')
            ->join('tags', 'taggables.tag_id', '=', 'tags.id')
            ->where('taggables.taggable_type', $modelClass)
            ->distinct()
            ->pluck('tags.tag_group_id');

        return self::whereIn('id', $groupIds)
            ->active()
            ->with(['activeTags' => fn($q) => $q->orderBy('name')])
            ->orderBy('display_order')
            ->get();
    }
}
