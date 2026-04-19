@extends('layouts.admin')

@section('content')
<div x-data="membershipManager()">
    <!-- Header Section -->
    <div class="flex justify-between items-end mb-12">
        <div>
            <h1 class="font-headline text-4xl text-on-surface mb-2">Membership Management</h1>
            <p class="font-body text-on-surface-variant text-lg">Define the global membership offering and monitor active scholars.</p>
        </div>
        <button @click="openModal()" class="bg-gradient-to-br from-primary to-primary-container text-on-primary px-6 py-3 rounded-lg font-bold uppercase tracking-widest text-xs flex items-center gap-2 shadow-lg shadow-primary/10 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">settings</span>
            Configure Membership
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-primary-fixed text-on-primary-fixed-variant rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Membership summary & stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <!-- Offering Overview Card -->
        <div class="md:col-span-3 bg-surface-container-low p-8 rounded-2xl border border-outline-variant/20 shadow-sm relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-8xl">workspace_premium</span>
            </div>
            
            <div class="flex flex-col md:flex-row justify-between gap-8">
                <div class="flex-1">
                    <h3 class="font-label text-xs uppercase tracking-widest text-primary font-bold mb-4">Current Offering</h3>
                    @if($settings)
                        <h2 class="font-headline text-3xl text-on-surface mb-2">{{ $settings->title }}</h2>
                        <div class="flex items-center gap-4 text-sm text-on-surface-variant mb-6">
                            <span class="font-bold text-primary">₹ {{ number_format($settings->price_inr, 2) }}</span>
                            <span class="w-1 h-1 bg-outline-variant rounded-full"></span>
                            <span>{{ $settings->privileges->count() }} Privileges</span>
                            <span class="w-1 h-1 bg-outline-variant rounded-full"></span>
                            <span class="flex items-center gap-1 {{ $settings->is_active ? 'text-primary' : 'text-stone-400' }}">
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">circle</span>
                                {{ $settings->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <p class="font-body text-on-surface-variant max-w-2xl leading-relaxed">
                            {{ $settings->description ?: 'No description provided.' }}
                        </p>
                    @else
                        <div class="py-4">
                            <p class="text-on-surface-variant italic">No membership has been configured yet. Click "Configure Membership" to set up your first offering.</p>
                        </div>
                    @endif
                </div>
                
                <div class="shrink-0 space-y-3">
                    <h3 class="font-label text-xs uppercase tracking-widest text-on-surface-variant font-bold mb-4">Privileges</h3>
                    <div class="space-y-2">
                        @forelse($settings->privileges ?? [] as $privilege)
                            <div class="flex items-center gap-2 text-xs text-on-surface">
                                <span class="material-symbols-outlined text-primary text-[16px]">check_circle</span>
                                {{ $privilege->privilege }}
                            </div>
                        @empty
                            <span class="text-[10px] text-stone-400 uppercase tracking-widest">No privileges defined</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="bg-primary text-on-primary p-8 rounded-2xl shadow-xl shadow-primary/20 flex flex-col justify-between">
            <div>
                <span class="font-label text-xs uppercase tracking-widest font-bold opacity-80">Active Members</span>
            </div>
            <div>
                <div class="text-5xl font-headline italic font-bold mb-1">{{ $activeMembersCount }}</div>
                <p class="text-xs opacity-70">Scholars with active subscriptions</p>
            </div>
            <div class="mt-4 pt-4 border-t border-on-primary/10">
                <a href="#" class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-1 hover:gap-2 transition-all">
                    View Analytics <span class="material-symbols-outlined text-xs">trending_flat</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Members Table Section -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/20 overflow-hidden">
        <div class="p-6 border-b border-outline-variant/10 flex justify-between items-center">
            <h3 class="font-headline text-2xl text-on-surface">Registered Members</h3>
            <div class="flex gap-2">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                    <input class="bg-surface-container-low border-none rounded-lg pl-10 pr-4 py-2 text-xs focus:ring-2 focus:ring-primary w-64" placeholder="Search members..." type="text"/>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low">
                    <tr>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Scholar Name</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Status</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Amount Paid</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Period</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Payment Ref.</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($members as $member)
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-surface-container-high flex items-center justify-center overflow-hidden border border-outline-variant/20">
                                        <img src="{{ $member->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($member->user->name) }}" class="object-cover h-full w-full" alt="">
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-on-surface">{{ $member->user->name }}</p>
                                        <p class="text-[10px] text-on-surface-variant">{{ $member->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($member->status) {
                                        'active' => 'bg-primary-container text-on-primary-container',
                                        'pending' => 'bg-tertiary-container text-on-tertiary-container',
                                        'expired' => 'bg-surface-container-highest text-on-surface-variant',
                                        'cancelled' => 'bg-error-container text-on-error-container',
                                        default => 'bg-stone-100'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $statusColor }}">
                                    {{ $member->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium">₹ {{ number_format($member->amount_paid_inr, 2) }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="text-on-surface">Started: {{ $member->started_at?->format('d M Y') ?: '—' }}</div>
                                <div class="text-on-surface-variant text-[10px]">Expires: {{ $member->expires_at?->format('d M Y') ?: 'Permanent' }}</div>
                            </td>
                            <td class="px-6 py-4 text-[10px] font-mono text-stone-500">
                                {{ $member->payment_reference ?: 'MANUAL' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-stone-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-sm">more_vert</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-5xl text-outline-variant/30 mb-2">person_search</span>
                                    <p class="text-on-surface-variant font-headline text-lg italic">No members found yet.</p>
                                    <p class="text-[10px] uppercase tracking-widest text-stone-400 mt-1">Users will appear here once they subscribe.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($members->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/10 bg-surface-container-low/30">
                {{ $members->links() }}
            </div>
        @endif
    </div>

    <!-- Configure Membership Modal -->
    <div x-show="modalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
         x-cloak>
        <!-- Backdrop -->
        <div x-show="modalOpen" 
             style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(8px);"
             class="fixed inset-0 transition-opacity"
             @click="closeModal()"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <!-- Modal Content -->
        <div x-show="modalOpen"
             class="bg-surface-container-lowest rounded-3xl shadow-2xl ring-1 ring-white/10 w-full max-w-2xl overflow-hidden relative z-10"
             x-transition:enter="ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0 translate-y-4"
             x-transition:enter-end="scale-100 opacity-100 translate-y-0"
             x-transition:leave="ease-in duration-200 transform"
             x-transition:leave-start="scale-100 opacity-100 translate-y-0"
             x-transition:leave-end="scale-95 opacity-0 translate-y-4">
            
            <form action="{{ route('admin.membership.configure') }}" method="POST" class="flex flex-col h-full max-h-[90vh]">
                @csrf
                <!-- Modal Header -->
                <div class="px-8 py-6 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/50">
                    <div>
                        <h2 class="font-headline text-2xl text-on-surface italic">Configure Membership Setting</h2>
                        <p class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">AnthroConnect Global Provision</p>
                    </div>
                    <button type="button" @click="closeModal()" class="text-stone-400 hover:text-on-surface transition-colors p-2 rounded-full hover:bg-surface-container-high">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-8 overflow-y-auto space-y-8 no-scrollbar">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2 sm:col-span-1 space-y-2">
                            <label class="font-label text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Membership Title</label>
                            <input name="title" x-model="config.title" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none" type="text" placeholder="e.g. AnthroConnect Premium"/>
                        </div>
                        <div class="col-span-2 sm:col-span-1 space-y-2">
                            <label class="font-label text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Price (INR)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-primary font-bold">₹</span>
                                <input name="price_inr" x-model="config.price_inr" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl pl-8 pr-3 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none font-sans" type="number" step="0.01" min="0"/>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="font-label text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Description</label>
                        <textarea name="description" x-model="config.description" rows="3" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none resize-none" placeholder="Provide a compelling summary of the membership offering..."></textarea>
                    </div>

                    <!-- Privileges Repeater -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <label class="font-label text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Privileges & Benefits</label>
                            <button type="button" @click="addPrivilege" class="text-primary font-bold text-xs flex items-center gap-1 bg-primary/10 px-3 py-1 rounded-full hover:bg-primary/20 transition-colors">
                                <span class="material-symbols-outlined text-sm">add</span> Add Row
                            </button>
                        </div>
                        <div class="space-y-3">
                            <template x-for="(priv, index) in config.privileges" :key="index">
                                <div class="flex gap-2 group">
                                    <div class="flex-1 relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-sm text-primary">verified</span>
                                        <input :name="'privileges['+index+']'" 
                                               x-model="config.privileges[index]" 
                                               class="w-full bg-white dark:bg-stone-900 border border-outline-variant/20 rounded-xl pl-10 pr-3 py-3 text-xs focus:ring-1 focus:ring-primary outline-none transition-all shadow-sm group-hover:border-primary/30" 
                                               type="text" 
                                               placeholder="e.g. Access to restricted archives"/>
                                    </div>
                                    <button type="button" @click="config.privileges.splice(index, 1)" class="p-3 text-stone-300 hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </div>
                            </template>
                            <template x-if="config.privileges.length === 0">
                                <div class="p-6 border-2 border-dashed border-outline-variant/10 rounded-xl text-center">
                                    <p class="text-[10px] uppercase tracking-widest text-stone-400">No privileges added. Click 'Add Row' to start.</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-outline-variant/10">
                        <label class="flex items-center gap-3 cursor-pointer group w-fit">
                            <div class="relative inline-flex items-center">
                                <input name="is_active" type="checkbox" value="1" x-model="config.is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </div>
                            <span class="text-xs font-bold text-on-surface uppercase tracking-widest">Active & Visible to Public</span>
                        </label>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-8 py-6 border-t border-outline-variant/10 flex justify-end gap-3 bg-surface-container-low/30">
                    <button type="button" @click="closeModal()" class="px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:bg-surface-container-high transition-all">Cancel</button>
                    <button type="submit" class="bg-primary text-on-primary px-10 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all">Save Configuration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function membershipManager() {
    return {
        modalOpen: false,
        config: {
            title: @json($settings->title ?? 'AnthroConnect Membership'),
            price_inr: @json($settings->price_inr ?? 0),
            description: @json($settings->description ?? ''),
            is_active: @json($settings->is_active ?? true),
            privileges: @json(($settings->privileges ?? collect())->pluck('privilege')->toArray() ?: [])
        },

        openModal() {
            this.modalOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.modalOpen = false;
            document.body.style.overflow = 'auto';
        },

        addPrivilege() {
            this.config.privileges.push('');
        }
    }
}
</script>
@endsection
