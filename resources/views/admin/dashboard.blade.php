@extends('layouts.admin')

@section('content')
<!-- Page Title -->
<div class="flex justify-between items-end">
    <div>
        <h2 class="font-headline text-4xl font-bold italic text-on-surface">Repository Overview</h2>
        <p class="text-on-surface-variant font-body mt-1">Managing the digital threads of human culture and evolution.</p>
    </div>
    <div class="flex space-x-3">
        <button class="px-5 py-2.5 bg-surface-container-lowest border border-outline-variant/40 rounded-lg text-sm font-medium hover:bg-stone-50 transition-colors shadow-sm">
            Generate Report
        </button>
        <button class="px-5 py-2.5 bg-gradient-to-br from-primary to-primary-container text-white rounded-lg text-sm font-bold uppercase tracking-wider shadow-md hover:opacity-90 transition-opacity">
            Sync Repository
        </button>
    </div>
</div>

<!-- 1. Overview Metrics -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4">
    <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between border-b-2 border-primary/10">
        <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Total Users</span>
        <div class="mt-4 flex items-baseline space-x-2">
            <span class="text-2xl font-bold font-sans">12,842</span>
            <span class="text-xs text-primary font-medium">+4.2%</span>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between border-b-2 border-primary/10">
        <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Active Learners</span>
        <div class="mt-4 flex items-baseline space-x-2">
            <span class="text-2xl font-bold font-sans">8,105</span>
            <span class="text-xs text-primary font-medium">Stable</span>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between border-b-2 border-primary/10">
        <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">New Papers</span>
        <div class="mt-4 flex items-baseline space-x-2">
            <span class="text-2xl font-bold font-sans">142</span>
            <span class="text-xs text-primary font-medium">+12</span>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between border-b-2 border-primary/10">
        <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Active Threads</span>
        <div class="mt-4 flex items-baseline space-x-2">
            <span class="text-2xl font-bold font-sans">318</span>
            <span class="text-xs text-error font-medium">-2%</span>
        </div>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between border-b-2 border-secondary/20">
        <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Pending UPSC</span>
        <div class="mt-4 flex items-baseline space-x-2">
            <span class="text-2xl font-bold font-sans">56</span>
            <span class="text-xs text-tertiary font-medium">Critical</span>
        </div>
    </div>
</div>

