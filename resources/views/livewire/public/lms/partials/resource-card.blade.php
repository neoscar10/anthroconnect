@php 
    $isLocked = !$resource->canAccess(Auth::user());
@endphp
<div wire:click="downloadResource({{ $resource->id }})" class="group flex items-start gap-4 p-4 rounded-2xl bg-stone-50 border border-stone-100 hover:bg-white hover:border-primary/20 hover:shadow-lg transition-all duration-300 cursor-pointer">
    <div class="w-10 h-10 rounded-xl bg-orange-50 text-primary flex items-center justify-center shrink-0">
        <span class="material-symbols-outlined text-sm">{{ $isLocked ? 'lock' : 'picture_as_pdf' }}</span>
    </div>
    <div class="flex-1 min-w-0">
        <h5 class="text-sm font-bold text-stone-900 group-hover:text-primary transition-colors leading-tight italic mb-1 truncate">{{ $resource->title }}</h5>
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-[9px] text-stone-400 uppercase font-bold tracking-widest">PDF Archive</span>
            @if($isLocked)
                <span class="text-[8px] bg-primary text-white px-1.5 py-0.5 rounded-full font-bold uppercase tracking-widest">Members Only</span>
            @endif
        </div>
    </div>
</div>
