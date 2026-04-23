<?php

namespace App\Services\Library;

use App\Models\LibraryResource;
use App\Models\User;

class LibraryAccessService
{
    public function check(?User $user, LibraryResource $resource): array
    {
        $isMemberOnly = $resource->access_type === 'member_only';

        if (!$isMemberOnly) {
            return [
                'allowed' => true,
                'is_member_only' => false,
                'reason' => 'public',
                'cta_label' => 'Open Resource',
                'lock_message' => null,
            ];
        }

        if (!$user) {
            return [
                'allowed' => false,
                'is_member_only' => true,
                'reason' => 'guest_login_required',
                'cta_label' => 'Login to Access',
                'lock_message' => 'Login and become a member to access this resource.',
            ];
        }

        if ($this->userHasActiveMembership($user)) {
            return [
                'allowed' => true,
                'is_member_only' => true,
                'reason' => 'allowed_member',
                'cta_label' => 'Open Resource',
                'lock_message' => null,
            ];
        }

        return [
            'allowed' => false,
            'is_member_only' => true,
            'reason' => 'membership_required',
            'cta_label' => 'Become a Member',
            'lock_message' => 'This resource is available to AnthroConnect members only.',
        ];
    }

    protected function userHasActiveMembership(User $user): bool
    {
        return $user->isMember();
    }
}
