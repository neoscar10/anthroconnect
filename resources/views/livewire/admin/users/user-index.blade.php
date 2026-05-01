<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="font-serif text-3xl font-bold text-primary dark:text-stone-100">User Management</h1>
            <p class="text-stone-500 text-sm mt-1">Manage platform users, onboarding status, and memberships.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-primary/10 border border-primary/20 text-primary px-4 py-3 rounded-xl flex items-center">
            <span class="material-symbols-outlined mr-2">check_circle</span>
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-200/60 overflow-hidden">
        <!-- Toolbar -->
        <div class="p-4 border-b border-stone-200/60 flex flex-col sm:flex-row gap-4 justify-between items-center bg-stone-50/50">
            <div class="relative w-full sm:w-96">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name, email, or WhatsApp..." 
                       class="w-full bg-white border-stone-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-sm">
            </div>
            
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <select wire:model.live="filter" class="w-full sm:w-auto bg-white border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm text-stone-700">
                    <option value="all">All Users</option>
                    <option value="members">Active Members</option>
                    <option value="non-members">Non-Members</option>
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200/60 text-xs uppercase tracking-wider text-stone-500">
                        <th class="px-6 py-4 font-semibold">User</th>
                        <th class="px-6 py-4 font-semibold">Contact</th>
                        <th class="px-6 py-4 font-semibold">Joined</th>
                        <th class="px-6 py-4 font-semibold">Membership</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-stone-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border border-stone-200 shadow-sm mr-3">
                                    <div>
                                        <div class="font-medium text-stone-900">{{ $user->name }}</div>
                                        <div class="text-xs text-stone-500 mt-0.5">
                                            @if($user->hasRole('Admin') || $user->hasRole('Super Admin'))
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-800">
                                                    Admin
                                                </span>
                                            @endif
                                            @if($user->onboarding_completed_at)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800 ml-1">
                                                    Onboarded
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-stone-700">{{ $user->email }}</div>
                                @if($user->whatsapp_phone)
                                    <div class="text-xs text-stone-500 mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.88-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.347-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.876 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        {{ $user->whatsapp_phone }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-stone-900">{{ $user->created_at->format('M j, Y') }}</div>
                                <div class="text-xs text-stone-500">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->membership && $user->membership->status === 'active')
                                    <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                                        <span class="material-symbols-outlined text-[14px] mr-1">workspace_premium</span>
                                        {{ $user->membership->membershipSetting->title ?? 'Active' }}
                                    </div>
                                    <div class="text-[10px] text-stone-500 mt-1 pl-1">
                                        Expires: {{ optional($user->membership->expires_at)->format('M j, Y') ?? 'N/A' }}
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-stone-100 text-stone-600 border border-stone-200">
                                        None
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($user->membership && $user->membership->status === 'active')
                                    <button wire:click="openRevokeModal({{ $user->id }})" class="text-error hover:bg-error/10 px-3 py-1.5 rounded-lg transition-colors text-sm font-medium inline-flex items-center">
                                        <span class="material-symbols-outlined text-[18px] mr-1">cancel</span>
                                        Revoke
                                    </button>
                                @else
                                    <button wire:click="openMembershipModal({{ $user->id }})" class="text-primary hover:bg-primary/10 px-3 py-1.5 rounded-lg transition-colors text-sm font-medium inline-flex items-center">
                                        <span class="material-symbols-outlined text-[18px] mr-1">add_circle</span>
                                        Assign Plan
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-stone-100 text-stone-400 mb-3">
                                    <span class="material-symbols-outlined text-2xl">group_off</span>
                                </div>
                                <h3 class="text-sm font-medium text-stone-900">No users found</h3>
                                <p class="mt-1 text-sm text-stone-500">No users match your current search or filter criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-stone-200/60 bg-stone-50/30">
                {{ $users->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

    <!-- Assign Membership Modal -->
    @if($assigningUserId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-sm transition-opacity" wire:transition.opacity>
            <div class="bg-surface rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-stone-200/50" @click.outside="$wire.closeMembershipModal()">
                <div class="px-6 py-4 border-b border-stone-200/60 flex justify-between items-center bg-stone-50/50">
                    <h3 class="text-lg font-serif font-bold text-stone-800">Assign Membership</h3>
                    <button wire:click="closeMembershipModal" class="text-stone-400 hover:text-stone-600 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <p class="text-sm text-stone-600 mb-4">Select a membership plan to assign to this user. The membership will be active for 1 year from today.</p>
                    
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Select Plan</label>
                        <select wire:model="selectedMembershipId" class="w-full bg-white border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm text-stone-700">
                            <option value="">-- Select a Plan --</option>
                            @foreach($membershipSettings as $setting)
                                <option value="{{ $setting->id }}">{{ $setting->title }} (₹{{ number_format($setting->price_inr, 2) }})</option>
                            @endforeach
                        </select>
                        @error('selectedMembershipId') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-stone-200/60 flex justify-end space-x-3 bg-stone-50/50">
                    <button wire:click="closeMembershipModal" class="px-4 py-2 text-sm font-medium text-stone-600 hover:text-stone-800 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="assignMembership" 
                            class="px-5 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-xl shadow-sm transition-colors flex items-center disabled:opacity-50"
                            wire:loading.attr="disabled"
                            wire:target="assignMembership">
                        <span wire:loading.remove wire:target="assignMembership">Assign Membership</span>
                        <span wire:loading wire:target="assignMembership" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Revoke Membership Modal -->
    @if($revokingUserId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-sm transition-opacity" wire:transition.opacity>
            <div class="bg-surface rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-stone-200/50" @click.outside="$wire.closeRevokeModal()">
                <div class="px-6 py-4 border-b border-stone-200/60 flex justify-between items-center bg-stone-50/50">
                    <h3 class="text-lg font-serif font-bold text-stone-800">Revoke Membership</h3>
                    <button wire:click="closeRevokeModal" class="text-stone-400 hover:text-stone-600 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <div class="p-8 text-center space-y-6">
                    <!-- Warning Icon -->
                    <div class="w-20 h-20 bg-error/10 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-4xl text-error">warning</span>
                    </div>
                    
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold text-stone-900">Are you absolutely sure?</h3>
                        <p class="text-sm text-stone-500 leading-relaxed">
                            This will immediately revoke the user's active membership. They will lose access to all premium features and archived content.
                        </p>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-stone-200/60 flex flex-col sm:flex-row-reverse gap-3 bg-stone-50/50">
                    <button wire:click="revokeMembership" 
                            class="w-full sm:w-auto px-6 py-2.5 text-sm font-bold text-white bg-error hover:bg-error/90 rounded-xl shadow-sm transition-all flex items-center justify-center disabled:opacity-50"
                            wire:loading.attr="disabled"
                            wire:target="revokeMembership">
                        <span wire:loading.remove wire:target="revokeMembership">Revoke Membership</span>
                        <span wire:loading wire:target="revokeMembership" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                    <button wire:click="closeRevokeModal" class="w-full sm:w-auto px-6 py-2.5 text-sm font-medium text-stone-600 hover:text-stone-800 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
