@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<section class="relative h-[70vh] min-h-[500px] w-full overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 hover:scale-105" 
         data-alt="Vibrant traditional cultural festival with people in ceremonial dress" 
         style="background-image: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.7)), url('https://lh3.googleusercontent.com/aida-public/AB6AXuBawWtNwzl9sMi8WPWBCDFvNPeKFMCVyM05exYM0o8RpAgoaUioIGOPTVBk05CNqnUF0st_N3p8ighepNHv8Uqz8j-ABivd6_8qtg1A4hkZ0JN7aqtjgaIiXIu3rhBvm21ES0i_kASvF4uCsf5OgrxhqD6xvffc5bsH_CzCagTsxnkoBCsPtuDgCXu_uYo5L8AEloAe-5WT3OuZktzXMSCJSu4UGMUkI5PR9DFLntkNNq2cdaZB-Ozx5hsrC-lJBkukkV71uzwg5vw')">
    </div>
    <div class="relative h-full max-w-7xl mx-auto px-6 flex flex-col items-center justify-center text-center">
        <span class="bg-primary/90 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-6">Editorial</span>
        <h1 class="font-serif text-5xl md:text-7xl font-bold text-white mb-6 leading-tight max-w-4xl">Explore Humanity</h1>
        <p class="text-white/90 text-lg md:text-xl max-w-2xl font-light leading-relaxed">
            Discover anthropology through immersive stories about culture, identity, traditions, and human societies around the world.
        </p>
        <button class="mt-10 bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-lg font-bold transition-all transform hover:-translate-y-1">
            Start Exploring
        </button>
    </div>
</section>

<!-- Theme Navigation Pills -->
<div class="bg-stone-200/20 dark:bg-stone-900 py-6 border-b border-stone-200/50 dark:border-primary/10">
    <div class="max-w-7xl mx-auto px-6 space-y-4">
        @foreach($tagGroups as $group)
            <div class="flex items-center gap-4 overflow-x-auto no-scrollbar">
                <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest shrink-0">{{ $group->name }}:</span>
                <div class="flex gap-2">
                    @if($loop->first)
                        <a href="{{ route('explore.index') }}" 
                           class="whitespace-nowrap px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest transition-colors {{ !$tagId ? 'bg-primary text-white' : 'bg-white dark:bg-primary/10 border border-stone-200 dark:border-primary/20 hover:bg-stone-200/30 text-stone-900 dark:text-stone-100' }}">
                            All
                        </a>
                    @endif
                    @foreach($group->activeTags as $tag)
                        <a href="{{ route('explore.index', ['tag_id' => $tag->id]) }}" 
                           class="whitespace-nowrap px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest transition-colors {{ $tagId == $tag->id ? 'bg-primary text-white' : 'bg-white dark:bg-primary/10 border border-stone-200 dark:border-primary/20 hover:bg-stone-200/30 text-stone-900 dark:text-stone-100' }}">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Featured Story -->
