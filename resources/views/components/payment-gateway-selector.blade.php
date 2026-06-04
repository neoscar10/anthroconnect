@props([
    'gateways' => [],
    'selected' => '',
])

<div {{ $attributes->merge(['class' => 'w-full text-left bg-stone-50 p-4 rounded-xl border border-stone-200/50']) }}>
    <label class="block text-xs uppercase tracking-wider font-bold text-stone-500 mb-3">Select Payment Method</label>
    <div class="space-y-2">
        @foreach ($gateways as $gw)
            <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg hover:bg-stone-100 transition-colors border {{ $selected === $gw['code'] ? 'border-primary bg-primary/5' : 'border-stone-200/40 bg-white' }}">
                <input type="radio" 
                       {{ $attributes->whereStartsWith('wire:model') }} 
                       value="{{ $gw['code'] }}" 
                       class="text-primary focus:ring-primary border-stone-300 mt-1"
                       @if($selected === $gw['code']) checked @endif>
                <div class="flex-1 flex justify-between items-center">
                    <div>
                        <span class="text-sm font-bold text-stone-850 block">
                            {{ $gw['name'] }}
                        </span>
                        @if (!empty($gw['description']))
                            <span class="text-[10px] text-stone-450 block mt-0.5 leading-normal">{{ $gw['description'] }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @if (!empty($gw['logo']) && @file_exists(public_path(parse_url($gw['logo'], PHP_URL_PATH))))
                            <img src="{{ $gw['logo'] }}" alt="{{ $gw['name'] }}" class="h-6 w-auto object-contain opacity-80">
                        @else
                            @if ($gw['code'] === 'razorpay')
                                <span class="material-symbols-outlined text-primary text-xl" style="font-variation-settings: 'FILL' 1;">credit_card</span>
                            @elseif ($gw['code'] === 'cashfree')
                                <span class="material-symbols-outlined text-primary text-xl" style="font-variation-settings: 'FILL' 1;">account_balance_wallet</span>
                            @else
                                <span class="material-symbols-outlined text-stone-400 text-xl" style="font-variation-settings: 'FILL' 1;">payments</span>
                            @endif
                        @endif
                    </div>
                </div>
            </label>
        @endforeach
    </div>
</div>
