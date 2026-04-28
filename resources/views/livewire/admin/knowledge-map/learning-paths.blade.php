<div class="p-8 max-w-6xl mx-auto">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <a href="{{ route('admin.knowledge-maps.index') }}" class="text-stone-400 hover:text-primary transition-colors flex items-center gap-2 text-xs font-bold uppercase tracking-widest mb-4">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Back to Maps
            </a>
            <h2 class="text-3xl font-headline italic font-bold text-primary dark:text-stone-100">Learning Paths</h2>
            <p class="text-stone-400 text-[10px] font-bold uppercase tracking-widest mt-1">Curate guided journeys through the <span class="text-primary italic">{{ $map->title }}</span></p>
        </div>
        <button wire:click="openAddPath" class="bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add_road</span>
            New Learning Path
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar: Paths List -->
        <div class="lg:col-span-1 space-y-4">
            @forelse($paths as $path)
                <button wire:click="selectPath({{ $path->id }})" @class([
                    'w-full text-left p-6 rounded-2xl border transition-all group',
                    'bg-white border-primary shadow-lg shadow-primary/5' => $selectedPathId == $path->id,
                    'bg-white border-stone-100 hover:border-stone-200' => $selectedPathId != $path->id,
                ])>
                    <div class="flex items-start justify-between mb-2">
                        <span @class([
                            'text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded',
                            'bg-green-100 text-green-700' => $path->difficulty === 'beginner',
                            'bg-orange-100 text-orange-700' => $path->difficulty === 'intermediate',
                            'bg-red-100 text-red-700' => $path->difficulty === 'advanced',
                        ])>{{ $path->difficulty }}</span>
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click.stop="deletePath({{ $path->id }})" wire:confirm="Delete this learning path?" class="text-stone-400 hover:text-red-600 transition-colors">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </div>
                    <h4 class="font-headline font-bold text-stone-900 mb-1">{{ $path->title }}</h4>
                    <p class="text-[10px] text-stone-400 font-bold uppercase tracking-widest">{{ $path->nodes->count() }} Steps</p>
                </button>
            @empty
                <div class="p-12 bg-stone-50 rounded-[2.5rem] border border-dashed border-stone-200 text-center">
                    <span class="material-symbols-outlined text-4xl text-stone-300 mb-2">route</span>
                    <p class="text-xs font-bold text-stone-400 uppercase tracking-widest">No paths created</p>
                </div>
            @endforelse
        </div>

        <!-- Main: Selected Path Details -->
        <div class="lg:col-span-2">
            @if($selectedPath)
                <div class="bg-white rounded-[2.5rem] border border-stone-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-stone-50 bg-stone-50/30">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-headline font-bold italic text-primary">{{ $selectedPath->title }}</h3>
                                <p class="text-xs text-stone-500 mt-1 leading-relaxed">{{ $selectedPath->description ?: 'No description provided.' }}</p>
                            </div>
                        </div>
                        <div class="flex gap-6">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-stone-400 text-sm">timer</span>
                                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-widest">{{ $selectedPath->estimated_duration ?: 'Not set' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-stone-400 text-sm">bar_chart</span>
                                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-widest">{{ $selectedPath->difficulty }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-xs font-bold uppercase tracking-[0.2em] text-stone-400">Sequence of Study</h4>
                            <div class="flex items-center gap-2">
                                <select wire:model="selectedNodeIdToAdd" class="bg-stone-50 border-none rounded-xl text-[10px] font-bold uppercase tracking-widest focus:ring-2 focus:ring-primary/20">
                                    <option value="">Add node to path...</option>
                                    @foreach($availableNodes as $node)
                                        <option value="{{ $node->id }}">{{ $node->title }} ({{ $node->node_type }})</option>
                                    @endforeach
                                </select>
                                <button wire:click="addNodeToPath" class="size-8 bg-primary text-white rounded-lg flex items-center justify-center shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-all">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @forelse($selectedPath->nodes as $index => $node)
                                <div class="flex items-center gap-4 group">
                                    <div class="size-8 rounded-full bg-primary text-white flex items-center justify-center text-xs font-black shadow-md shadow-primary/10">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1 p-4 bg-stone-50 rounded-2xl flex items-center justify-between border border-transparent hover:border-primary/20 transition-all">
                                        <div class="flex items-center gap-4">
                                            <div class="size-10 rounded-xl bg-white flex items-center justify-center text-stone-400 shadow-sm">
                                                <span class="material-symbols-outlined text-sm">
                                                    {{ $node->node_type === 'thinker' ? 'person' : 'description' }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-stone-900">{{ $node->title }}</p>
                                                <p class="text-[9px] uppercase font-bold text-stone-400 tracking-widest">{{ $node->node_type }}</p>
                                            </div>
                                        </div>
                                        <button wire:click="removeNodeFromPath({{ $node->id }})" class="size-8 rounded-lg text-stone-300 hover:text-red-600 hover:bg-red-50 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <span class="material-symbols-outlined text-sm">close</span>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-12 text-center text-stone-300">
                                    <p class="text-[10px] font-bold uppercase tracking-widest">Select nodes above to build this path.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @else
                <div class="h-full flex flex-col items-center justify-center p-12 text-center bg-stone-50 rounded-[3rem] border border-stone-100">
                    <div class="size-20 rounded-full bg-white flex items-center justify-center text-stone-200 mb-4 shadow-sm">
                        <span class="material-symbols-outlined text-4xl">arrow_forward</span>
                    </div>
                    <h3 class="text-xl font-headline font-bold text-stone-400">Select a Learning Path</h3>
                    <p class="text-xs text-stone-400 mt-2">Choose an existing path or create a new one to start curating the curriculum.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Path Modal -->
    @if($showPathModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-sm">
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden">
                <div class="p-8 border-b border-stone-100 bg-stone-50/50">
                    <h3 class="text-xl font-headline font-bold italic text-primary">New Learning Path</h3>
                    <p class="text-[9px] uppercase font-bold tracking-widest text-stone-400 mt-1">Group nodes into a guided educational sequence</p>
                </div>
                <div class="p-8 space-y-6">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Path Title</label>
                        <input wire:model="pathTitle" type="text" placeholder="e.g. History of Social Theory" class="w-full bg-stone-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Description</label>
                        <textarea wire:model="pathDescription" rows="3" class="w-full bg-stone-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Difficulty</label>
                            <select wire:model="pathDifficulty" class="w-full bg-stone-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-2">Est. Duration</label>
                            <input wire:model="pathDuration" type="text" placeholder="e.g. 4 Hours" class="w-full bg-stone-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20">
                        </div>
                    </div>
                </div>
                <div class="p-8 bg-stone-50/50 border-t border-stone-100 flex justify-end gap-3">
                    <button wire:click="$set('showPathModal', false)" class="px-6 py-2.5 text-stone-600 text-[10px] font-bold uppercase tracking-widest hover:underline">Cancel</button>
                    <button wire:click="savePath" class="px-8 py-2.5 bg-primary text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-all">Create Path</button>
                </div>
            </div>
        </div>
    @endif
</div>