<section class="max-w-7xl mx-auto px-6 py-16">
    @if($featuredArticle)
    <div class="grid lg:grid-cols-2 gap-12 items-center bg-white dark:bg-primary/5 rounded-2xl overflow-hidden border border-stone-200 dark:border-primary/10 shadow-sm">
        <div class="h-[400px] lg:h-[600px] bg-cover bg-center" 
             style="background-image: url('{{ $featuredArticle->featured_image ? Storage::url($featuredArticle->featured_image) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuB1KrzCmQu1SRay1g3xwXb34xOECNU7hsNulEoKpJxrQI33s6lcR1kxVQWaAqd2jWREeOYYJUKAQAYHlNJRTbkAABcQVSa9Q1WTvE-0M5SWd9xohvvS8_i0-mZ4FMzzSQwAvEA1L1y9wG4xD70lA7gKCrnCw1GprAFAKXceoz2eyK9Sj1sneWzVTyAxoOjH-9QnJtovBZQjNYfh505cm4BtDNZQQ1eqxonbhvV99UXuLrKo4vJLer0BJzVRkJZSnGPKy4ACpmz6Nak' }}')">
        </div>
        <div class="p-8 lg:p-12">
            <span class="text-primary font-bold uppercase tracking-widest text-xs mb-4 block">
                {{ $featuredArticle->tags->first() ? $featuredArticle->tags->first()->name : 'Feature Story' }}
            </span>
            <h2 class="font-serif text-4xl lg:text-5xl font-bold mb-6 leading-tight text-stone-900">
                {{ $featuredArticle->title }}
            </h2>
            <p class="text-stone-600 dark:text-stone-400 text-lg mb-8 leading-relaxed">
                {{ $featuredArticle->excerpt }}
            </p>
            <div class="flex items-center gap-4 mb-8">
                <div class="size-12 rounded-full bg-cover bg-center border border-stone-200" 
                     style="background-image: url('{{ $featuredArticle->creator->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($featuredArticle->creator->name) }}')">
                </div>
                <div>
                    <p class="font-bold text-stone-900">{{ $featuredArticle->creator->name }}</p>
                    <p class="text-xs text-stone-500">
                        {{ $featuredArticle->published_at ? $featuredArticle->published_at->format('M d, Y') : 'Recently' }} • {{ $featuredArticle->reading_time_minutes ?? 5 }} min read
                    </p>
                </div>
            </div>
            <a href="{{ route('explore.show', $featuredArticle->slug) }}" class="inline-block bg-primary text-white px-8 py-3 rounded-lg font-bold hover:bg-primary/90 transition-all">
                Read Story
            </a>
        </div>
    </div>
    @else
    <div class="bg-stone-100 p-12 text-center rounded-2xl border border-stone-200">
        <span class="material-symbols-outlined text-4xl text-stone-400 mb-2">auto_stories</span>
        <h2 class="font-serif text-2xl font-bold text-stone-900 mb-2">The archives are silent</h2>
        <p class="text-stone-500">No featured narrative matches your current exploration.</p>
    </div>
    @endif
</section>

<!-- Story Grid -->
<section class="max-w-7xl mx-auto px-6 py-16 border-t border-stone-200 dark:border-primary/10">
    <h3 class="font-serif text-3xl font-bold mb-10 text-stone-900">Latest Explorations</h3>
    
    @if($articles->count() > 0)
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
        @foreach($articles as $article)
        <div class="group cursor-pointer">
            <div class="aspect-[4/3] rounded-xl overflow-hidden mb-6 bg-stone-100">
                @if($article->featured_image)
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                         src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" />
                @else
                    <div class="w-full h-full flex items-center justify-center opacity-10">
                        <span class="material-symbols-outlined text-4xl">image</span>
                    </div>
                @endif
            </div>
            <h4 class="font-serif text-2xl font-bold mb-3 group-hover:text-primary transition-colors text-stone-900">
                {{ $article->title }}
            </h4>
            <p class="text-stone-600 dark:text-stone-400 mb-4 line-clamp-3">
                {{ $article->excerpt }}
            </p>
            <div class="flex items-center justify-between text-xs text-stone-500 font-medium">
                <div class="flex items-center gap-2">
                    <span>{{ $article->published_at ? $article->published_at->format('F d, Y') : 'Recently' }}</span> 
                    <span>•</span> 
                    <span>{{ $article->reading_time_minutes ?? 5 }} min read</span>
                </div>
                @if($article->tags->isNotEmpty())
                <span class="text-primary font-bold uppercase tracking-widest text-[10px]">{{ $article->tags->first()->name }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    
    @if($articles->hasPages())
        <div class="mt-16 text-center">
            {{ $articles->links() }}
        </div>
    @endif
    
    @else
    <div class="bg-stone-100 p-12 text-center rounded-2xl border border-stone-200">
        <p class="text-stone-500">End of the archive. No more articles to show right now.</p>
    </div>
    @endif
</section>
@endsection
