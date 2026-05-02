<div class="group relative rounded-[2rem] border border-outline-variant/10 bg-surface-container-low p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:border-primary/20 flex flex-col justify-between h-full" data-id="{{ $class->id }}" wire:key="class-{{ $class->id }}">
    <div>
        <div class="flex items-start justify-between gap-4 mb-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-secondary/10 text-secondary">
                <span class="material-symbols-outlined text-2xl">folder</span>
            </div>

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="p-2 text-stone-400 hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">more_vert</span>
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-surface-container-lowest rounded-2xl shadow-2xl border border-outline-variant/10 z-20 overflow-hidden py-2" x-cloak>
                    <button wire:click="openClassModal({{ $class->id }})" class="w-full px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-stone-600 hover:bg-stone-50 flex items-center gap-3">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        Rename Class
                    </button>
                    <button wire:confirm="Are you sure? This will detach all lessons and resources in this class." wire:click="deleteClass({{ $class->id }})" class="w-full px-5 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-error hover:bg-error/5 flex items-center gap-3">
                        <span class="material-symbols-outlined text-sm">delete</span>
                        Delete Class
                    </button>
                </div>
            </div>
        </div>

        <div>
            <h3 class="font-headline text-xl font-bold italic text-on-surface leading-tight">{{ $class->title }}</h3>
            <p class="mt-1.5 line-clamp-2 text-[11px] text-on-surface-variant font-medium leading-relaxed">{{ $class->description ?: 'No description added for this class folder.' }}</p>
        </div>
    </div>

    <div class="mt-6">
        <div class="flex flex-wrap items-center gap-2 text-[9px] font-bold uppercase tracking-widest text-stone-400">
            <span class="rounded-full bg-surface-container-lowest px-4 py-2 border border-outline-variant/10 flex items-center gap-1">
                <span class="material-symbols-outlined text-[12px]">play_circle</span>
                {{ $class->lessons_count }} Videos
            </span>
            <span class="rounded-full bg-surface-container-lowest px-4 py-2 border border-outline-variant/10 flex items-center gap-1">
                <span class="material-symbols-outlined text-[12px]">description</span>
                {{ $class->resources_count }} PDFs
            </span>
            <span class="rounded-full bg-surface-container-lowest px-4 py-2 border border-outline-variant/10 flex items-center gap-1">
                <span class="material-symbols-outlined text-[12px]">quiz</span>
                {{ $class->mcq_questions_count }} MCQs
            </span>
            @if($class->is_published)
                <span class="rounded-full bg-primary/10 px-4 py-2 text-primary">Published</span>
            @else
                <span class="rounded-full bg-stone-100 px-4 py-2">Draft</span>
            @endif
        </div>

        <button wire:click="openClass({{ $class->id }})" class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-primary px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-on-primary shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 active:translate-y-0">
            Open Folder
        </button>
    </div>

    <div class="drag-handle absolute top-8 left-8 opacity-0 group-hover:opacity-100 transition-opacity cursor-move bg-surface-container-lowest p-1 rounded-lg border border-outline-variant/10 text-stone-300 hover:text-primary">
        <span class="material-symbols-outlined text-[16px]">drag_indicator</span>
    </div>
</div>
