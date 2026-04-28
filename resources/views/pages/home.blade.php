@extends('layouts.public')

@section('content')

<!-- HERO -->
<section class="relative min-h-[700px] flex flex-col justify-center px-6 py-12 overflow-hidden bg-stone-50">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-stone-100/60 lg:bg-orange-50/40 mix-blend-multiply"></div>
        <img class="w-full h-full object-cover opacity-60 lg:opacity-30" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB0Tkriu6ZA3X61zhkQcYN1WHjSsQOB3B2yqiHs7QABsLbZdFzpc94BVIsL6TxFhBHRK6Qgpvann-vqHGkk0Qo3q4gWfWc8e_Hf3l1I6A84CCQ8cCslBvDE8O_Mi0hcY2M37cY_Yy4l_ANQsbXNq9h25fMPRGEwOAty_6n4RQMStRkuO0QDgzUrX-He3TJtjNtpph6qzt07J__NrRgCBhhUXgnjyM1ed-_uyUQKb1rBItZFovjmIgs7fMfA1UxFfJlpFiDbMLgN-UIV"/>
    </div>
    <div class="relative z-10 max-w-4xl mx-auto text-center">
        <span class="inline-block px-4 py-1.5 rounded-full bg-orange-100 text-orange-900 text-xs font-bold tracking-widest uppercase mb-6">Established 2024</span>
        <h2 class="text-4xl md:text-5xl lg:text-7xl font-headline text-stone-900 leading-tight mb-6">Understanding <span class="text-orange-800 italic">Humanity</span> Through Anthropology</h2>
        <p class="text-lg md:text-xl text-stone-700 font-body leading-relaxed mb-10 max-w-2xl mx-auto">
            Preserving narratives, analyzing cultures, and exploring the biological evolution of our species through a dedicated academic lens.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <button class="w-full sm:w-auto py-4 px-10 bg-stone-900 text-stone-50 font-bold rounded-xl shadow-xl hover:-translate-y-1 active:scale-95 transition-all">
                Explore Anthropology
            </button>
            <button class="w-full sm:w-auto py-4 px-10 bg-white border border-stone-200 text-stone-900 font-bold rounded-xl active:scale-95 hover:bg-stone-50 transition-all">
                Start Learning
            </button>
        </div>
    </div>
</section>

<!-- AUDIENCE (Bento Grid) -->
<section class="max-w-7xl mx-auto px-6 py-20 lg:py-32">
    <div class="text-center mb-16">
        <h3 class="text-2xl lg:text-5xl font-headline text-stone-900 mb-4 italic">Who We Serve</h3>
        <div class="w-16 h-1 bg-orange-800 mx-auto rounded-full"></div>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 lg:gap-6">
        <div class="p-8 bg-stone-100/50 rounded-3xl border border-stone-200 flex flex-col items-center text-center hover:bg-orange-50 transition group">
            <span class="material-symbols-outlined text-stone-400 group-hover:text-orange-800 mb-4 text-3xl transition-colors">history_edu</span>
            <span class="text-sm font-bold text-stone-900 uppercase tracking-widest">UPSC Aspirants</span>
        </div>
        <div class="p-8 bg-stone-100/50 rounded-3xl border border-stone-200 flex flex-col items-center text-center hover:bg-orange-50 transition group">
            <span class="material-symbols-outlined text-stone-400 group-hover:text-orange-800 mb-4 text-3xl transition-colors">science</span>
            <span class="text-sm font-bold text-stone-900 uppercase tracking-widest">Researchers</span>
        </div>
        <div class="p-8 bg-stone-100/50 rounded-3xl border border-stone-200 flex flex-col items-center text-center hover:bg-orange-50 transition group">
            <span class="material-symbols-outlined text-stone-400 group-hover:text-orange-800 mb-4 text-3xl transition-colors">school</span>
            <span class="text-sm font-bold text-stone-900 uppercase tracking-widest">Students</span>
        </div>
        <div class="p-8 bg-stone-100/50 rounded-3xl border border-stone-200 flex flex-col items-center text-center hover:bg-orange-50 transition group">
            <span class="material-symbols-outlined text-stone-400 group-hover:text-orange-800 mb-4 text-3xl transition-colors">psychology</span>
            <span class="text-sm font-bold text-stone-900 uppercase tracking-widest">Curious Minds</span>
        </div>
        <div class="p-8 bg-stone-100/50 rounded-3xl border border-stone-200 flex flex-col items-center text-center hover:bg-orange-50 transition group col-span-2 md:col-span-1">
            <span class="material-symbols-outlined text-stone-400 group-hover:text-orange-800 mb-4 text-3xl transition-colors">groups_3</span>
            <span class="text-sm font-bold text-stone-900 uppercase tracking-widest">Educators</span>
        </div>
    </div>
