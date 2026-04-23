@extends('layouts.admin')

@section('content')
<div class="space-y-12">
    <!-- Header -->
    <div class="flex justify-between items-end">
        <div>
            <h1 class="font-headline text-4xl text-on-surface mb-2 italic">Research Library Overview</h1>
            <p class="font-body text-on-surface-variant text-lg">Manage scholarly publications, monographs, and academic papers.</p>
        </div>
        <a href="{{ route('admin.library.resources.index', ['open_modal' => 1]) }}" class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-[10px] flex items-center gap-2 shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">add</span>
            New Publication
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-surface-container-lowest p-8 rounded-[32px] border border-outline-variant/10 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">library_books</span>
                </div>
                <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Total Resources</span>
            </div>
            <p class="text-4xl font-headline italic font-bold text-on-surface">{{ $stats['total'] }}</p>
        </div>

        <div class="bg-surface-container-lowest p-8 rounded-[32px] border border-outline-variant/10 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 bg-secondary/10 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-secondary text-sm">check_circle</span>
                </div>
                <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Published</span>
            </div>
            <p class="text-4xl font-headline italic font-bold text-secondary">{{ $stats['published'] }}</p>
        </div>

        <div class="bg-surface-container-lowest p-8 rounded-[32px] border border-outline-variant/10 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 bg-tertiary/10 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-tertiary text-sm">edit_note</span>
                </div>
                <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Drafts</span>
            </div>
            <p class="text-4xl font-headline italic font-bold text-tertiary">{{ $stats['drafts'] }}</p>
        </div>

        <div class="bg-surface-container-lowest p-8 rounded-[32px] border border-outline-variant/10 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-orange-700 text-sm">star</span>
                </div>
                <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Featured</span>
            </div>
            <p class="text-4xl font-headline italic font-bold text-orange-700">{{ $stats['featured'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Latest Additions -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="font-headline text-2xl text-on-surface italic">Recent Accessions</h3>
            <div class="bg-white rounded-[32px] border border-outline-variant/10 overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-low/50">
                        <tr>
                            <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Title</th>
                            <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Type</th>
                            <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        @forelse($stats['latest_additions'] as $res)
                            <tr class="hover:bg-surface-container-low/30 transition-colors">
                                <td class="px-8 py-4">
                                    <p class="font-bold text-on-surface text-sm line-clamp-1">{{ $res->title }}</p>
                                    <p class="text-[10px] text-on-surface-variant uppercase tracking-widest mt-0.5">{{ $res->author_display }}</p>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-widest">{{ $res->resourceType->name }}</span>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest {{ $res->status === 'published' ? 'bg-primary/10 text-primary border border-primary/20' : 'bg-stone-100 text-stone-500 border border-stone-200' }}">
                                        {{ $res->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-8 py-12 text-center text-stone-400 italic">No resources available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions / Info -->
        <div class="space-y-8">
            <div class="bg-secondary-container/20 border border-secondary/10 p-8 rounded-[40px] space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-secondary">info</span>
                    </div>
                    <h4 class="font-headline text-xl text-secondary italic font-bold">Library Policy</h4>
                </div>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    Resources in the library should include full citations and, where possible, a previewable PDF. High-quality abstracts are required for publication to support global research discovery.
                </p>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-widest text-secondary">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        Verified Citations
                    </div>
                    <div class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-widest text-secondary">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        Thematic Tagging
                    </div>
                    <div class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-widest text-secondary">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        Cross-Module Linking
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
