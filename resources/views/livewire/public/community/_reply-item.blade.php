<div class="group relative bg-white rounded-3xl p-6 border border-stone-100 shadow-sm transition-all {{ $reply->depth > 0 ? 'ml-6 sm:ml-12 mt-4' : '' }}">
    <div class="flex items-start gap-4">
        <!-- Scholar Info -->
        <div class="shrink-0">
            <div class="h-10 w-10 rounded-full overflow-hidden border border-stone-200">
                <img src="{{ $reply->author?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($reply->author?->name ?? 'Scholar') }}" class="w-full h-full object-cover">
            </div>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <span class="text-[11px] font-bold text-stone-900 uppercase tracking-widest">{{ $reply->author?->name }}</span>
                    <span class="text-[10px] text-stone-400">•</span>
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">{{ $reply->published_at->diffForHumans() }}</span>
                    @if($reply->is_expert_reply)
                        <span class="px-2 py-0.5 bg-primary/10 text-primary text-[8px] font-bold uppercase tracking-widest rounded-full border border-primary/20">Expert</span>
                    @endif
                </div>
                
                <div class="flex items-center gap-2">
                    <button wire:click="vote('reply', {{ $reply->id }}, 1)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border {{ $reply->votes()->where('user_id', Auth::id())->where('vote', 1)->exists() ? 'bg-primary text-white border-primary shadow-lg shadow-primary/20' : 'bg-stone-50 text-stone-400 border-stone-100 hover:text-stone-900 transition-colors' }}">
                        <span class="material-symbols-outlined text-[10px]" style="font-variation-settings: 'FILL' 1;">thumb_up</span>
                        <span class="text-[10px] font-bold">{{ $reply->upvotes_count }}</span>
                    </button>
                    @if($reply->depth < 2)
                        <button wire:click="setReplyingTo({{ $reply->id }})" class="p-1 px-3 text-[10px] font-bold text-stone-400 hover:text-primary uppercase tracking-widest transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">reply</span>
                            Reply
                        </button>
                    @endif
                </div>
            </div>

            <div class="text-sm text-stone-700 leading-relaxed font-body">
                {!! nl2br(e($reply->body)) !!}
            </div>

            <!-- Inline Reply Composer -->
            @if($replyingTo == $reply->id)
                <div class="mt-6" x-data x-transition.origin.top>
                    @include('livewire.public.community._discussion-composer', [
                        'replyingTo' => $reply->id, 
                        'compact' => true, 
                        'placeholder' => 'Add your peer review to this contribution...'
                    ])
                </div>
            @endif
        </div>
    </div>

    <!-- Recursive Children -->
    @if($reply->children->isNotEmpty())
        <div class="mt-4 space-y-4">
            @foreach($reply->children as $child)
                @include('livewire.public.community._reply-item', ['reply' => $child])
            @endforeach
        </div>
    @endif
</div>
