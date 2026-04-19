@extends('layouts.admin')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush

@section('content')
<div x-data="onboardingEditor()">
    <!-- Header Section -->
    <div class="flex justify-between items-end mb-12">
        <div>
            <h1 class="font-headline text-4xl text-on-surface mb-2">Onboarding Flow Management</h1>
            <p class="font-body text-on-surface-variant text-lg">Configure and sequence the steps users encounter after registration.</p>
        </div>
        <form action="{{ route('admin.onboarding.store') }}" method="POST" class="flex items-center gap-3">
            @csrf
            <input type="hidden" name="type" value="card_multi">
            <input type="hidden" name="title" value="">
            <button type="submit" class="bg-gradient-to-br from-primary to-primary-container text-on-primary px-6 py-3 rounded-lg font-bold uppercase tracking-widest text-xs flex items-center gap-2 shadow-lg shadow-primary/10 hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-sm">add_circle</span>
                New Step
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-primary-fixed text-on-primary-fixed-variant rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Dashboard Layout: Steps List + Editor Side Panel -->
    <div class="grid grid-cols-12 gap-8">
        <!-- Sequencing View (Left 4 Columns) -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-label text-xs uppercase tracking-widest text-on-surface-variant font-bold">Sequencing View</h3>
                <span class="font-label text-[10px] text-outline px-2 py-0.5 bg-surface-container-high rounded-full">{{ $steps->count() }} STEPS</span>
            </div>
            
            <div class="space-y-4" id="steps-list">
                @foreach($steps as $step)
                    <div data-id="{{ $step->id }}" 
                       class="block group {{ $activeStep && $activeStep->id == $step->id ? 'bg-surface-container-lowest border-l-4 border-primary shadow-sm ring-1 ring-outline-variant/20' : 'bg-surface-container-low border border-transparent hover:border-outline-variant/40' }} p-5 rounded-r-lg transition-all duration-300">
                        <div class="flex items-start gap-3">
                            <span class="step-handle material-symbols-outlined text-outline-variant group-hover:text-primary transition-colors cursor-grab active:cursor-grabbing">drag_indicator</span>
                            <div class="flex-1">
                                <a href="{{ route('admin.onboarding.index', ['step' => $step->id]) }}" class="block">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-label text-[10px] uppercase font-bold {{ $step->is_active ? 'text-primary' : 'text-on-surface-variant' }}">
                                            Step {{ $loop->iteration }}: {{ $step->is_active ? 'Active' : 'Draft' }}
                                        </span>
                                        @if($step->is_active)
                                            <span class="material-symbols-outlined text-primary text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                        @endif
                                    </div>
                                    <h4 class="font-headline font-semibold text-on-surface">{{ $step->title ?: 'Untitled Step' }}</h4>
                                    <p class="font-body text-xs text-on-surface-variant mt-1">{{ Str::title(str_replace('_', ' ', $step->type)) }}</p>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Step Editor (Right 8 Columns) -->
        <div class="col-span-12 lg:col-span-8 bg-surface-container-lowest p-10 rounded-xl shadow-sm ring-1 ring-outline-variant/20">
            @if($activeStep)
                <div class="flex justify-between items-start mb-8 border-b border-outline-variant/20 pb-6">
                    <div>
                        <h3 class="font-headline text-2xl text-on-surface">{{ $activeStep->title }} Editor</h3>
                        <p class="text-sm text-on-surface-variant">Editing configurations for Step {{ $steps->where('id', '<=', $activeStep->id)->count() }}</p>
                    </div>
                    <div class="flex gap-3">
                        <form id="delete-step-{{ $activeStep->id }}" action="{{ route('admin.onboarding.destroy', $activeStep) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                        <button type="button" @click="$dispatch('open-delete-modal', { 
                                    title: 'Delete Onboarding Step', 
                                    message: 'Permanent removal of this step from the flow. This action cannot be undone.', 
                                    action: { type: 'form', target: 'delete-step-{{ $activeStep->id }}' } 
                                })" 
                                class="bg-surface-container-high text-error px-5 py-2 rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-error/10 transition-colors">
                            Delete Step
                        </button>
                        <button type="button" @click="saveStep()" class="bg-primary text-on-primary px-8 py-2 rounded-lg font-bold text-xs uppercase tracking-widest shadow-md hover:opacity-90 transition-opacity">Save Step</button>
                    </div>
                </div>

                <form id="step-editor-form" action="{{ route('admin.onboarding.update', $activeStep) }}" method="POST" class="space-y-10">
                    @csrf
                    @method('PATCH')
                    
                    <!-- Basic Information -->
                    <div class="grid grid-cols-2 gap-8">
                        <div class="col-span-2 space-y-2">
                            <label class="font-label text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Step Title</label>
                            <input name="title" x-model="title" 
                                   @input="generateSlug()" 
                                   placeholder="e.g., Select your area of interest" 
                                   class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg p-3 text-on-surface focus:ring-2 focus:ring-primary focus:border-primary transition-all" type="text"/>
                            
                            <!-- Hidden Fields -->
                            <input type="hidden" name="slug" x-model="slug">
                            <input type="hidden" name="sort_order" value="{{ $activeStep->sort_order }}">
                        </div>
                        
                        <div class="col-span-2 space-y-2">
                            <label class="font-label text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Supporting Text</label>
                            <textarea name="description" placeholder="Briefly describe what the user should do on this step..." class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg p-3 text-on-surface focus:ring-2 focus:ring-primary focus:border-primary transition-all" rows="2">{{ $activeStep->description }}</textarea>
                        </div>
                        <div class="col-span-2 space-y-2">
                            <label class="font-label text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Step Type</label>
                            <select name="type" x-model="type" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg p-3 text-on-surface focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                                <option value="card_multi">Card (Multi-select)</option>
                                <option value="card_single">Card (Single choice)</option>
                                <option value="radio">Radio List</option>
                                <option value="multi_select">Multi-select List</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Content Modules -->
                    <template x-if="['card_multi', 'card_single', 'radio', 'multi_select'].includes(type)">
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <label class="font-label text-[10px] uppercase font-bold text-on-surface-variant tracking-widest" x-text="type.startsWith('card') ? 'Selectable Cards' : 'List Options'"></label>
                                <button @click="addCategory()" class="text-primary font-bold text-xs flex items-center gap-1" type="button">
                                    <span class="material-symbols-outlined text-sm">add</span> Add <span x-text="type.startsWith('card') ? 'Card' : 'Option'"></span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="(cat, index) in content.categories" :key="index">
                                    <div class="p-4 bg-surface-container-low rounded-lg border border-outline-variant/30 flex gap-4 items-center">
                                        <!-- Visual Indicator based on type -->
                                        <div class="shrink-0">
                                            <template x-if="type === 'card_multi' || type === 'card_single'">
                                                <div class="w-12 h-12 bg-surface-container-lowest rounded-lg flex items-center justify-center border border-outline-variant/20 shadow-sm">
                                                    <span class="material-symbols-outlined text-primary" x-text="cat.icon || 'label'"></span>
                                                </div>
                                            </template>
                                            <template x-if="type === 'radio'">
                                                <div class="w-5 h-5 rounded-full border-2 border-primary/30 flex items-center justify-center">
                                                    <div class="w-2.5 h-2.5 rounded-full bg-primary/10"></div>
                                                </div>
                                            </template>
                                            <template x-if="type === 'multi_select'">
                                                <div class="w-5 h-5 rounded border-2 border-primary/30 flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-[14px] text-primary/10">check</span>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Form Fields -->
                                        <div class="flex-1 space-y-1">
                                            <input x-model="cat.title" class="w-full bg-transparent border-none p-0 font-bold text-sm text-on-surface focus:ring-0" type="text" placeholder="Title"/>
                                            <template x-if="type.startsWith('card')">
                                                <input x-model="cat.desc" class="w-full bg-transparent border-none p-0 text-xs text-on-surface-variant focus:ring-0" type="text" placeholder="Description"/>
                                            </template>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center gap-2">
                                            <button @click="removeCategory(index)" class="text-outline hover:text-error transition-colors" type="button">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- 
                    <!-- Geographic Regions Section -->
                    <div class="space-y-6 pt-10 border-t border-outline-variant/10">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="font-label text-[10px] uppercase font-bold text-on-surface-variant tracking-widest text-primary">Geographic Regions</label>
                                <p class="text-[10px] text-on-surface-variant">Enable region-based selection for this step.</p>
                            </div>
                            <button @click="addRegion()" class="text-primary font-bold text-xs flex items-center gap-1" type="button">
                                <span class="material-symbols-outlined text-sm">add</span> Add Region
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <template x-for="(region, index) in content.regions" :key="index">
                                <div class="p-3 bg-surface-container-lowest rounded-lg border border-outline-variant/30 flex gap-3 items-center">
                                    <div class="flex-1">
                                        <input x-model="region.label" class="w-full bg-transparent border-none p-0 font-bold text-xs text-on-surface focus:ring-0" type="text" placeholder="Region Name"/>
                                        <input x-model="region.key" class="w-full bg-transparent border-none p-0 text-[9px] text-on-surface-variant focus:ring-0" type="text" placeholder="key (e.g. india)"/>
                                    </div>
                                    <button @click="removeRegion(index)" class="text-outline hover:text-error transition-colors" type="button">
                                        <span class="material-symbols-outlined text-xs">close</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    --}}


                    <!-- Global Toggles Section -->
                    <div class="pt-8 border-t border-outline-variant/20 flex items-center justify-between">
                        <div>
                            <h4 class="font-headline font-semibold text-lg text-on-surface">Status & Integration</h4>
                            <div class="flex gap-8 mt-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative inline-flex items-center">
                                        <input name="is_active" type="checkbox" value="1" {{ $activeStep->is_active ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    </div>
                                    <span class="text-sm font-medium text-stone-700">Active</span>
                                </label>
                                
                                {{-- 
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative inline-flex items-center">
                                        <input name="upsc_integration" type="checkbox" value="1" {{ $activeStep->upsc_integration ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    </div>
                                    <span class="text-sm font-medium text-stone-700">UPSC Integration</span>
                                </label>
                                --}}
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Content Input for JSON sync -->
                    <input type="hidden" name="content" :value="JSON.stringify(content)">
                </form>
            @else
                <div class="h-full flex flex-col items-center justify-center text-center p-20 bg-surface-container-low/30 rounded-xl border-2 border-dashed border-outline-variant/20">
                    <span class="material-symbols-outlined text-6xl text-outline-variant mb-4">account_tree</span>
                    <h3 class="font-headline text-2xl text-on-surface-variant">No Flow Selected</h3>
                    <p class="text-on-surface-variant mt-2 max-w-sm">Select a step from the sequencing view on the left or create a new flow to get started.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function onboardingEditor() {
    return {
        title: '{{ $activeStep ? $activeStep->title : "" }}',
        slug: '{{ $activeStep ? $activeStep->slug : "" }}',
        type: '{{ $activeStep ? $activeStep->type : "" }}',
        content: @json(array_merge(['categories' => [], 'regions' => []], (array)($activeStep ? $activeStep->content : []))),
        
        init() {
            this.initSortable();
        },

        initSortable() {
            const el = document.getElementById('steps-list');
            if (el) {
                Sortable.create(el, {
                    handle: '.step-handle',
                    animation: 150,
                    ghostClass: 'bg-primary/5',
                    onEnd: (evt) => {
                        const ids = Array.from(el.querySelectorAll('[data-id]')).map(item => item.dataset.id);
                        this.saveOrder(ids);
                    }
                });
            }
        },

        generateSlug() {
            this.slug = this.title.toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        },

        async saveOrder(ids) {
            try {
                const response = await fetch('{{ route("admin.onboarding.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids })
                });
                
                if (response.ok) {
                    // Update the visual sequence numbers if needed or just reload
                    window.location.reload();
                }
            } catch (error) {
                console.error('Failed to save order:', error);
            }
        },
        
        addCategory() {
            if (!this.content.categories) this.content.categories = [];
            this.content.categories.push({ 
                title: '', 
                desc: '', 
                icon: this.type.startsWith('card') ? 'label' : '' 
            });
        },
        
        removeCategory(index) {
            this.content.categories.splice(index, 1);
        },
        
        addRegion() {
            if (!this.content.regions) this.content.regions = [];
            this.content.regions.push({ 
                label: '', 
                key: ''
            });
        },
        
        removeRegion(index) {
            this.content.regions.splice(index, 1);
        },
        
        saveStep() {
            document.getElementById('step-editor-form').submit();
        }
    }
}
</script>
@endsection
