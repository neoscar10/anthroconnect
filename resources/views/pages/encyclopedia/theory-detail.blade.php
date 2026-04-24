@extends('layouts.public')

@section('content')
<main class="max-w-7xl mx-auto px-6 py-8 md:py-16">
    <!-- Hero Header -->
    <section class="bg-white dark:bg-stone-900 rounded-[40px] shadow-sm border border-primary/10 overflow-hidden mb-16">
        <div class="flex flex-col md:flex-row items-center gap-12 p-8 md:p-16">
            <div class="w-full md:w-1/3 shrink-0 rounded-3xl overflow-hidden aspect-video md:aspect-square bg-primary/5 flex items-center justify-center relative group">
                <span class="material-symbols-outlined text-[120px] text-primary/20 group-hover:scale-110 transition-transform duration-700">psychology</span>
                <div class="absolute inset-0 bg-gradient-to-t from-primary/10 to-transparent"></div>
            </div>
            <div class="flex-1 space-y-6 text-center md:text-left">
                <div>
                    <span class="text-primary font-extrabold tracking-widest text-[10px] uppercase bg-primary/5 px-3 py-1 rounded-full">Major Theoretical Framework</span>
                    <h2 class="text-4xl md:text-6xl font-headline font-bold mt-4 text-stone-900 dark:text-stone-100 italic leading-tight">
                        {{ $theory->title }}
                    </h2>
                </div>
                <p class="text-xl text-stone-600 dark:text-stone-300 leading-relaxed max-w-2xl font-medium italic">
                    "{{ $theory->short_description }}"
                </p>
                
                <div class="flex flex-wrap justify-center md:justify-start gap-4 pt-4">
                    <div class="flex items-center gap-2 px-4 py-2 bg-stone-50 dark:bg-stone-800 rounded-xl border border-stone-100 dark:border-stone-700">
                        <span class="material-symbols-outlined text-primary text-sm">history_edu</span>
                        <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Theoretical Origin</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-20">
            
            <!-- Detailed Analysis -->
            <section>
                <div class="flex items-center gap-4 mb-10 border-b border-stone-100 dark:border-stone-800 pb-6">
                    <span class="material-symbols-outlined text-primary text-3xl">menu_book</span>
                    <h3 class="text-3xl font-headline font-bold italic text-stone-900 dark:text-stone-100">Theoretical Analysis</h3>
                </div>
                <div class="prose-content text-lg text-stone-700 dark:text-stone-300 leading-relaxed font-serif-body">
                    {!! \Illuminate\Support\Str::markdown($theory->body_markdown ?? '') !!}
                </div>
            </section>

            <!-- Key Thinkers -->
            @if($theory->key_thinkers_text)
            <section class="bg-primary/5 p-12 rounded-[48px] border border-primary/10 relative overflow-hidden group">
                <div class="absolute -right-20 -bottom-20 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity">
                    <span class="material-symbols-outlined text-[300px] text-primary">groups</span>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="size-14 bg-primary/10 rounded-2xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-3xl">groups</span>
                        </div>
                        <h3 class="text-3xl font-headline font-bold text-stone-900 dark:text-stone-100 italic">Key Proponents & Thinkers</h3>
                    </div>
                    <p class="text-stone-700 dark:text-stone-300 text-xl leading-relaxed italic font-serif">
                        {{ $theory->key_thinkers_text }}
                    </p>
                </div>
            </section>
            @endif

        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-16">
            
            <!-- Quick Reference -->
            <section class="p-8 bg-white dark:bg-stone-900 rounded-[32px] border border-stone-200 dark:border-stone-800 shadow-sm">
                <h3 class="font-bold text-[10px] uppercase tracking-[0.2em] mb-8 text-stone-400">Quick Reference</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-primary mt-1">label</span>
                        <div>
                            <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-1">Category</p>
                            <p class="text-sm font-bold text-stone-900 dark:text-stone-100">Theoretical Framework</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-primary mt-1">verified_user</span>
                        <div>
                            <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-1">Status</p>
                            <p class="text-sm font-bold text-stone-900 dark:text-stone-100">Academic Standard</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Related Theories -->
            @if($relatedTheories->isNotEmpty())
            <section>
                <h3 class="font-bold text-[10px] uppercase tracking-[0.2em] mb-8 text-stone-400">Related Frameworks</h3>
                <div class="space-y-4">
                    @foreach($relatedTheories as $related)
                    <a href="{{ route('encyclopedia.theories.show', $related->slug) }}" class="block p-6 rounded-2xl bg-white dark:bg-stone-900 border border-stone-100 dark:border-stone-800 hover:border-primary/30 hover:bg-primary/5 transition-all group">
                        <h4 class="font-bold text-stone-900 dark:text-stone-100 group-hover:text-primary transition-colors mb-2">{{ $related->title }}</h4>
                        <p class="text-xs text-stone-500 line-clamp-2 leading-relaxed">{{ $related->short_description }}</p>
                    </a>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Community CTA -->
            <section class="bg-stone-900 text-white p-10 rounded-[40px] shadow-2xl relative overflow-hidden group">
                <div class="absolute inset-0 opacity-10 ethno-pattern"></div>
                <div class="relative z-10">
                    <h3 class="font-headline font-bold text-2xl italic mb-4">Deepen the Discourse</h3>
                    <p class="text-sm text-stone-400 leading-relaxed mb-8">
                        Connect with fellow scholars to discuss the contemporary applications of {{ $theory->title }} in modern fieldwork.
                    </p>
                    <a href="{{ route('community.index') }}" class="block w-full py-4 bg-primary hover:bg-orange-800 transition-colors rounded-xl text-center text-[10px] font-extrabold uppercase tracking-widest">
                        Join Discussion
                    </a>
                </div>
            </section>

        </div>
    </div>
</main>

<style>
    .font-serif-body { font-family: 'Lora', serif; }
    .prose-content p { margin-bottom: 2rem; }
    .prose-content h2, .prose-content h3 { font-family: 'Lora', serif; font-weight: 700; color: #9e5015; margin-top: 3rem; margin-bottom: 1.5rem; }
    .prose-content ul, .prose-content ol { margin-left: 2rem; margin-bottom: 2rem; }
    .prose-content ul { list-style: disc; }
    .prose-content ol { list-style: decimal; }
    .prose-content blockquote { border-left: 4px solid #9e5015; padding-left: 2rem; font-style: italic; margin: 3rem 0; color: #57534e; }
</style>
@endsection
