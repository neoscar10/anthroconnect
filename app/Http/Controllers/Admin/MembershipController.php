<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Membership\MembershipService;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    protected MembershipService $membershipService;

    public function __construct(MembershipService $membershipService)
    {
        $this->membershipService = $membershipService;
    }

    /**
     * Display the membership management dashboard.
     */
    public function index()
    {
        $settings = $this->membershipService->getCurrentSettings();
        $members = $this->membershipService->getMembersQuery()->paginate(15);
        $activeMembersCount = $this->membershipService->getActiveMembersCount();

        return view('admin.membership.index', compact('settings', 'members', 'activeMembersCount'));
    }

    /**
     * Configure the membership offering.
     */
    public function configure(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price_inr' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'privileges' => 'nullable|array',
            'privileges.*' => 'nullable|string|max:255',
        ]);

        $this->membershipService->updateConfiguration($validated);

        return redirect()->route('admin.membership.index')
            ->with('success', 'Membership configuration updated successfully.');
    }
}
