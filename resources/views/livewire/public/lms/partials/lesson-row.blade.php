@php 
    $isCompleted = in_array($lesson->id, $completedLessonIds);
    $isLocked = !$lesson->canAccess(Auth::user());
@endphp
<div wire:click="openLesson('{{ $lesson->slug }}')" 
    class="group p-4 md:p-6 bg-stone-50 rounded-2xl border border-stone-100 flex flex-col md:flex-row md:items-center gap-4 md:gap-6 cursor-pointer hover:bg-white hover:border-primary/20 hover:shadow-lg transition-all duration-300">
    <div class="flex items-center gap-4 md:gap-6 flex-1 min-w-0">
        <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-white border border-stone-200 flex items-center justify-center shrink-0 font-headline font-bold italic text-base md:text-lg {{ $isCompleted ? 'bg-green-500 border-green-500 text-white' : 'text-stone-300 group-hover:text-primary transition-colors' }}">
            @if($isCompleted)
                <span class="material-symbols-outlined text-sm">check</span>
            @else
                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 md:gap-3 mb-1">
                <h4 class="font-headline text-lg md:text-xl font-bold italic truncate transition-colors {{ $isCompleted ? 'text-green-700' : 'text-stone-900 group-hover:text-primary' }}">{{ $lesson->title }}</h4>
                @if($lesson->is_preview)
                    <span class="bg-primary/10 text-primary px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest">Preview</span>
                @endif
            </div>
            <p class="text-xs text-stone-500 line-clamp-1 italic">{{ $lesson->short_description }}</p>
        </div>
    </div>

    <div class="flex items-center justify-between md:justify-end gap-6 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-stone-100">
        <div class="flex flex-col items-start md:items-end">
            <span class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-stone-400">{{ $lesson->video_source_type == 'upload' ? 'Video Lecture' : 'External Seminar' }}</span>
            <span class="text-[8px] md:text-[9px] text-stone-400 italic">{{ $lesson->duration_minutes ?? '0' }}m duration</span>
        </div>
        @if($isLocked)
            <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-stone-200 flex items-center justify-center shadow-inner group-hover:bg-primary group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-xs md:text-sm" style="font-variation-settings: 'FILL' 1;">lock</span>
            </div>
        @elseif($isCompleted)
            <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-green-100 border border-green-200 flex items-center justify-center text-green-600">
                <span class="material-symbols-outlined text-xs md:text-sm">verified</span>
            </div>
        @else
            <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-white border border-stone-200 flex items-center justify-center group-hover:border-primary group-hover:bg-primary group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-xs md:text-sm">play_arrow</span>
            </div>
        @endif
    </div>
</div>