</section>

<!-- LEARNING PATHS -->
<section class="bg-stone-100/50 py-20 lg:py-32">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col lg:flex-row justify-between lg:items-end mb-12 gap-6">
            <div>
                <h3 class="text-3xl lg:text-5xl font-headline text-stone-900 mb-2">Learning Paths</h3>
                <p class="text-stone-500">Expertly curated academic modules for modern learners</p>
            </div>
            <a href="#" class="flex items-center gap-2 text-orange-800 font-bold hover:gap-4 transition-all uppercase tracking-widest text-xs">
                View All <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-stone-200 hover:shadow-xl transition-shadow group cursor-pointer">
                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center mb-6 group-hover:bg-orange-800 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-orange-800 group-hover:text-white">groups</span>
                </div>
                <h4 class="font-bold text-stone-900 mb-2">Cultural</h4>
                <p class="text-xs text-stone-500 mb-4 hidden lg:block">Exploring societal behaviors and belief systems.</p>
                <span class="text-[10px] px-2.5 py-1 bg-green-100 text-green-800 rounded-full font-bold uppercase tracking-widest">Beginner</span>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-stone-200 hover:shadow-xl transition-shadow group cursor-pointer">
                <div class="w-12 h-12 bg-stone-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-stone-900 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-stone-700 group-hover:text-white">architecture</span>
                </div>
                <h4 class="font-bold text-stone-900 mb-2">Archaeology</h4>
                <p class="text-xs text-stone-500 mb-4 hidden lg:block">Uncovering history through material remains.</p>
                <span class="text-[10px] px-2.5 py-1 bg-orange-100 text-orange-800 rounded-full font-bold uppercase tracking-widest">Advanced</span>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-stone-200 hover:shadow-xl transition-shadow group cursor-pointer">
                <div class="w-12 h-12 bg-stone-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-stone-900 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-stone-700 group-hover:text-white">skeleton</span>
                </div>
                <h4 class="font-bold text-stone-900 mb-2">Physical</h4>
                <p class="text-xs text-stone-500 mb-4 hidden lg:block">The biological evolution and adaptation of humans.</p>
                <span class="text-[10px] px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full font-bold uppercase tracking-widest">Intermediate</span>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-stone-200 hover:shadow-xl transition-shadow group cursor-pointer">
                <div class="w-12 h-12 bg-stone-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-stone-900 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-stone-700 group-hover:text-white">translate</span>
                </div>
                <h4 class="font-bold text-stone-900 mb-2">Linguistic</h4>
                <p class="text-xs text-stone-500 mb-4 hidden lg:block">Analyzing language within social and cultural contexts.</p>
                <span class="text-[10px] px-2.5 py-1 bg-green-100 text-green-800 rounded-full font-bold uppercase tracking-widest">Beginner</span>
            </div>
        </div>
    </div>
</section>

