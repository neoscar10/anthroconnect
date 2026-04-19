<?php

namespace App\Services\Membership;

use App\Models\MembershipSetting;
use App\Models\MembershipPrivilege;
use App\Models\UserMembership;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MembershipService
{
    /**
     * Get the current active membership configuration.
     */
    public function getCurrentSettings(): ?MembershipSetting
    {
        return MembershipSetting::with('privileges')->where('is_active', true)->first();
    }

    /**
     * Create or update the global membership configuration.
     */
    public function updateConfiguration(array $data): MembershipSetting
    {
        return DB::transaction(function () use ($data) {
            $setting = MembershipSetting::first() ?? new MembershipSetting();
            
            $setting->fill([
                'title' => $data['title'] ?? 'AnthroConnect Membership',
                'price_inr' => $data['price_inr'],
                'description' => $data['description'] ?? '',
                'is_active' => $data['is_active'] ?? true,
            ]);
            
            $setting->save();

            // Sync Privileges
            $this->syncPrivileges($setting, $data['privileges'] ?? []);

            return $setting->load('privileges');
        });
    }

    /**
     * Activate membership for a user.
     */
    public function activateMembership(User $user, MembershipSetting $setting, string $paymentReference): UserMembership
    {
        return UserMembership::updateOrCreate(
            ['user_id' => $user->id],
            [
                'membership_setting_id' => $setting->id,
                'amount_paid_inr' => $setting->price_inr,
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => null, // Permanent for now
                'payment_reference' => $paymentReference,
            ]
        );
    }

    /**
     * Sync privileges for a membership setting.
     */
    protected function syncPrivileges(MembershipSetting $setting, array $privileges)
    {
        $setting->privileges()->delete();

        foreach ($privileges as $index => $privilegeText) {
            if (empty(trim($privilegeText))) continue;

            $setting->privileges()->create([
                'privilege' => $privilegeText,
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * Get members query for the table.
     */
    public function getMembersQuery()
    {
        return UserMembership::with('user', 'membershipSetting')
            ->orderBy('created_at', 'desc');
    }

    public function getActiveMembersCount(): int
    {
        return UserMembership::where('status', 'active')->count();
    }
}
