<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="font-serif text-3xl font-bold text-primary dark:text-stone-100">Payment Audit Logs</h1>
            <p class="text-stone-500 text-sm mt-1">Audit payment transactions, lifecycle states, and webhook details.</p>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-stone-200/60 overflow-hidden">
        <!-- Filters Toolbar -->
        <div class="p-4 border-b border-stone-200/60 flex flex-col md:flex-row gap-4 bg-stone-50/50">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search reference, order ID, email..." 
                       class="w-full bg-white border-stone-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-sm">
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <select wire:model.live="status" class="bg-white border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm text-stone-700">
                    <option value="all">All Statuses</option>
                    <option value="initiated">Initiated</option>
                    <option value="pending">Pending</option>
                    <option value="authorized">Authorized</option>
                    <option value="captured">Captured</option>
                    <option value="failed">Failed</option>
                    <option value="refunded">Refunded</option>
                </select>

                <select wire:model.live="gateway" class="bg-white border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm text-stone-700">
                    <option value="all">All Gateways</option>
                    <option value="dummy">Dummy</option>
                    <option value="razorpay">Razorpay</option>
                    <option value="cashfree">Cashfree</option>
                </select>

                <input wire:model.live="dateFrom" type="date" placeholder="From Date" class="bg-white border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm text-stone-700">
                <input wire:model.live="dateTo" type="date" placeholder="To Date" class="bg-white border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm text-stone-700">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200/60 text-xs uppercase tracking-wider text-stone-500">
                        <th class="px-6 py-4 font-semibold">Reference</th>
                        <th class="px-6 py-4 font-semibold">User</th>
                        <th class="px-6 py-4 font-semibold">Gateway / Purpose</th>
                        <th class="px-6 py-4 font-semibold">Amount</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Created At</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($transactions as $tx)
                        <tr class="hover:bg-stone-50/50 transition-colors group">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-stone-700">
                                {{ $tx->reference }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-stone-900">{{ $tx->user->name ?? 'Guest' }}</div>
                                <div class="text-xs text-stone-500">{{ $tx->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-stone-100 text-stone-800 border uppercase">
                                        {{ $tx->gateway->value }}
                                    </span>
                                    <span class="text-xs text-stone-500 uppercase">{{ str_replace('_', ' ', $tx->purpose->value) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-stone-900">
                                ₹{{ number_format($tx->amount, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeClass = match($tx->status->value) {
                                        'captured' => 'bg-green-100 text-green-800 border-green-200',
                                        'authorized' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'failed' => 'bg-red-100 text-red-800 border-red-200',
                                        'refunded' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        default => 'bg-stone-100 text-stone-800 border-stone-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border uppercase {{ $badgeClass }}">
                                    {{ $tx->status->value }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-stone-500">
                                {{ $tx->created_at->format('M j, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="viewDetails({{ $tx->id }})" class="text-primary hover:bg-primary/10 px-3 py-1.5 rounded-lg transition-colors text-sm font-medium inline-flex items-center">
                                    <span class="material-symbols-outlined text-[18px] mr-1">visibility</span>
                                    Audit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-stone-400">
                                No transactions found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transactions->hasPages())
            <div class="px-6 py-4 border-t border-stone-200/60 bg-stone-50/30">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- Transaction Detail Audit Modal -->
    @if ($selectedTransaction)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[85vh] overflow-hidden border border-stone-200/50 flex flex-col">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-stone-200 flex justify-between items-center bg-stone-50">
                    <div>
                        <h3 class="text-lg font-serif font-bold text-stone-800">Audit Details - {{ $selectedTransaction->reference }}</h3>
                        <p class="text-xs text-stone-500 mt-0.5">Created at {{ $selectedTransaction->created_at->format('M j, Y H:i:s') }}</p>
                    </div>
                    <button wire:click="closeDetails" class="text-stone-400 hover:text-stone-600 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto space-y-6 flex-1 text-sm text-stone-700">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Left Block: Details -->
                        <div class="space-y-4 md:col-span-2">
                            <h4 class="font-bold text-xs uppercase text-stone-400 tracking-wider">General Information</h4>
                            <div class="grid grid-cols-2 gap-4 bg-stone-50 p-4 rounded-xl border">
                                <div>
                                    <span class="block text-xs text-stone-500">User</span>
                                    <span class="font-medium text-stone-900">{{ $selectedTransaction->user->name ?? 'N/A' }} ({{ $selectedTransaction->user->email ?? 'N/A' }})</span>
                                </div>
                                <div>
                                    <span class="block text-xs text-stone-500">Amount & Currency</span>
                                    <span class="font-medium text-stone-900">₹ {{ number_format($selectedTransaction->amount, 2) }} ({{ $selectedTransaction->currency }})</span>
                                </div>
                                <div>
                                    <span class="block text-xs text-stone-500">Gateway Provider</span>
                                    <span class="font-medium uppercase text-stone-900">{{ $selectedTransaction->gateway->value }}</span>
                                </div>
                                <div>
                                    <span class="block text-xs text-stone-500">Billing Purpose</span>
                                    <span class="font-medium uppercase text-stone-900">{{ str_replace('_', ' ', $selectedTransaction->purpose->value) }}</span>
                                </div>
                            </div>

                            <h4 class="font-bold text-xs uppercase text-stone-400 tracking-wider">Gateway Parameters</h4>
                            <div class="grid grid-cols-2 gap-4 bg-stone-50 p-4 rounded-xl border font-mono text-xs">
                                <div>
                                    <span class="block text-xs text-stone-500 font-sans">Gateway Order ID</span>
                                    <span class="text-stone-950 font-bold">{{ $selectedTransaction->gateway_order_id ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="block text-xs text-stone-500 font-sans">Gateway Payment ID</span>
                                    <span class="text-stone-950 font-bold">{{ $selectedTransaction->gateway_payment_id ?? 'N/A' }}</span>
                                </div>
                            </div>

                            @if($selectedTransaction->failure_reason)
                                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl">
                                    <span class="block text-xs font-bold uppercase text-red-500 tracking-wider">Failure Reason</span>
                                    <p class="text-sm mt-1">{{ $selectedTransaction->failure_reason }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Right Block: Status & Timeline -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-xs uppercase text-stone-400 tracking-wider">Audit Timeline</h4>
                            <div class="relative border-l-2 border-stone-200 pl-4 space-y-4 ml-2">
                                <div class="relative">
                                    <div class="absolute -left-[21px] top-0.5 w-2.5 h-2.5 rounded-full bg-stone-400 border border-white"></div>
                                    <span class="text-xs text-stone-500 block">Initiated</span>
                                    <span class="text-xs font-semibold text-stone-700">{{ $selectedTransaction->created_at->format('M j, H:i') }}</span>
                                </div>
                                @if($selectedTransaction->isAuthorized() || $selectedTransaction->isCaptured())
                                    <div class="relative">
                                        <div class="absolute -left-[21px] top-0.5 w-2.5 h-2.5 rounded-full bg-blue-500 border border-white"></div>
                                        <span class="text-xs text-stone-500 block">Authorized</span>
                                        <span class="text-xs font-semibold text-stone-700">{{ optional($selectedTransaction->updated_at)->format('M j, H:i') }}</span>
                                    </div>
                                @endif
                                @if($selectedTransaction->isCaptured())
                                    <div class="relative">
                                        <div class="absolute -left-[21px] top-0.5 w-2.5 h-2.5 rounded-full bg-green-500 border border-white"></div>
                                        <span class="text-xs text-stone-500 block">Captured</span>
                                        <span class="text-xs font-semibold text-stone-700">{{ optional($selectedTransaction->paid_at)->format('M j, H:i') }}</span>
                                    </div>
                                @endif
                                @if($selectedTransaction->isFailed())
                                    <div class="relative">
                                        <div class="absolute -left-[21px] top-0.5 w-2.5 h-2.5 rounded-full bg-red-500 border border-white"></div>
                                        <span class="text-xs text-stone-500 block">Failed</span>
                                        <span class="text-xs font-semibold text-stone-700">{{ optional($selectedTransaction->failed_at)->format('M j, H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Webhook Ingress Logs Section -->
                    <div class="space-y-4">
                        <h4 class="font-bold text-xs uppercase text-stone-400 tracking-wider">Inbound Webhook Event History</h4>
                        <div class="border rounded-xl divide-y overflow-hidden">
                            @forelse ($webhookLogs as $log)
                                <div class="p-4 bg-stone-50/50 hover:bg-stone-50">
                                    <div class="flex justify-between items-center text-xs">
                                        <div class="font-mono text-stone-600 font-bold">
                                            Event: <span class="text-stone-900 uppercase font-semibold">{{ $log->event_type }}</span> ({{ $log->event_id }})
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($log->processed)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-800 border">PROCESSED</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-800 border">FAILED / IGNORED</span>
                                            @endif
                                            <span class="text-stone-400">{{ $log->created_at->format('M j, Y H:i:s') }}</span>
                                        </div>
                                    </div>
                                    @if($log->failure_reason)
                                        <p class="text-xs text-red-600 mt-2 font-semibold">Error: {{ $log->failure_reason }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="p-4 text-center text-xs text-stone-400">No webhook events logged for this transaction.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-stone-200 flex justify-end bg-stone-50">
                    <button wire:click="closeDetails" class="px-5 py-2 text-sm font-semibold text-stone-700 bg-white border rounded-xl hover:bg-stone-100 transition-all">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
