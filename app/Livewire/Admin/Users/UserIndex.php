<?php

namespace App\Livewire\Admin\Users;

use App\Models\MembershipSetting;
use App\Models\User;
use App\Models\UserMembership;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = 'all'; // all, members, non-members

    // Membership assignment modal state
    public $assigningUserId = null;
    public $selectedMembershipId = null;

    // Membership revoke modal state
    public $revokingUserId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function openMembershipModal($userId)
    {
        $this->assigningUserId = $userId;
        $this->selectedMembershipId = null;
    }

    public function closeMembershipModal()
    {
        $this->assigningUserId = null;
        $this->selectedMembershipId = null;
    }

    public function openRevokeModal($userId)
    {
        $this->revokingUserId = $userId;
    }

    public function closeRevokeModal()
    {
        $this->revokingUserId = null;
    }

    public function assignMembership()
    {
        $this->validate([
            'assigningUserId' => 'required|exists:users,id',
            'selectedMembershipId' => 'required|exists:membership_settings,id',
        ]);

        $setting = MembershipSetting::findOrFail($this->selectedMembershipId);

        UserMembership::updateOrCreate(
            ['user_id' => $this->assigningUserId],
            [
                'membership_setting_id' => $setting->id,
                'amount_paid_inr' => $setting->price_inr,
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => now()->addYear(),
                'payment_reference' => 'ADMIN_ASSIGNED_' . strtoupper(uniqid()),
            ]
        );

        $this->closeMembershipModal();
        session()->flash('message', 'Membership assigned successfully.');
    }

    public function revokeMembership()
    {
        if (!$this->revokingUserId) return;

        $membership = UserMembership::where('user_id', $this->revokingUserId)->where('status', 'active')->first();
        
        if ($membership) {
            $membership->update([
                'status' => 'expired',
                'expires_at' => now()
            ]);
            
            session()->flash('message', 'Membership revoked successfully.');
        }

        $this->closeRevokeModal();
    }

    public function render()
    {
        $query = User::query()->with('membership.membershipSetting');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('whatsapp_phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filter === 'members') {
            $query->whereHas('membership', function($q) {
                $q->where('status', 'active');
            });
        } elseif ($this->filter === 'non-members') {
            $query->whereDoesntHave('membership', function($q) {
                $q->where('status', 'active');
            });
        }

        $users = $query->latest()->paginate(20);
        $membershipSettings = MembershipSetting::where('is_active', true)->get();

        return view('livewire.admin.users.user-index', [
            'users' => $users,
            'membershipSettings' => $membershipSettings,
        ])->layout('layouts.admin');
    }
}
