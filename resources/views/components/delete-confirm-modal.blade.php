<div x-data="{ 
        open: false, 
        title: '', 
        message: '', 
        action: null,
        confirming: false,
        
        handleOpen(event) {
            this.title = event.detail.title || 'Are you sure?';
            this.message = event.detail.message || 'This action cannot be undone.';
            this.action = event.detail.action;
            this.open = true;
            this.confirming = false;
        },
        
        async confirm() {
            this.confirming = true;
            
            if (!this.action) return;
            
            if (this.action.type === 'form') {
                const form = document.getElementById(this.action.target);
                if (form) {
                    form.submit();
                }
            } else if (this.action.type === 'livewire') {
                const component = Livewire.find(this.action.component);
                if (component) {
                    await component.call(this.action.method, ...(this.action.params || []));
                }
                this.open = false;
            }
        }
    }" 
    @open-delete-modal.window="handleOpen"
    x-show="open" 
    class="fixed inset-0 z-[1000] flex items-center justify-center p-4 sm:p-6" 
    x-cloak>
    
    <!-- Overlay -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Panel -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300 transform"
         x-transition:enter-start="scale-95 opacity-0 translate-y-4"
         x-transition:enter-end="scale-100 opacity-100 translate-y-0"
         x-transition:leave="ease-in duration-200 transform"
         x-transition:leave-start="scale-100 opacity-100 translate-y-0"
         x-transition:leave-end="scale-95 opacity-0 translate-y-4"
         class="bg-surface-container-lowest rounded-[32px] shadow-2xl ring-1 ring-white/10 w-full max-w-md overflow-hidden relative z-10 border border-error/10">
        
        <div class="p-8 text-center space-y-6">
            <!-- Warning Icon -->
            <div class="w-20 h-20 bg-error/10 rounded-3xl flex items-center justify-center mx-auto mb-4 animate-bounce">
                <span class="material-symbols-outlined text-4xl text-error">warning</span>
            </div>
            
            <div class="space-y-2">
                <h3 class="font-headline text-3xl text-on-surface italic font-bold" x-text="title"></h3>
                <p class="text-[10px] uppercase font-bold text-stone-400 tracking-widest">Irreversible Archivist Action</p>
            </div>
            
            <p class="text-sm text-stone-500 leading-relaxed max-w-xs mx-auto" x-text="message"></p>
            
            <div class="flex flex-col gap-3 pt-4">
                <button @click="confirm()" 
                        :disabled="confirming"
                        class="w-full bg-error text-white py-4 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-error/20 hover:bg-error/90 transition-all flex items-center justify-center gap-2">
                    <template x-if="confirming">
                        <span class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    </template>
                    <span x-text="confirming ? 'Processing...' : 'Yes, Proceed with Deletion'"></span>
                </button>
                
                <button @click="open = false" 
                        :disabled="confirming"
                        class="w-full py-4 rounded-2xl text-xs font-bold uppercase tracking-widest text-stone-400 hover:bg-stone-50 transition-all">
                    Discard Action
                </button>
            </div>
        </div>
    </div>
</div>
