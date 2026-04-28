@props([
    'selectedTags' => [],
    'groups' => [],
    'label' => 'Tags',
    'name' => 'tags'
])

@php
    $selectedIds = collect($selectedTags)->pluck('id')->toArray();
    if (empty($groups)) {
        $groups = \App\Models\TagGroup::active()->with('activeTags')->orderBy('display_order')->get();
    }
    $instanceId = $attributes->get('id', 'tag-selector-' . Str::random(8));
@endphp

<div id="{{ $instanceId }}"
     @set-tags.window="if ($event.detail.id === '{{ $instanceId }}') updateSelected($event.detail.tags)"
     x-data="{ 
    selected: @js($selectedIds),
    activeGroupIds: [],
    init() {
        // Find which groups have selected tags and activate them
        const groups = @js($groups);
        this.activeGroupIds = groups
            .filter(g => g.active_tags.some(t => this.selected.includes(t.id)))
            .map(g => g.id);

        this.$watch('selected', value => {
            this.$dispatch('change', value);
            this.$dispatch('input', value);
        });
    },
    updateSelected(ids) {
        this.selected = Array.isArray(ids) ? ids : [];
        // Re-evaluate active groups when tags are externally set
        const groups = @js($groups);
        this.activeGroupIds = groups
            .filter(g => g.active_tags.some(t => ids.includes(t.id)))
            .map(g => g.id);
    },
    toggleGroup(groupId) {
        if (this.activeGroupIds.includes(groupId)) {
            // Optional: confirm if removing a group with selected tags
            const groupTags = this.getGroupTagIdsById(groupId);
            this.selected = this.selected.filter(id => !groupTags.includes(id));
            this.activeGroupIds = this.activeGroupIds.filter(id => id !== groupId);
        } else {
            this.activeGroupIds.push(groupId);
        }
    },
    toggleTag(tagId, selectionType) {
        if (selectionType === 'single_select') {
            const groupTagIds = this.getGroupTagIds(tagId);
            this.selected = this.selected.filter(id => !groupTagIds.includes(id));
            this.selected.push(tagId);
        } else {
            const index = this.selected.indexOf(tagId);
            if (index > -1) {
                this.selected = this.selected.filter(id => id !== tagId);
            } else {
                this.selected.push(tagId);
            }
        }
    },
    getGroupTagIds(tagId) {
        const groups = @js($groups);
        for (const group of groups) {
            const tagIds = group.active_tags.map(t => t.id);
            if (tagIds.includes(tagId)) return tagIds;
        }
        return [];
    },
    getGroupTagIdsById(groupId) {
        const groups = @js($groups);
        const group = groups.find(g => g.id === groupId);
        return group ? group.active_tags.map(t => t.id) : [];
    }
}" class="space-y-6">
    <div class="space-y-4">
        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-400">1. Select Taxonomy Dimensions</label>
        <div class="flex flex-wrap gap-2">
            @foreach($groups as $group)
                <button type="button" 
                        @click="toggleGroup({{ $group->id }})"
                        :class="activeGroupIds.includes({{ $group->id }}) 
                            ? 'bg-secondary text-white border-secondary' 
                            : 'bg-stone-50 text-stone-500 border-stone-200 hover:border-stone-400'"
                        class="px-4 py-2 rounded-xl border text-[10px] font-bold uppercase tracking-widest transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm" x-text="activeGroupIds.includes({{ $group->id }}) ? 'check_box' : 'add_box'"></span>
                    {{ $group->name }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="space-y-4" x-show="activeGroupIds.length > 0" x-cloak x-transition>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 italic">2. Assign Specific Tags</label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($groups as $group)
                <div x-show="activeGroupIds.includes({{ $group->id }})" 
                     class="space-y-3 p-5 bg-white rounded-2xl border border-outline-variant/10 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-xs font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-stone-400">sell</span>
                            {{ $group->name }}
                        </h4>
                        <span class="text-[8px] font-bold uppercase tracking-tighter px-2 py-0.5 rounded {{ $group->selection_type == 'single_select' ? 'bg-secondary/10 text-secondary' : 'bg-primary/10 text-primary' }}">
                            {{ $group->selection_type == 'single_select' ? 'Single' : 'Multi' }}
                        </span>
                    </div>
                    
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($group->activeTags as $tag)
                            <button type="button" 
                                    @click="toggleTag({{ $tag->id }}, '{{ $group->selection_type }}')"
                                    :class="selected.includes({{ $tag->id }}) 
                                        ? 'bg-primary text-on-primary border-primary' 
                                        : 'bg-stone-50 text-stone-600 border-stone-200 hover:border-primary/50'"
                                    class="px-3 py-1.5 rounded-lg border text-[10px] font-medium transition-all flex items-center gap-1.5">
                                {{ $tag->name }}
                                <span x-show="selected.includes({{ $tag->id }})" class="material-symbols-outlined text-[10px]">check</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Hidden Inputs for Form Submission -->
    <div class="hidden">
        <template x-for="id in selected" :key="id">
            <input type="hidden" name="{{ $name }}[]" :value="id">
        </template>
    </div>
</div>
