@extends('layouts.admin')

@section('content')
<div x-data="topicManager()" class="relative">
    
    <!-- Info Banner (Success Message) -->
    @if(session('success'))
        <div class="mb-8 px-6 py-4 bg-primary/10 text-primary rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center justify-between border border-primary/20 animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                {{ session('success') }}
            </div>
            <button @click="location.reload()" class="hover:rotate-180 transition-transform duration-500">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-8 px-6 py-4 bg-error/10 text-error rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center justify-between border border-error/20 animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-sm">error</span>
                {{ session('error') }}
            </div>
            <button @click="location.reload()" class="hover:rotate-180 transition-transform duration-500">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex justify-between items-end mb-12">
        <div>
            <h1 class="font-headline text-4xl text-on-surface mb-2 italic">Global Topics Management</h1>
            <p class="font-body text-on-surface-variant text-lg">Organize shared taxonomies used across editorial, research, and community experiences.</p>
        </div>
        <button @click="openModal()" class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-[10px] flex items-center gap-2 shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">add</span>
            Create New Topic
        </button>
    </div>

    <!-- Info Banner -->
    <div class="bg-secondary-container/30 border border-secondary/10 p-6 rounded-3xl mb-12 flex gap-6 items-center">
        <div class="w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-secondary">schema</span>
        </div>
        <div>
            <h4 class="font-bold text-secondary text-sm mb-1 uppercase tracking-widest">Shared Architecture</h4>
            <p class="text-on-surface-variant text-sm leading-relaxed">
                Topics created here help organize content across multiple AnthroConnect modules. They represent the foundational categories for our archives and public narratives.
            </p>
        </div>
    </div>

    <!-- Topics Content -->
    <div class="bg-surface-container-lowest rounded-3xl shadow-sm border border-outline-variant/10 overflow-hidden">
        <form id="filterForm" action="{{ route('admin.topics.index') }}" method="GET" class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/20">
            <div class="flex items-center gap-4">
                <span class="material-symbols-outlined text-stone-400">filter_list</span>
                <input name="search" value="{{ request('search') }}" onchange="document.getElementById('filterForm').submit()" type="text" placeholder="Search taxonomies..." class="bg-transparent border-none focus:ring-0 text-sm w-64 placeholder-stone-400">
            </div>
            <div class="flex gap-2">
                <select name="status" onchange="document.getElementById('filterForm').submit()" class="bg-surface-container-low border-none rounded-xl px-4 py-2 text-xs font-bold focus:ring-2 focus:ring-primary cursor-pointer">
                    <option value="" {{ request('status') === '' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low/50">
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em]">Topic Details</th>
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em]">Summary</th>
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em]">Visibility</th>
                        <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($topics as $topic)
                        <tr class="hover:bg-surface-container-low/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="font-headline text-lg font-bold text-on-surface italic leading-tight">{{ $topic->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 max-w-sm">
                                <p class="text-xs text-on-surface-variant line-clamp-2 leading-relaxed italic">
                                    {{ $topic->short_description ?: 'No categorical description provided.' }}
                                </p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-2">
                                    @if($topic->is_members_only)
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[12px] text-secondary">lock</span>
                                            <span class="text-[9px] font-bold uppercase tracking-widest text-secondary">Members Only</span>
                                        </div>
                                    @else
                                        <form action="{{ route('admin.topics.toggle-status', $topic) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="flex items-center gap-2 group/status">
                                                <div class="w-2 h-2 rounded-full {{ $topic->is_active ? 'bg-primary shadow-[0_0_8px_rgba(80,101,42,0.4)]' : 'bg-stone-300' }}"></div>
                                                <span class="text-[10px] font-bold uppercase tracking-widest {{ $topic->is_active ? 'text-primary' : 'text-stone-400' }}">
                                                    {{ $topic->is_active ? 'Public' : 'Hidden' }}
                                                </span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right whitespace-nowrap overflow-visible">
                                <div x-data="{ open: false }" class="relative inline-flex justify-end items-center">
                                    <button @click="open = !open" @click.away="open = false" class="p-2 text-stone-400 hover:text-on-surface transition-colors rounded-full hover:bg-surface-container-high transition-colors">
                                        <span class="material-symbols-outlined text-sm">more_vert</span>
                                    </button>
                                    
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
                                         x-cloak
                                         class="absolute right-0 top-1/2 -translate-y-1/2 mr-10 w-48 bg-surface-container-lowest rounded-xl shadow-2xl border border-outline-variant/20 z-[100] overflow-hidden text-left">
                                        <button type="button" @click="open = false; openModal({{ $topic->id }})" class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm text-primary">edit_note</span>
                                            Update Topic
                                        </button>
                                        
                                        <form id="delete-topic-{{ $topic->id }}" action="{{ route('admin.topics.destroy', $topic) }}" method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button" @click="open = false; $dispatch('open-delete-modal', { 
                                                    title: 'Archive Topic', 
                                                    message: 'Deleting this topic will remove it from all research categorizations. Proceed?', 
                                                    action: { type: 'form', target: 'delete-topic-{{ $topic->id }}' } 
                                                })" 
                                                class="w-full text-left px-4 py-3 text-[10px] uppercase tracking-widest font-bold text-error hover:bg-error/5 transition-colors flex items-center gap-2 border-t border-outline-variant/10">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                            Archive Topic
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-32 text-center">
                                <div class="flex flex-col items-center opacity-40">
                                    <span class="material-symbols-outlined text-6xl mb-4">dataset</span>
                                    <p class="font-headline text-2xl italic">The archives are thin...</p>
                                    <p class="text-xs uppercase tracking-widest mt-2">No taxonomy topics identified matching your search.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($topics->hasPages())
            <div class="px-8 py-6 border-t border-outline-variant/10 bg-surface-container-low/20">
                {{ $topics->links() }}
            </div>
        @endif
    </div>

    <!-- Topic Management Modal -->
    <div x-show="modalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
         x-cloak>
        <div x-show="modalOpen" 
             style="background: rgba(12, 10, 9, 0.4); backdrop-filter: blur(8px);"
             class="fixed inset-0 transition-opacity"
             @click="closeModal()"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div x-show="modalOpen"
             class="bg-surface-container-lowest rounded-[32px] shadow-2xl ring-1 ring-white/10 w-full max-w-2xl overflow-hidden relative z-10 flex flex-col"
             x-transition:enter="ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0 translate-y-4"
             x-transition:enter-end="scale-100 opacity-100 translate-y-0"
             x-transition:leave="ease-in duration-200 transform"
             x-transition:leave-start="scale-100 opacity-100 translate-y-0"
             x-transition:leave-end="scale-95 opacity-0 translate-y-4">
            
            <form :action="editingTopicId ? `/admin/topics/${editingTopicId}` : '{{ route('admin.topics.store') }}'" method="POST">
                @csrf
                <template x-if="editingTopicId">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/30">
                    <h4 class="font-headline text-2xl text-on-surface italic font-bold" x-text="editingTopicId ? 'Modify Topic' : 'New Taxonomy Topic'"></h4>
                    <button type="button" @click="closeModal()" class="text-stone-400 hover:text-on-surface transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-10 space-y-8">
                    @if ($errors->any())
                        <div class="p-4 bg-error/10 text-error rounded-xl text-sm mb-4">
                            Please check the form for errors.
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Topic Name</label>
                        <input name="name" x-model="form.name" type="text" required class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl p-5 text-lg font-headline italic font-bold text-on-surface focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none" placeholder="e.g. Cultural Anthropology">
                        @error('name') <span class="text-[10px] text-error font-medium px-4">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest px-1">Description</label>
                        <textarea name="short_description" x-model="form.short_description" rows="3" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none resize-none" placeholder="How should this topic be understood in the context of our research?"></textarea>
                        @error('short_description') <span class="text-[10px] text-error font-medium px-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-outline-variant/10">
                        <div class="flex items-center gap-10">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative inline-flex items-center">
                                    <input name="is_active" type="checkbox" value="1" x-model="form.is_active" class="sr-only peer">
                                    <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </div>
                                <span class="text-[10px] font-bold text-on-surface uppercase tracking-widest">Active</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative inline-flex items-center">
                                    <input name="is_members_only" type="checkbox" value="1" x-model="form.is_members_only" class="sr-only peer">
                                    <div class="w-11 h-6 bg-stone-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                                </div>
                                <span class="text-[10px] font-bold text-on-surface uppercase tracking-widest">Members Only Access</span>
                            </label>
                        </div>

                        <div class="flex gap-4">
                            <button type="button" @click="closeModal()" class="px-8 py-4 rounded-2xl text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:bg-surface-container-high transition-all">Cancel</button>
                            <button type="submit" class="bg-primary text-on-primary px-10 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:-translate-y-0.5 active:translate-y-0 transition-all">
                                <span x-text="editingTopicId ? 'Update' : 'Create'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function topicManager() {
        return {
            modalOpen: @json($errors->any()),
            editingTopicId: null,
            topics: @json($topics->items()),
            form: {
                name: '',
                short_description: '',
                is_active: true,
                is_members_only: false
            },

            openModal(id = null) {
                this.editingTopicId = id;
                if (id) {
                    const topic = this.topics.find(t => t.id === id);
                    if (topic) {
                        this.form.name = topic.name;
                        this.form.short_description = topic.short_description || '';
                        this.form.is_active = topic.is_active ? true : false;
                        this.form.is_members_only = topic.is_members_only ? true : false;
                    }
                } else {
                    this.form.name = '';
                    this.form.short_description = '';
                    this.form.is_active = true;
                    this.form.is_members_only = false;
                }
                this.modalOpen = true;
                document.body.style.overflow = 'hidden';
            },
            
            closeModal() {
                this.modalOpen = false;
                document.body.style.overflow = 'auto';
            }
        }
    }
</script>
@endsection