<!-- EDITORIAL (Magazine Stack) -->
<section class="max-w-7xl mx-auto px-6 py-20 lg:py-32 bg-white">
    <div class="flex justify-between items-center mb-16">
        <h3 class="text-3xl lg:text-5xl font-headline text-stone-900 italic">Editorial Insights</h3>
        <button class="hidden lg:block text-xs font-bold uppercase tracking-widest text-stone-400 hover:text-stone-900">Explore Journal</button>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20">
        <article class="group">
            <div class="aspect-[16/9] lg:aspect-[4/3] rounded-3xl overflow-hidden mb-6 shadow-xl relative">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCUuG3y-NTPK6v-GhoCmDcNcd7Gs3gAZjasvvnSvE5HiM688SHeRXyiJJkuhWEw9GS5SqRBCbnWzZ790PMwRomvZB1KDXZDqTsrKphkS3zGd8bvl_KrKj-Ls348nlGGNFOtPMxaYdJEXdk8XoMsQVy-lmDMDlmQBZ30Yuf2iDqE2aXaI9cSDcPz8017nlgQyisMnIeWYETBsOH3bNsgS3R0zpk3O-RvLZn4kpwYqOQEmyxKpZjT-MyGobcJGcO3vcu4QxOw1WDryzue"/>
                <div class="absolute inset-0 bg-gradient-to-t from-stone-900/40 to-transparent"></div>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <span class="text-[11px] font-bold text-orange-800 uppercase tracking-widest">Cultural Studies</span>
                <span class="w-1 h-1 bg-stone-300 rounded-full"></span>
                <span class="text-xs text-stone-500 italic">12 min read</span>
            </div>
            <h4 class="text-2xl lg:text-3xl font-headline text-stone-900 leading-tight group-hover:text-orange-900 transition-colors mb-4">The Resilience of Oral Traditions in Digital Eras</h4>
            <p class="text-stone-600 leading-relaxed font-body">An exploration of how indigenous narratives thrive and adapt within global digital social networks.</p>
        </article>

        <article class="group">
            <div class="aspect-[16/9] lg:aspect-[4/3] rounded-3xl overflow-hidden mb-6 shadow-xl relative">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAnnzkYJMbfKTuJhn-wiApDHRFP3HW6DlZU4_LtDIKEKaTc2vzUzHx5YCLdtns8r4Nxrc7hLvTX9NCKW9QMktYj89UzBIL_zsx1ShsuHRs8W7LIVlpMq7kOkswGqciLLqGDZOgvKCRX7z4C-luuXsCef8pDRJm6vzJiJ1l0vAO-GHE7FQPX8jUtLoGIrnuAALSpXEomCrCqazXtMXOgDJuv38PSD3LRLS22SoPUWWiDM97Vd6C3aMyGzMjy64MNV63OfZdM7EeFI42H"/>
                <div class="absolute inset-0 bg-gradient-to-t from-stone-900/40 to-transparent"></div>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <span class="text-[11px] font-bold text-orange-800 uppercase tracking-widest">Archaeology</span>
                <span class="w-1 h-1 bg-stone-300 rounded-full"></span>
                <span class="text-xs text-stone-500 italic">8 min read</span>
            </div>
            <h4 class="text-2xl lg:text-3xl font-headline text-stone-900 leading-tight group-hover:text-orange-900 transition-colors mb-4">Unearthing the Lost Cities of the Indus Valley</h4>
            <p class="text-stone-600 leading-relaxed font-body">New surveys reveal complex urban planning and hydrological systems dating back millennia.</p>
        </article>
    </div>
</section>

