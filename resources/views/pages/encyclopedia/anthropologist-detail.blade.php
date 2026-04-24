@extends('layouts.public')

@section('content')
<main class="max-w-7xl mx-auto px-6 py-8 md:py-16">
    <!-- Hero Profile Section -->
    <section class="bg-white dark:bg-stone-900 rounded-3xl shadow-sm border border-primary/10 overflow-hidden mb-16">
        <div class="flex flex-col md:flex-row items-center gap-10 p-8 md:p-14">
            <div class="w-56 h-72 md:w-72 md:h-96 shrink-0 rounded-2xl overflow-hidden shadow-2xl border-4 border-stone-50 dark:border-stone-800">
                @if($anthropologist->profile_image)
                    <img alt="{{ $anthropologist->full_name }}" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700" src="{{ Storage::url($anthropologist->profile_image) }}"/>
                @else
                    <div class="w-full h-full bg-stone-100 dark:bg-stone-800 flex items-center justify-center">
                        <span class="material-symbols-outlined text-7xl text-stone-300">person</span>
                    </div>
                @endif
            </div>
            <div class="flex-1 space-y-6 text-center md:text-left">
                <div>
                    <span class="text-primary font-extrabold tracking-widest text-[10px] uppercase bg-primary/5 px-3 py-1 rounded-full">Distinguished Thinker</span>
                    <h2 class="text-4xl md:text-6xl font-headline font-bold mt-3 text-stone-900 dark:text-stone-100 italic">
                        {{ $anthropologist->full_name }}
                    </h2>
                    <p class="text-stone-500 dark:text-stone-400 mt-4 font-bold tracking-tight">
                        @php
                            $lifespan = ($anthropologist->birth_year && $anthropologist->death_year) 
                                ? "{$anthropologist->birth_year}–{$anthropologist->death_year}" 
                                : ($anthropologist->birth_year ? "Born {$anthropologist->birth_year}" : "");
                            
                            $infoParts = array_filter([
                                $lifespan,
                                $anthropologist->discipline_or_specialization,
                                $anthropologist->nationality
                            ]);
                        @endphp
                        {{ implode(' | ', $infoParts) }}
                    </p>
                </div>
                <p class="text-lg text-stone-600 dark:text-stone-300 leading-relaxed max-w-3xl font-medium">
                    {{ $anthropologist->summary }}
                </p>
                
                @if($anthropologist->topics->isNotEmpty())
                <div class="flex flex-wrap justify-center md:justify-start gap-2 pt-2">
                    @foreach($anthropologist->topics as $topic)
                        <span class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-bold rounded-full uppercase tracking-tighter">
                            {{ $topic->name }}
                        </span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
        <!-- Main Content Column -->
        <div class="lg:col-span-8 space-y-20">
            
            <!-- Biography -->
            <section>
                <div class="flex items-center gap-4 mb-10 border-b border-stone-100 dark:border-stone-800 pb-6">
                    <span class="material-symbols-outlined text-primary text-3xl">history_edu</span>
                    <h3 class="text-3xl font-headline font-bold italic text-stone-900 dark:text-stone-100">Biography</h3>
                </div>
                <div class="prose-content text-lg text-stone-700 dark:text-stone-300 leading-relaxed font-serif-body">
                    {!! \Illuminate\Support\Str::markdown($anthropologist->biography_markdown ?? '') !!}
                </div>
            </section>

            <!-- Major Contributions -->
            @if($contributions->isNotEmpty())
            <section>
                <div class="flex items-center gap-4 mb-10 border-b border-stone-100 dark:border-stone-800 pb-6">
                    <span class="material-symbols-outlined text-primary text-3xl">account_tree</span>
                    <h3 class="text-3xl font-headline font-bold italic text-stone-900 dark:text-stone-100">Major Contributions</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($contributions as $contribution)
                    <div class="p-8 bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 hover:border-primary/30 hover:shadow-xl transition-all group">
                        <h4 class="font-headline font-bold text-xl text-primary mb-4 italic group-hover:scale-105 transition-transform origin-left">
                            {{ $contribution['title'] }}
                        </h4>
                        <p class="text-sm text-stone-600 dark:text-stone-400 leading-relaxed">
                            {{ $contribution['description'] }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Key Theory Block -->
            @if($keyTheory)
            <section class="bg-primary/5 p-10 rounded-[40px] border border-primary/10 relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 opacity-5 group-hover:opacity-10 transition-opacity">
                    <span class="material-symbols-outlined text-[200px] text-primary">psychology</span>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-primary text-4xl">psychology</span>
                            <h3 class="text-3xl font-headline font-bold text-stone-900 dark:text-stone-100 italic">Key Theory: {{ $keyTheory->title }}</h3>
                        </div>
                        <a class="text-primary font-bold text-xs uppercase tracking-widest flex items-center gap-2 hover:underline" href="{{ route('encyclopedia.theories.show', $keyTheory->slug) }}">
                            Explore Theory <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <p class="text-stone-700 dark:text-stone-300 text-lg leading-relaxed mb-8 max-w-3xl">
                        {{ $keyTheory->short_description }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                         <span class="bg-white dark:bg-stone-800 px-4 py-2 rounded-xl text-xs font-bold border border-primary/20 shadow-sm">
                            Fundamental Framework
                         </span>
                    </div>
                </div>
            </section>
            @endif

        </div>

        <!-- Sidebar Column -->
        <div class="lg:col-span-4 space-y-16">
            
            <!-- Related Concepts -->
            @if($anthropologist->coreConcepts->isNotEmpty())
            <section>
                <h3 class="font-bold text-[10px] uppercase tracking-[0.2em] mb-6 text-stone-400 dark:text-stone-500">Related Concepts</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($anthropologist->coreConcepts as $concept)
                        <a href="{{ route('encyclopedia.concepts.show', $concept->slug) }}" class="px-4 py-3 bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl text-sm font-bold text-stone-700 dark:text-stone-300 shadow-sm hover:border-primary/50 transition-colors">
                            {{ $concept->title }}
                        </a>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Influence & Legacy -->
            <section class="relative">
                <h3 class="font-bold text-[10px] uppercase tracking-[0.2em] mb-6 text-stone-400">Influence & Legacy</h3>
                <div class="text-lg text-stone-600 dark:text-stone-400 leading-relaxed italic border-l-4 border-primary/30 pl-8 py-4 bg-primary/5 rounded-r-2xl">
                    "This thinker reshaped the foundations of modern anthropology, challenging us to rethink our understanding of human culture and society."
                </div>
            </section>

            <!-- Related Thinkers -->
            @if($relatedThinkers->isNotEmpty())
            <section>
                <h3 class="font-bold text-[10px] uppercase tracking-[0.2em] mb-6 text-stone-400">Related Thinkers</h3>
                <div class="space-y-4">
                    @foreach($relatedThinkers as $thinker)
                    <a href="{{ route('encyclopedia.anthropologists.show', $thinker->slug) }}" class="flex items-center gap-4 p-4 rounded-2xl bg-white dark:bg-stone-900 border border-transparent hover:border-primary/20 hover:bg-primary/5 transition-all group">
                        <div class="w-14 h-14 rounded-full bg-orange-100 dark:bg-stone-800 overflow-hidden shrink-0 border border-stone-100 dark:border-stone-700 grayscale group-hover:grayscale-0 transition-all">
                            @if($thinker->profile_image)
                                <img src="{{ Storage::url($thinker->profile_image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center opacity-20">
                                    <span class="material-symbols-outlined">person</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-bold text-stone-900 dark:text-stone-100 group-hover:text-primary transition-colors line-clamp-1">{{ $thinker->full_name }}</h4>
                            <p class="text-[10px] text-stone-500 font-bold uppercase tracking-widest">{{ $thinker->discipline_or_specialization ?: 'Anthropologist' }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Community CTA placeholder -->
            <section class="bg-stone-900 text-white p-8 rounded-[32px] shadow-2xl relative overflow-hidden group">
                <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-primary">forum</span>
                        <h3 class="font-headline font-bold text-xl italic">Join the Discussion</h3>
                    </div>
                    <p class="text-xs text-stone-400 leading-relaxed mb-6">
                        Share your thoughts on {{ $anthropologist->full_name }}'s legacy with our global community of anthropology students and scholars.
                    </p>
                    <button class="w-full py-4 bg-primary hover:bg-orange-800 transition-colors rounded-xl text-[10px] font-extrabold uppercase tracking-widest">
                        Access Community Hub
                    </button>
                </div>
            </section>

        </div>
    </div>
</main>

<style>
    .font-serif-body {
        font-family: 'Lora', serif;
    }
    .prose-content p {
        margin-bottom: 2rem;
    }
    .prose-content h2, .prose-content h3 {
        font-family: 'Lora', serif;
        font-weight: 700;
        color: #9e5015;
        margin-top: 3rem;
        margin-bottom: 1.5rem;
    }
    .prose-content ul, .prose-content ol {
        margin-left: 2rem;
        margin-bottom: 2rem;
    }
    .prose-content ul {
        list-style: disc;
    }
    .prose-content ol {
        list-style: decimal;
    }
    .prose-content blockquote {
        border-left: 4px solid #9e5015;
        padding-left: 2rem;
        font-style: italic;
        margin: 3rem 0;
        color: #57534e;
    }
</style>
@endsection