<!-- Bento Grid Layout -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- 2. Content Management Overview (2 cols) -->
    <div class="lg:col-span-2 space-y-4">
        <div class="flex items-center justify-between px-2">
            <h3 class="font-headline text-2xl italic text-on-surface">Archival Logs</h3>
            <button class="text-xs text-primary font-bold uppercase hover:underline">View All Records</button>
        </div>
        <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/20">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low">
                    <tr>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Document Title</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Category</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Last Modified</th>
                        <th class="px-6 py-4 label-md text-on-surface-variant uppercase tracking-widest text-[10px]">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-medium text-sm text-stone-900">The Paleolithic Transition in Deccan</p>
                            <p class="text-[10px] text-stone-400 italic">By Dr. S. Kulkarni</p>
                        </td>
                        <td class="px-6 py-4 text-xs text-stone-600">Research Paper</td>
                        <td class="px-6 py-4 text-xs text-stone-500">2 hrs ago</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-primary-fixed text-on-primary-fixed-variant rounded-full text-[10px] font-bold">PUBLISHED</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-medium text-sm text-stone-900">Tribal Linguistics of Northeast India</p>
                            <p class="text-[10px] text-stone-400 italic">Staff Upload</p>
                        </td>
                        <td class="px-6 py-4 text-xs text-stone-600">Lesson Material</td>
                        <td class="px-6 py-4 text-xs text-stone-500">Oct 24, 2023</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-tertiary-container text-on-tertiary-container rounded-full text-[10px] font-bold uppercase">Under Review</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-medium text-sm text-stone-900">Kinship Patterns in Oceania</p>
                            <p class="text-[10px] text-stone-400 italic">User Submission</p>
                        </td>
                        <td class="px-6 py-4 text-xs text-stone-600">Article</td>
                        <td class="px-6 py-4 text-xs text-stone-500">Oct 23, 2023</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-surface-container-highest text-on-surface-variant rounded-full text-[10px] font-bold uppercase">Draft</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-medium text-sm text-stone-900">Functionalism vs. Structuralism</p>
                            <p class="text-[10px] text-stone-400 italic">UPSC Core Series</p>
                        </td>
                        <td class="px-6 py-4 text-xs text-stone-600">UPSC Content</td>
                        <td class="px-6 py-4 text-xs text-stone-500">Oct 22, 2023</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-primary-fixed text-on-primary-fixed-variant rounded-full text-[10px] font-bold uppercase">Published</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- 4. User Growth Chart (Simulated) -->
        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-6 border border-outline-variant/10">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-headline text-xl italic text-on-surface">Registration Trends</h3>
                <div class="flex space-x-2">
                    <span class="flex items-center text-[10px] text-stone-400"><span class="w-2 h-2 rounded-full bg-primary mr-1"></span> New Users</span>
                    <span class="flex items-center text-[10px] text-stone-400"><span class="w-2 h-2 rounded-full bg-secondary mr-1"></span> Active Learners</span>
                </div>
            </div>
            <div class="h-48 w-full flex items-end justify-between space-x-2 relative pt-8">
                <!-- Grid Lines -->
                <div class="absolute inset-x-0 top-8 border-t border-outline-variant/10"></div>
                <div class="absolute inset-x-0 top-24 border-t border-outline-variant/10"></div>
                <div class="absolute inset-x-0 top-40 border-t border-outline-variant/10"></div>
                
                <div class="w-full h-24 bg-primary/20 rounded-t-sm relative group"></div>
                <div class="w-full h-32 bg-primary/20 rounded-t-sm"></div>
                <div class="w-full h-28 bg-primary/20 rounded-t-sm"></div>
                <div class="w-full h-40 bg-primary/30 rounded-t-sm"></div>
                <div class="w-full h-44 bg-primary/40 rounded-t-sm border-t-2 border-primary"></div>
                <div class="w-full h-36 bg-primary/20 rounded-t-sm"></div>
                <div class="w-full h-48 bg-primary/50 rounded-t-sm border-t-2 border-primary"></div>
                <div class="w-full h-52 bg-primary/60 rounded-t-sm border-t-2 border-primary"></div>
                <div class="w-full h-32 bg-primary/20 rounded-t-sm"></div>
                <div class="w-full h-40 bg-primary/30 rounded-t-sm"></div>
                <div class="w-full h-44 bg-primary/40 rounded-t-sm border-t-2 border-primary"></div>
                <div class="w-full h-56 bg-primary/70 rounded-t-sm border-t-2 border-primary"></div>
            </div>
            <div class="flex justify-between mt-4 text-[10px] font-medium text-stone-400 uppercase tracking-tighter">
                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span><span>Jul</span><span>Aug</span><span>Sep</span><span>Oct</span><span>Nov</span><span>Dec</span>
            </div>
        </div>
    </div>

    <!-- Side Grid (1 col) -->
    <div class="space-y-8">
        <!-- 5. Quick Actions -->
        <div class="bg-surface-container-low rounded-xl p-6">
            <h3 class="font-headline text-xl italic text-on-surface mb-4">Command Center</h3>
            <div class="grid grid-cols-1 gap-3">
                <button class="flex items-center justify-between p-3 bg-surface-container-lowest rounded-lg border border-outline-variant/30 hover:border-primary hover:shadow-md transition-all group">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-primary mr-3">library_add</span>
                        <span class="text-sm font-medium text-stone-700">Add New Lesson</span>
                    </div>
                    <span class="material-symbols-outlined text-stone-300 group-hover:text-primary transition-colors text-sm">arrow_forward</span>
                </button>
                <button class="flex items-center justify-between p-3 bg-surface-container-lowest rounded-lg border border-outline-variant/30 hover:border-primary hover:shadow-md transition-all group">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-primary mr-3">article</span>
                        <span class="text-sm font-medium text-stone-700">Publish Article</span>
                    </div>
                    <span class="material-symbols-outlined text-stone-300 group-hover:text-primary transition-colors text-sm">arrow_forward</span>
                </button>
                <button class="flex items-center justify-between p-3 bg-surface-container-lowest rounded-lg border border-outline-variant/30 hover:border-primary hover:shadow-md transition-all group">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-primary mr-3">gavel</span>
                        <span class="text-sm font-medium text-stone-700">Moderate Forum</span>
                    </div>
                    <span class="material-symbols-outlined text-stone-300 group-hover:text-primary transition-colors text-sm">arrow_forward</span>
                </button>
                <button class="flex items-center justify-between p-3 bg-surface-container-lowest rounded-lg border border-outline-variant/30 hover:border-primary hover:shadow-md transition-all group">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-primary mr-3">upload_file</span>
                        <span class="text-sm font-medium text-stone-700">Upload Research</span>
                    </div>
                    <span class="material-symbols-outlined text-stone-300 group-hover:text-primary transition-colors text-sm">arrow_forward</span>
                </button>
            </div>
        </div>

        <!-- 3. Community & Moderation Activity -->
        <div class="space-y-4">
            <h3 class="font-headline text-xl italic text-on-surface px-2">Moderation Queue</h3>
            <div class="space-y-3">
                <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/10 shadow-sm relative overflow-hidden group">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-error"></div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-bold text-error uppercase">Flagged Content</span>
                        <span class="text-[10px] text-stone-400">12m ago</span>
                    </div>
                    <p class="text-xs text-stone-700 line-clamp-2 italic mb-3">"...cultural relativism should not be used to justify ethical..."</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-[10px] font-medium">Rahul M.</span>
                        </div>
                        <button class="text-[10px] font-bold text-primary px-2 py-1 bg-primary/10 rounded">REVIEW</button>
                    </div>
                </div>
                <!-- More items can be added here -->
            </div>
        </div>
    </div>
</div>

<!-- Spacer -->
<div class="h-10"></div>
@endsection
