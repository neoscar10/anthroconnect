<div class="relative flex min-h-screen w-full flex-col">
    <main class="flex-1 max-w-5xl mx-auto w-full px-6 py-12 lg:py-20">

        <!-- Progress -->
        <div class="mb-12">
            <div class="flex flex-wrap items-center justify-center gap-2 md:gap-4 mb-6 text-sm md:text-base font-medium">
                @foreach(($progressMeta['steps'] ?? []) as $index => $progressStep)
                    <span class="{{ ($progressStep['is_current'] ?? false) ? 'text-primary' : 'text-slate-500' }} flex items-center gap-1">
                        @if($progressStep['is_completed'] ?? false)
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                        @endif
                        Step {{ $index + 1 }}: {{ $progressStep['title'] }}
                    </span>
                    @if(!$loop->last)
                        <span class="text-slate-400">/</span>
                    @endif
                @endforeach
            </div>

            <div class="w-full bg-primary/10 h-2 rounded-full overflow-hidden">
                <div class="bg-primary h-full transition-all duration-500"
                     style="width: {{ max(0, min(100, (int)($progressMeta['percent'] ?? 0))) }}%">
                </div>
            </div>

            <p class="text-center mt-3 text-slate-500 text-sm">
                {{ (int)($progressMeta['current_index'] ?? 1) }} of {{ (int)($progressMeta['total_steps'] ?? 1) }} steps completed
            </p>
        </div>

        <!-- Heading -->
        <div class="text-center mb-16 px-4">
            <h1 class="text-4xl md:text-5xl font-serif-heading font-bold mb-4 text-stone-900 leading-tight">
                {{ $stepData['title'] ?? 'Step Title Missing' }}
            </h1>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto font-body italic leading-relaxed">
                {{ $stepData['supporting_text'] ?? '' }}
            </p>

            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-stone-100 text-stone-500 text-[10px] font-bold uppercase tracking-[0.2em] mt-8 animate-in fade-in slide-in-from-bottom-2 duration-700 shadow-inner">
                <span class="material-symbols-outlined text-sm">info</span>
                @if(in_array($stepData['step_type'] ?? '', ['card_single', 'radio']))
                    Please select one option to proceed
                @else
                    You can select multiple options that apply to you
                @endif
            </div>
        </div>

        @if ($errors->has('selection'))
            <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm font-medium flex items-center gap-2 shadow-sm animate-in zoom-in-95">
                <span class="material-symbols-outlined text-base">error</span>
                {{ $errors->first('selection') }}
            </div>
        @endif

        <!-- Major Domains -->
        <section class="mb-16">
            <h2 class="text-2xl font-serif-heading font-bold mb-10 flex items-center gap-3 text-stone-900">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-xl">category</span>
                </div>
                Major Domains
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach(($stepData['options'] ?? []) as $option)
                    @php
                        $isSelected = in_array($stepData['step_type'] ?? '', ['card_single','radio'], true)
                            ? (($selected_option ?? null) === $option['key'])
                            : in_array($option['key'], $selected_options ?? [], true);
                    @endphp

                    <button
                        type="button"
                        wire:click="toggleOption('{{ $option['key'] }}')"
                        class="flex flex-col text-left p-8 rounded-2xl border-2 transition-all duration-300 group relative overflow-hidden {{ $isSelected ? 'border-primary bg-primary/5 shadow-xl shadow-primary/5' : 'border-stone-200 bg-white hover:border-primary/40 hover:bg-stone-50' }}"
                    >
                        @if($isSelected)
                            <div class="absolute top-4 right-4 text-primary">
                                <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1">check_circle</span>
                            </div>
                        @endif

                        
                        <h3 class="font-serif-heading font-bold text-xl mb-3 text-stone-900">{{ $option['label'] }}</h3>
                        
                        @if(!empty($option['description']))
                            <p class="text-sm text-slate-600 leading-relaxed font-body italic">{{ $option['description'] }}</p>
                        @endif
                    </button>
                @endforeach
            </div>
        </section>

        <!-- Additional Interests -->
        @if(!empty($stepData['additional_interests'] ?? []) && ($progressMeta['current_index'] ?? 0) > 1)
            <section class="mb-16">
                <h2 class="text-2xl font-serif-heading font-bold mb-8 flex items-center gap-3 text-stone-900">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-xl">label</span>
                    </div>
                    Additional Interests
                </h2>
                <p class="text-xs text-slate-500 mb-6 font-bold uppercase tracking-widest ml-13 flex items-center gap-2">
                    <span class="material-symbols-outlined text-xs">info</span>
                    Select all that apply to you
                </p>

                <div class="flex flex-wrap gap-4">
                    @foreach(($stepData['additional_interests'] ?? []) as $interest)
                        @php $interestSelected = in_array($interest['key'], $selected_additional_interests ?? [], true); @endphp
                        <button
                            type="button"
                            wire:click="toggleAdditionalInterest('{{ $interest['key'] }}')"
                            class="px-8 py-3 rounded-full border-2 transition-all text-sm font-bold uppercase tracking-widest {{ $interestSelected ? 'bg-primary text-white border-primary shadow-lg shadow-primary/20' : 'border-stone-300 hover:bg-stone-50 hover:border-primary' }}"
                        >
                            {{ $interest['label'] }}
                        </button>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Regions -->
        @if(!empty($stepData['show_regions']) && !empty($stepData['regions']))
            <section class="mb-16">
                <h2 class="text-2xl font-serif-heading font-bold mb-8 flex items-center gap-3 text-stone-900">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-xl">map</span>
                    </div>
                    Regions You Are Interested In
                </h2>
                <p class="text-xs text-slate-500 mb-6 font-bold uppercase tracking-widest ml-13 flex items-center gap-2">
                    <span class="material-symbols-outlined text-xs">info</span>
                    Select all that apply to you
                </p>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                    @foreach(($stepData['regions'] ?? []) as $region)
                        @php $isRegionSelected = in_array($region['key'], $selected_regions ?? [], true); @endphp
                        <button
                            type="button"
                            wire:click="toggleRegion('{{ $region['key'] }}')"
                            class="flex flex-col items-center gap-4 p-6 rounded-2xl border-2 transition-all group {{ $isRegionSelected ? 'border-primary bg-primary/5 shadow-md' : 'border-stone-200 bg-white hover:border-primary/50' }}"
                        >
                            <span class="text-sm font-bold text-stone-700 italic group-hover:text-stone-900 transition-colors">{{ $region['label'] }}</span>
                            <div class="w-full h-1 rounded-full overflow-hidden bg-primary/20">
                                <div class="h-full bg-primary transition-all duration-500" style="width: {{ $isRegionSelected ? '100' : '0' }}%"></div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- UPSC -->
        @if(!empty($stepData['show_upsc_toggle']))
            <div class="bg-primary/5 border border-primary/20 rounded-3xl p-10 mb-12 flex flex-col md:flex-row items-center justify-between gap-10 shadow-sm">
                <div class="text-center md:text-left">
                    <h3 class="text-2xl font-serif-heading font-bold mb-2 text-stone-900 italic leading-tight">{{ $stepData['upsc_label'] }}</h3>
                    <p class="text-slate-600 font-body italic leading-relaxed">{{ $stepData['upsc_description'] }}</p>
                </div>

                <label class="relative inline-flex items-center cursor-pointer scale-110">
                    <input type="checkbox" wire:model.live="preparing_for_upsc" class="sr-only peer">
                    <div class="w-16 h-8 bg-stone-300 rounded-full peer-checked:bg-primary after:content-[''] after:absolute after:top-1 after:start-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:after:translate-x-full shadow-inner after:shadow-md"></div>
                </label>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex flex-col items-center gap-8 pt-12 border-t border-stone-200">
            <button
                type="button"
                wire:click="saveAndContinue"
                class="w-full md:w-80 py-6 bg-primary hover:bg-primary/90 text-white font-bold rounded-2xl shadow-xl shadow-primary/30 transition-all active:scale-95 flex items-center justify-center gap-3 group"
            >
                <span class="uppercase tracking-[0.2em] text-sm">{{ $stepData['continue_label'] ?? 'Continue' }}</span>
                <span class="material-symbols-outlined text-xl group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </button>

            @if(!empty($stepData['is_skippable']))
                <button
                    type="button"
                    wire:click="skip"
                    class="text-slate-500 hover:text-primary font-bold text-xs uppercase tracking-[0.2em] transition-colors italic"
                >
                    {{ $stepData['skip_label'] ?? 'Skip for now' }}
                </button>
            @endif
        </div>

    </main>
</div>