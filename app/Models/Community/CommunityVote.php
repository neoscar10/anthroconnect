<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CommunityVote extends Model
{
    use HasFactory;

    protected $table = 'community_discussion_votes';

    protected $fillable = [
        'user_id',
        'votable_type',
        'votable_id',
        'vote',
    ];

    /**
     * Relationship: The user who cast the vote.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Polymorphic target (Discussion or Reply).
     */
    public function votable()
    {
        return $this->morphTo();
    }
}
