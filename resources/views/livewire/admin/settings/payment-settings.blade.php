<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="font-serif text-3xl font-bold text-primary dark:text-stone-100">Payment Settings</h1>
        <p class="text-stone-500 text-sm mt-1">Configure active gateways, prioritize checkout order, and monitor revenue performance.</p>
    </div>

    <!-- Feedback Messages -->
    @if ($errorMessage)
        <div class="p-4 bg-red-50 text-red-600 rounded-xl text-xs font-semibold flex items-center gap-2 border border-red-200">
            <span class="material-symbols-outlined text-[16px]">error</span>
            {{ $errorMessage }}
        </div>
    @endif

    @if ($successMessage)
        <div class="p-4 bg-green-50 text-green-700 rounded-xl text-xs font-semibold flex items-center gap-2 border border-green-200">
            <span class="material-symbols-outlined text-[16px]">check_circle</span>
            {{ $successMessage }}
        </div>
    @endif

    <!-- Analytics Dashboard Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Revenue Card -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/60 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center text-stone-400">
                <span class="text-xs uppercase tracking-wider font-semibold">Total Revenue</span>
                <span class="material-symbols-outlined text-primary text-[20px]">payments</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-serif font-bold text-stone-900">₹{{ number_format($totalRevenue, 2) }}</span>
            </div>
        </div>

        <!-- Successful Count Card -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/60 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center text-stone-400">
                <span class="text-xs uppercase tracking-wider font-semibold">Successful</span>
                <span class="material-symbols-outlined text-green-600 text-[20px]">check_circle</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-bold text-stone-900">{{ $successfulPayments }}</span>
                <span class="text-[10px] text-stone-400 block mt-1">Captured Payments</span>
            </div>
        </div>

        <!-- Failed Count Card -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/60 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center text-stone-400">
                <span class="text-xs uppercase tracking-wider font-semibold">Failed</span>
                <span class="material-symbols-outlined text-red-600 text-[20px]">cancel</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-bold text-stone-900">{{ $failedPayments }}</span>
                <span class="text-[10px] text-stone-400 block mt-1">Declined Payments</span>
            </div>
        </div>

        <!-- Refunded Count Card -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/60 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center text-stone-400">
                <span class="text-xs uppercase tracking-wider font-semibold">Refunded</span>
                <span class="material-symbols-outlined text-purple-600 text-[20px]">undo</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-bold text-stone-900">{{ $refundedPayments }}</span>
                <span class="text-[10px] text-stone-400 block mt-1">Returned Transactions</span>
            </div>
        </div>

        <!-- Today Transactions Card -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/60 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center text-stone-400">
                <span class="text-xs uppercase tracking-wider font-semibold">Today</span>
                <span class="material-symbols-outlined text-amber-600 text-[20px]">today</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-bold text-stone-900">{{ $transactionsToday }}</span>
                <span class="text-[10px] text-stone-400 block mt-1">New Orders Today</span>
            </div>
        </div>

        <!-- Monthly Transactions Card -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/60 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center text-stone-400">
                <span class="text-xs uppercase tracking-wider font-semibold">This Month</span>
                <span class="material-symbols-outlined text-blue-600 text-[20px]">calendar_month</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-bold text-stone-900">{{ $transactionsThisMonth }}</span>
                <span class="text-[10px] text-stone-400 block mt-1">Created this month</span>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (Grid split) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Gateway Configuration Card -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-stone-200/60 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-stone-100 bg-stone-50/50">
                    <h2 class="text-lg font-serif font-bold text-stone-850">Gateway Activation & Priority</h2>
                    <p class="text-xs text-stone-450 mt-1">Manage which gateways are offered on checkout and prioritize their visibility order.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-stone-50 border-b border-stone-100 text-xs font-bold text-stone-500 uppercase">
                                <th class="px-6 py-4">Gateway</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Default</th>
                                <th class="px-6 py-4">Priority</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-sm text-stone-700">
                            @foreach ($gateways as $index => $gw)
                                <tr class="hover:bg-stone-50/30 transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="font-bold text-stone-900">{{ $gw->display_name }}</div>
                                        <div class="text-[10px] text-stone-400 font-mono mt-0.5 uppercase">{{ $gw->gateway }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        @if ($gw->is_enabled)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">Active</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-stone-150 text-stone-500 border border-stone-250">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        @if ($gw->is_default)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">Default</span>
                                        @else
                                            <button wire:click="setDefault('{{ $gw->gateway }}')" class="text-xs text-stone-400 hover:text-stone-800 underline font-medium">Make Default</button>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-1">
                                            <button wire:click="moveUp('{{ $gw->gateway }}')" @if($index === 0) disabled class="opacity-30 cursor-not-allowed" @endif class="p-1 hover:bg-stone-100 rounded text-stone-500">
                                                <span class="material-symbols-outlined text-[16px]">arrow_upward</span>
                                            </button>
                                            <button wire:click="moveDown('{{ $gw->gateway }}')" @if($index === count($gateways) - 1) disabled class="opacity-30 cursor-not-allowed" @endif class="p-1 hover:bg-stone-100 rounded text-stone-500">
                                                <span class="material-symbols-outlined text-[16px]">arrow_downward</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <button wire:click="toggleGateway('{{ $gw->gateway }}')" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-all hover:bg-stone-50 {{ $gw->is_enabled ? 'bg-white border-red-200 text-red-600 hover:bg-red-50' : 'bg-primary text-white border-primary hover:opacity-90' }}">
                                            {{ $gw->is_enabled ? 'Disable' : 'Enable' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="p-6 bg-stone-50/50 border-t border-stone-100 text-xs text-stone-450 font-medium flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[14px]">shield</span> Safety regulations: A default gateway must always be active and you cannot disable the final active gateway.
            </div>
        </div>

        <!-- Revenue Breakdown Card -->
        <div class="bg-white rounded-2xl border border-stone-200/60 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-stone-100 bg-stone-50/50">
                <h2 class="text-lg font-serif font-bold text-stone-850">Revenue by Gateway</h2>
                <p class="text-xs text-stone-450 mt-1">Transaction counts and success ratios mapped by provider.</p>
            </div>

            <div class="p-6 space-y-6 flex-1">
                @foreach ($breakdown as $item)
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <div>
                                <span class="font-bold text-stone-900">{{ $item['display_name'] }}</span>
                                <span class="text-stone-400 text-xs font-normal ml-2">({{ $item['count'] }} tx)</span>
                            </div>
                            <span class="font-serif font-bold text-stone-900">₹{{ number_format($item['revenue'], 2) }}</span>
                        </div>
                        <div class="relative w-full bg-stone-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-primary h-full rounded-full" style="width: {{ min(100, $item['success_rate']) }}%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-stone-450">
                            <span>Success rate: {{ $item['success_rate'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