<!-- KNOWLEDGE MAP -->
<section class="lg:max-w-[calc(100%-3rem)] lg:mx-auto lg:rounded-[3rem] px-6 py-20 lg:py-32 bg-stone-900 text-stone-50 overflow-hidden relative">
    <div class="absolute right-0 top-0 w-64 h-64 bg-orange-900/20 blur-[100px] rounded-full"></div>
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center relative z-10">
        <div>
            <span class="inline-block px-4 py-1.5 rounded-full bg-stone-800 text-orange-200 text-xs font-bold tracking-widest uppercase mb-6">Interactive Tools</span>
            <h3 class="text-3xl lg:text-6xl font-headline mb-6 leading-tight">The Interactive <br/>Knowledge <span class="italic text-orange-200">Map</span></h3>
            <p class="text-stone-400 text-lg mb-10 leading-relaxed font-body">
                Visualize the connections between global civilizations through time. Our interactive cartography tool allows you to trace migration, trade, and cultural diffusion.
            </p>
            <a href="{{ route('knowledge-map.show', ['from' => request()->fullUrl()]) }}" class="w-full lg:w-auto py-5 px-12 bg-orange-800 text-white font-bold rounded-2xl active:scale-95 hover:bg-orange-700 transition-all shadow-2xl shadow-orange-900/40 flex items-center justify-center gap-2 text-decoration-none">
                Launch Explorer <span class="material-symbols-outlined text-sm">north_east</span>
            </a>
        </div>
        
        <div class="aspect-video w-full bg-stone-800 rounded-3xl flex items-center justify-center border border-stone-700 relative overflow-hidden shadow-2xl">
            <img class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD-bP_aZfsM3zBWR3GOkv5BLVFY6eDcMKEj1WK-VShabsKMPo6xnkvDJ_NL-35dzzk7bMbUPwmS-rjGhaUw3WW2MHlZugMcQv_4EqqlOz9YBaGNqW0fDjABN0o3-kyLT5Q2iVJa22tt7DQMCtjhs8zP25tGP5CFPwqedna4TKJezt0BE0ZFjL0ENxVGSk3nLxfox9KjMFxpF6zBAPi4P5EKD2vKsZ1elxtT88TyBXYUKMNJz5YVt9_Vr9uwWOYhBLeqWJ9YJkkTyQ6b"/>
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-16 h-16 bg-white/10 backdrop-blur rounded-full flex items-center justify-center mb-4 ring-1 ring-white/20">
                    <span class="material-symbols-outlined text-4xl text-orange-200">map</span>
                </div>
                <span class="text-xs font-bold tracking-widest uppercase text-stone-300">Preview Mode Enabled</span>
            </div>
        </div>
    </div>
</section>

<!-- COMMUNITY -->
<section class="px-6 py-20 lg:py-32 bg-stone-50">
    <div class="max-w-4xl mx-auto bg-white p-8 lg:p-16 rounded-[2.5rem] border border-stone-200 shadow-2xl shadow-stone-200/50">
        <div class="flex flex-col items-center text-center">
            <div class="flex -space-x-4 mb-8">
                <img class="w-12 h-12 rounded-full border-4 border-white object-cover shadow-md" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD-ld7oPBvmDzIVtPAkIlBSOD19DByTANgl7L7mLCfBmg2GgOZaDk7OYdty1RybRGUy-R_spjQ6cVKz_ZdU_bNM1-KxFJXf_1nWCXl8FFxZwwu97lZCX2h3wB_yMQCWK0GxWVzzOfm05aPRh0P5Nd70pecRfyoG2EEXJfKdEvYeQggbBESoFNrQx2FdyTQGYsNi9WFGlcYobvyA82c1La0k_KJqisnQCEsYkIV8gfrHu0GvwbbV59Z9udbNcLIv1C4kPYj4_dorRdFt"/>
                <img class="w-12 h-12 rounded-full border-4 border-white object-cover shadow-md" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQQZx2wm48il8t6YfZjXBL6VqorPU4Xk1VPQ-vhpaqmTUFl9DXzeGR3dgp7koWf3VyrOoWQ2xDeWFWwjAaY-sVhZK6gBOyv_Pzc33Xb2f0Zc_JijsDiO6YFZiY9lBaRCggkv25yNDliNwB-XjtWaVdIjOTvUui2pjtGvPtdQlizXk4293YdgDiTsnpK6iHMxSC2k8p0JcvPBsMpPjBBFq0KQotXPICL8JXGBye0FD5ijcZOrQVBSfJ2B0agqSWKIqIhs4CjDLR0f0N"/>
                <img class="w-12 h-12 rounded-full border-4 border-white object-cover shadow-md" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCFOVYi7EIdlpyqsf5N8Ie8qYvdKKtLuoui4k1WLjHUF9o4IzMv5ZGtuhAYHIG1erNGXhqdVkk2EY2MGb1-8-2SwXEdp4U0ScBlyyl2ep9wnqguH0T_V8tf2q2StF21rMoHPadSop-B2pD-LtF4rlqSfU6xf5qCFf9Ep8x0u1gphkcSCTir_tVECFMl2cq9Ixm5ty3yTWd1aF6AlghXKU4FI7VEZeXWTXkCndWPdnu50IQKmYJJcaCNr1keVnfwVRhsvR4yxTgq2Pzb"/>
                <div class="w-12 h-12 rounded-full border-4 border-white bg-orange-100 flex items-center justify-center text-[11px] font-bold text-orange-900 shadow-md">+12k</div>
            </div>
            
            <h3 class="text-3xl lg:text-5xl font-headline text-stone-900 mb-4">Global Discussion Forum</h3>
            <p class="text-stone-500 text-lg mb-12 max-w-lg font-body">Join the conversation with anthropologists, students, and researchers from 45+ countries.</p>
            
            <div class="grid grid-cols-2 gap-8 lg:gap-16 w-full mb-12 py-8 border-y border-stone-100">
                <div class="text-center">
                    <div class="text-3xl lg:text-5xl font-bold text-stone-900 mb-2">12k+</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-stone-400 font-bold">Discussions</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl lg:text-5xl font-bold text-stone-900 mb-2">4.8k</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-stone-400 font-bold">Researches</div>
                </div>
            </div>
            
            <button class="w-full lg:w-auto py-5 px-16 bg-stone-900 text-stone-50 font-bold rounded-2xl active:scale-95 hover:bg-stone-800 transition-all shadow-xl shadow-stone-900/20">Enter Community</button>
        </div>
    </div>
