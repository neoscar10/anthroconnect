<div 
    wire:key="membership-upgrade-modal"
    x-data="{ 
        show: @entangle('show').live,
        loading: false,
        errorMessage: @entangle('paymentSuccess').live ? null : null,
        init() {
            window.addEventListener('start-payment-checkout', async event => {
                const options = event.detail[0];
                const gateway = options.gateway;
                this.loading = true;
                this.errorMessage = null;

                try {
                    await window.PaymentGatewayManager.launchCheckout(gateway, options, {
                        onSuccess: (payload) => {
                            this.verifyPayment(payload, options.reference);
                        },
                        onDismiss: () => {
                            this.loading = false;
                        },
                        onFailure: (errorMsg) => {
                            this.loading = false;
                            this.errorMessage = errorMsg;
                        }
                    });
                } catch (err) {
                    this.loading = false;
                    this.errorMessage = 'Checkout initialization failed.';
                }
            });
        },
        async verifyPayment(payload, reference) {
            this.loading = true;
            this.errorMessage = null;

            try {
                const csrfMeta = document.querySelector('meta[name=csrf-token]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                const res = await fetch('/payments/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(Object.assign({}, payload, {
                        transaction_reference: reference
                    }))
                });

                const data = await res.json();
                
                if (data.success) {
                    await $wire.handlePaymentVerified(reference);
                    this.loading = false;
                } else {
                    this.loading = false;
                    this.errorMessage = data.message || 'Payment verification failed.';
                }
            } catch (err) {
                this.loading = false;
                this.errorMessage = 'Network error during verification.';
            }
        }
    }"
    x-show="show"
    x-on:open-upgrade-modal.window="$wire.open()"
    x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
>
    <!-- Script Injection -->
    <script src="{{ asset('js/payment-gateway-manager.js') }}" defer></script>

    <!-- Overlay -->
    <div 
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="show = false"
        style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(12px);"
        class="fixed inset-0"
    ></div>

    <!-- Modal Container -->
    <div 
        x-show="show"
        x-transition:enter="ease-out duration-300 transform"
        x-transition:enter-start="scale-95 opacity-0 translate-y-8"
        x-transition:enter-end="scale-100 opacity-100 translate-y-0"
        x-transition:leave="ease-in duration-200 transform"
        x-transition:leave-start="scale-100 opacity-100 translate-y-0"
        x-transition:leave-end="scale-95 opacity-0 translate-y-8"
        class="bg-white rounded-3xl shadow-2xl ring-1 ring-black/5 w-full max-w-4xl max-h-[90vh] overflow-hidden relative z-10 flex flex-col md:flex-row"
    >
        @if($paymentSuccess)
            <!-- Success State -->
            <div class="flex-1 p-12 flex flex-col items-center justify-center text-center animate-in fade-in zoom-in duration-500">
                <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mb-8">
                    <span class="material-symbols-outlined text-6xl text-primary" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                </div>
                <h2 class="text-4xl font-headline italic font-bold text-stone-900 mb-4">Welcome to the Inner Circle!</h2>
                <p class="text-lg text-stone-600 max-w-md mb-8">
                    Your membership has been activated successfully. You now have full access to the AnthroConnect platform.
                </p>
                <div class="bg-stone-100 p-4 rounded-xl mb-10 text-xs font-mono text-primary border border-primary/10">
                    Transaction Ref: {{ $paymentReference }}
                </div>
                <button 
                    @click="show = false"
                    class="bg-primary text-white px-12 py-4 rounded-xl font-bold uppercase tracking-widest hover:opacity-90 transition-opacity"
                >
                    Start Exploring
                </button>
            </div>
        @else
            <!-- Left Side: Summary -->
            <div class="w-full md:w-5/12 bg-primary p-8 sm:p-12 text-white flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -right-20 -bottom-20 opacity-10">
                    <span class="material-symbols-outlined text-[300px] rotate-12">workspace_premium</span>
                </div>

                <div class="relative z-10">
                    <div class="mb-10">
                        <h3 class="text-white/60 font-medium text-[10px] uppercase font-bold tracking-[0.2em] mb-4">Checkout Summary</h3>
                        <h2 class="text-3xl font-headline italic font-bold leading-tight">{{ $globalSetting->title ?? 'AnthroConnect Membership' }}</h2>
                    </div>

                    <div class="space-y-6 mb-12">
                        <p class="text-sm opacity-80 leading-relaxed">{{ $globalSetting->description ?? 'Unlock the full potential of anthropological research.' }}</p>

                        @if($globalSetting)
                            <div class="space-y-3">
                                @foreach($globalSetting->privileges as $privilege)
                                    <div class="flex items-center gap-3 text-xs">
                                        <span class="material-symbols-outlined text-white/60 text-sm">verified</span>
                                        {{ $privilege->privilege }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="relative z-10 pt-8 border-t border-white/10">
                    <div class="flex justify-between items-end">
                        <span class="text-xs uppercase tracking-widest opacity-60">Total to Pay</span>
                        <span class="text-4xl font-headline italic font-bold">₹ {{ number_format($globalSetting->price_inr ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Action Trigger -->
            <div class="w-full md:w-7/12 p-8 sm:p-12 pb-8 flex flex-col bg-white justify-between overflow-hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-stone-900">Secure Checkout</h3>
                    <button 
                        @click="show = false" 
                        :disabled="loading"
                        class="text-stone-400 hover:text-stone-900 transition-colors disabled:opacity-50"
                    >
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto flex flex-col justify-start items-center text-center px-4 py-4 scrollbar-thin">
                    <div class="w-12 h-12 bg-stone-50 rounded-full flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-3xl text-stone-400">shield_with_heart</span>
                    </div>
                    <h4 class="text-md font-bold text-stone-900 mb-1">100% Safe & Secure Payment</h4>
                    <p class="text-xs text-stone-500 max-w-sm mb-5">
                        We partner with industry leaders to process your payments securely. No credit card information is collected or stored on our servers.
                    </p>

                        @if (count($gatewaysData) > 1)
                            <div class="mb-4 w-full">
                                <x-payment-gateway-selector :gateways="$gatewaysData" wire:model.live="selectedGateway" :selected="$selectedGateway" />
                            </div>
                        @endif

                        <button 
                            @click="$wire.processPurchase()"
                            :disabled="loading"
                            class="w-full bg-primary text-white py-4 rounded-2xl font-bold uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center gap-3 disabled:opacity-50 disabled:translate-y-0"
                        >
                            <span x-show="!loading">
                                @if (count($gatewaysData) === 1)
                                    Pay with {{ $gatewaysData[0]['name'] }}
                                @else
                                    Upgrade to Premium
                                @endif
                            </span>
                            <span x-show="loading" class="flex items-center gap-2 whitespace-nowrap">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Please wait...
                            </span>
                        </button>
                    </div>

                <div class="pt-6 border-t border-stone-100 flex items-center justify-center gap-6 text-[10px] text-stone-400 font-medium">
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-xs">lock</span> PCI-DSS Compliant</span>
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-xs">verified</span> SSL Encrypted</span>
                </div>
            </div>
        @endif
    </div>
</div>
