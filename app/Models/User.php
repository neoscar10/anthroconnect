<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'avatar',
        'user_type',
        'onboarding_completed_at',
        'whatsapp_phone',
        'whatsapp_phone_verified_at',
        'otp_verified_at',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the membership record associated with the user.
     */
    public function membership(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserMembership::class)->where('status', 'active');
    }

    /**
     * Check if the user is an active member.
     */
    public function isMember(): bool
    {
        return $this->membership()->exists();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'onboarding_completed_at' => 'datetime',
            'whatsapp_phone_verified_at' => 'datetime',
            'otp_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Relationship to LMS lesson progress records.
     */
    public function lessonProgress()
    {
        return $this->hasMany(\App\Models\Lms\LmsLessonProgress::class);
    }

    /**
     * Relationship to community discussions.
     */
    public function communityDiscussions()
    {
        return $this->hasMany(\App\Models\Community\CommunityDiscussion::class);
    }

    /**
     * Relationship to community discussion replies.
     */
    public function communityReplies()
    {
        return $this->hasMany(\App\Models\Community\CommunityDiscussionReply::class);
    }

    /**
     * Relationship to community votes.
     */
    public function communityVotes()
    {
        return $this->hasMany(\App\Models\Community\CommunityVote::class);
    }

    /**
     * Authentication Helpers
     */
    public function hasVerifiedWhatsappPhone(): bool
    {
        return ! is_null($this->whatsapp_phone_verified_at);
    }

    public function hasCompletedOtpVerification(): bool
    {
        return ! is_null($this->otp_verified_at);
    }
}
