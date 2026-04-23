@php
    $isInline = isset($compact) && $compact;
@endphp

<div class="{{ $isInline ? 'bg-stone-50/50 rounded-2xl p-4 sm:p-6 border border-stone-100 shadow-inner' : 'bg-stone-50 rounded-[40px] p-8 sm:p-12 border border-stone-200' }} relative overflow-hidden transition-all duration-300">
    @if(!$isInline)
        <div class="absolute top-0 right-0 p-8 opacity-5">
            <span class="material-symbols-outlined text-8xl text-stone-900">edit_note</span>
        </div>
    @endif
    
    <div class="relative z-10 space-y-6">
        @if(!$isInline)
            <div class="space-y-2">
                <h3 class="text-2xl font-headline font-bold text-stone-900 italic">Join the Conversation</h3>
                <p class="text-sm text-stone-500">Contribute your analysis or peer review to this intellectual inquiry.</p>
            </div>
        @endif

        @if($replyingTo && $replyingTo !== 'root' && !$isInline)
           <div class="flex items-center justify-between bg-white px-4 py-2 rounded-xl border border-primary/20">
               <div class="flex items-center gap-2">
                   <span class="material-symbols-outlined text-primary text-sm">reply</span>
                   <span class="text-[10px] font-bold text-stone-600 uppercase tracking-widest">Replying to Scholar Contribution</span>
               </div>
               <button wire:click="$set('replyingTo', null)" class="text-stone-400 hover:text-red-500 transition-colors"><span class="material-symbols-outlined text-sm">close</span></button>
           </div>
        @endif

        @if (session()->has('message') && ($replyingTo == session('last_reply_to')))
            <div class="bg-green-50 text-green-800 p-4 rounded-xl text-sm font-medium border border-green-200 shadow-sm animate-bounce">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="submitReply" class="space-y-4">
            <div class="relative">
                <textarea 
                    wire:model="replyBody"
                    rows="{{ $isInline ? '3' : '6' }}" 
                    placeholder="{{ $placeholder ?? 'Enter your contribution here...' }}" 
                    class="w-full bg-white border-stone-200 {{ $isInline ? 'rounded-2xl p-4' : 'rounded-[32px] p-8' }} text-sm leading-relaxed text-stone-700 focus:ring-primary focus:border-primary placeholder:text-stone-300 shadow-sm transition-all"
                ></textarea>
                @error('replyBody') <span class="text-red-500 text-[10px] font-bold mt-2 block uppercase tracking-tight">{{ $message }}</span> @enderror
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="{{ $isInline ? 'w-8 h-8' : 'w-10 h-10' }} rounded-full bg-orange-100 flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined {{ $isInline ? 'text-lg' : 'text-xl' }}">shield_person</span>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-stone-900 uppercase tracking-tight leading-none">Verified Scholar</p>
                        <p class="text-[8px] text-stone-500 italic mt-1 leading-none">{{ Auth::user()?->name ?? 'Guest Scholar' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    @if($isInline)
                        <button type="button" wire:click="$set('replyingTo', null)" class="px-6 py-3 text-[10px] font-bold text-stone-400 hover:text-stone-900 uppercase tracking-widest transition-colors">Cancel</button>
                    @endif
                    <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-orange-800 text-white font-bold {{ $isInline ? 'py-3 px-8 text-xs rounded-xl' : 'py-4 px-12 rounded-2xl' }} shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center gap-2">
                        <span>{{ $isInline ? 'Reply' : 'Publish Contribution' }}</span>
                        <span class="material-symbols-outlined {{ $isInline ? 'text-sm' : '' }}">send</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