</section>

<!-- THOUGHT LEADERSHIP (Dr. Sudhir Yadav) -->
<section class="px-6 py-20 lg:py-32 bg-white text-center">
    <div class="max-w-4xl mx-auto flex flex-col items-center">
        <div class="w-32 lg:w-48 h-32 lg:h-48 rounded-full overflow-hidden mb-10 border-[6px] border-orange-50 p-1.5 shadow-2xl">
            <img class="w-full h-full object-cover rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA5ufau4auEA9FKyu_RsjKI4ap9W26cHed8F5pkaa9pSFgqZ69-ercRnKrFmFjRYjF12iOUCcGKZJzbzNWONN3hpcpRh6aEuMRuWDY7OuewHARBRLcsylrnnVCYDOFhEQYph1ge6RRy-5xx1na1t7YQQnPYbjSr14o8APThoEKTsyBx_ClB4ZPK-P4Gaicc4T7rexZ6DDWDtvBwy0reQgFqB12_gGBS3LvoPoaXP8lyVwa4fhbSG061DnjwOb9xBzR6Y34DKlSO30fF"/>
        </div>
        
        <h4 class="text-2xl font-headline text-stone-900 mb-1">Dr. Sudhir Yadav</h4>
        <p class="text-xs font-bold text-orange-800 uppercase tracking-widest mb-10">Chief Academic Officer</p>
        
        <blockquote class="text-stone-700 italic font-headline text-2xl lg:text-5xl leading-tight mb-12 px-4 relative">
            <span class="absolute -top-6 lg:-top-10 left-0 text-orange-100 text-7xl lg:text-9xl font-serif">“</span>
            "Anthropology is not just a study of the past; it is the bridge to understanding our collective future."
            <span class="absolute -bottom-16 right-0 text-orange-100 text-7xl lg:text-9xl font-serif">”</span>
        </blockquote>
        
        <a class="text-sm font-bold text-stone-900 underline underline-offset-8 decoration-orange-300 decoration-2 hover:text-orange-800 transition-colors uppercase tracking-widest" href="#">View Full Publications</a>
    </div>
</section>

@endsection
