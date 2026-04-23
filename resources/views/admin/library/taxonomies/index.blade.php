@extends('layouts.admin')

@section('content')
<div x-data="taxonomyManager()" class="space-y-12">
    <!-- Header -->
    <div class="flex justify-between items-end">
        <div>
            <h1 class="font-headline text-4xl text-on-surface mb-2 italic">{{ $title }} Management</h1>
            <p class="font-body text-on-surface-variant text-lg">Define shared classification terms for the research library.</p>
        </div>
        <button @click="openModal()" class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-[10px] flex items-center gap-2 shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">add</span>
            Add {{ Str::singular($title) }}
        </button>
    </div>

    <!-- Info Banner -->
    @if(session('success'))
        <div class="px-6 py-4 bg-primary/10 text-primary rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center justify-between border border-primary/20">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="px-6 py-4 bg-error/10 text-error rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center justify-between border border-error/20">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-sm">error</span>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Content -->
    <div class="bg-white rounded-[40px] border border-outline-variant/10 overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-low/50">
                <tr>
                    <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Name</th>
                    <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Slug</th>
                    <th class="px-8 py-5 text-[10px] uppercase font-bold text-on-surface-variant tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                @forelse($taxonomies as $tax)
                    <tr class="hover:bg-surface-container-low/30 transition-colors group">
                        <td class="px-8 py-6">
                            <span class="font-headline text-lg font-bold text-on-surface italic leading-tight">{{ $tax->name }}</span>
                            @if(isset($tax->description))
                                <p class="text-[10px] text-stone-400 mt-1 line-clamp-1 italic">{{ $tax->description }}</p>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-[10px] font-mono text-stone-500 bg-stone-100 px-2 py-1 rounded-md">{{ $tax->slug }}</span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <button @click="openModal({{ json_encode($tax) }})" class="p-2 text-stone-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                                <form id="delete-form-{{ $tax->id }}" action="{{ route($routePrefix . '.destroy', $tax) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button" @click="$dispatch('open-delete-modal', { 
                                            title: 'Delete {{ Str::singular($title) }}?', 
                                            message: 'Are you sure you want to remove this classification? This may affect linked resources.', 
                                            action: { type: 'form', target: 'delete-form-{{ $tax->id }}' } 
                                        })" class="p-2 text-stone-400 hover:text-error transition-colors">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-24 text-center text-stone-400 italic">No entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-12" x-cloak>
        <div class="fixed inset-0 bg-stone-900/60 backdrop-blur-md" @click="closeModal()"></div>
        <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
            <form :action="editingId ? `{{ route($routePrefix . '.index') }}/${editingId}` : '{{ route($routePrefix . '.store') }}'" method="POST">
                @csrf
                <template x-if="editingId">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-8 border-b border-outline-variant/10 bg-surface-container-low/30 flex justify-between items-center shrink-0">
                    <h4 class="font-headline text-2xl italic font-bold" x-text="editingId ? 'Edit {{ Str::singular($title) }}' : 'New {{ Str::singular($title) }}'"></h4>
                    <button type="button" @click="closeModal()" class="text-stone-400 hover:text-on-surface">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-8 space-y-6 overflow-y-auto flex-1">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Name</label>
                        <input name="name" x-model="form.name" type="text" required class="w-full bg-surface-container-low border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-primary outline-none">
                    </div>



                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-on-surface-variant tracking-widest">Description</label>
                        <textarea name="description" x-model="form.description" rows="3" class="w-full bg-surface-container-low border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-primary outline-none resize-none"></textarea>
                    </div>

                    <div class="flex items-center gap-3 pt-4">
                        <input type="checkbox" name="is_active" x-model="form.is_active" class="rounded text-primary focus:ring-primary">
                        <span class="text-[10px] font-bold uppercase tracking-widest">Active & Visible</span>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <button type="button" @click="closeModal()" class="flex-1 py-4 text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:bg-stone-100 rounded-2xl transition-all">Cancel</button>
                        <button type="submit" class="flex-1 bg-primary text-on-primary py-4 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-primary/20">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function taxonomyManager() {
        return {
            modalOpen: false,
            editingId: null,
            form: {
                name: '',
                description: '',
                is_active: true
            },
            openModal(tax = null) {
                if (tax) {
                    this.editingId = tax.id;
                    this.form.name = tax.name;
                    this.form.description = tax.description || '';
                    this.form.is_active = tax.is_active;
                } else {
                    this.editingId = null;
                    this.form.name = '';
                    this.form.description = '';
                    this.form.is_active = true;
                }
                this.modalOpen = true;
            },
            closeModal() {
                this.modalOpen = false;
            }
        }
    }
</script>
@endsection
