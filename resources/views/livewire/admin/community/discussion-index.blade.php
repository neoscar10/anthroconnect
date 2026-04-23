<div>
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="font-headline text-4xl font-bold italic text-on-surface">Community Discussions</h2>
            <p class="text-on-surface-variant font-body mt-1">Moderate scholarly inquiries and community highlights.</p>
        </div>
        <div class="flex gap-4">
            <div class="bg-surface-container-lowest p-1 rounded-xl shadow-sm border border-outline-variant/10 flex">
                <button wire:click="$set('status_filter', '')" class="px-4 py-2 text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all {{ $status_filter === '' ? 'bg-primary text-white' : 'text-stone-400 hover:text-stone-600' }}">All</button>
                <button wire:click="$set('status_filter', 'published')" class="px-4 py-2 text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all {{ $status_filter === 'published' ? 'bg-primary text-white' : 'text-stone-400 hover:text-stone-600' }}">Published</button>
                <button wire:click="$set('status_filter', 'hidden')" class="px-4 py-2 text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all {{ $status_filter === 'hidden' ? 'bg-primary text-white' : 'text-stone-400 hover:text-stone-600' }}">Hidden</button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm border border-outline-variant/10">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Search Discussions</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-300 text-sm">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Title or content..." class="w-full bg-stone-50 border-none rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary">
            </div>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm border border-outline-variant/10">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Filter by Topic</label>
            <select wire:model.live="topic_filter" class="w-full bg-stone-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary py-2 px-3">
                <option value="">All Topics</option>
                @foreach($topics as $topic)
                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm border border-outline-variant/10 flex items-center justify-around">
             <div class="text-center">
                 <p class="text-2xl font-bold text-stone-800">{{ $discussions->total() }}</p>
                 <p class="text-[9px] font-bold uppercase text-stone-400 tracking-widest">Total Threads</p>
             </div>
             <div class="w-px h-10 bg-stone-100"></div>
             <div class="text-center">
                 <p class="text-2xl font-bold text-primary">{{ \App\Models\Community\CommunityDiscussion::where('is_expert_spotlight', true)->count() }}</p>
                 <p class="text-[9px] font-bold uppercase text-stone-400 tracking-widest">Expert Spots</p>
             </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/20">
        <table class="w-full text-left border-collapse">
            <thead class="bg-stone-50/50">
                <tr>
                    <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[9px] font-bold">Scholar & Discussion</th>
                    <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[9px] font-bold">Status</th>
                    <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[9px] font-bold text-center">Featured</th>
                    <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[9px] font-bold text-center">Expert</th>
                    <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[9px] font-bold text-center">Engagement</th>
                    <th class="px-6 py-4 text-on-surface-variant uppercase tracking-widest text-[9px] font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                @foreach($discussions as $disc)
                    <tr class="hover:bg-primary/5 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-full bg-stone-100 flex items-center justify-center text-stone-400">
                                    <span class="material-symbols-outlined text-sm">person</span>
                                </div>
                                <div class="max-w-md">
                                    <p class="font-bold text-stone-900 leading-tight group-hover:text-primary transition-colors">{{ $disc->title }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-stone-400">{{ $disc->author?->name ?? 'Unknown Author' }}</span>
                                        <span class="w-1 h-1 rounded-full bg-stone-300"></span>
                                        <span class="text-[9px] font-bold text-primary italic">{{ $disc->topic?->name }}</span>
                                        <span class="w-1 h-1 rounded-full bg-stone-300"></span>
                                        <span class="text-[9px] font-medium text-stone-400">{{ $disc->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <select wire:change="updateStatus({{ $disc->id }}, $event.target.value)" 
                                class="text-[10px] font-bold uppercase tracking-widest border-none bg-stone-50 rounded-lg py-1 px-2 cursor-pointer focus:ring-0">
                                <option value="published" {{ $disc->status === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="hidden" {{ $disc->status === 'hidden' ? 'selected' : '' }}>Hidden</option>
                                <option value="archived" {{ $disc->status === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="toggleFeatured({{ $disc->id }})" class="p-1 rounded-lg transition-colors {{ $disc->is_featured ? 'text-orange-500 bg-orange-50' : 'text-stone-300 hover:text-stone-400' }}">
                                <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' {{ $disc->is_featured ? 1 : 0 }};">star</span>
                            </button>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="toggleExpert({{ $disc->id }})" class="p-1 rounded-lg transition-colors {{ $disc->is_expert_spotlight ? 'text-primary bg-primary/10' : 'text-stone-300 hover:text-stone-400' }}">
                                <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' {{ $disc->is_expert_spotlight ? 1 : 0 }};">verified</span>
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-4 text-stone-400">
                                <div class="text-center">
                                    <p class="text-xs font-bold text-stone-600">{{ $disc->replies_count }}</p>
                                    <p class="text-[8px] uppercase tracking-tighter">Replies</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs font-bold text-stone-600">{{ $disc->likes_count }}</p>
                                    <p class="text-[8px] uppercase tracking-tighter">Likes</p>
                                </div>
                            </div>
                        </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.community.discussions.show', $disc->id) }}" class="p-2 text-primary hover:bg-gray-100 rounded-lg transition-colors" title="Moderate Dialogue">
                                        <span class="material-symbols-outlined text-sm">forum</span>
                                    </a>
                                    <button wire:click="toggleFeatured({{ $disc->id }})" class="p-2 {{ $disc->is_featured ? 'text-orange-500' : 'text-gray-400' }} hover:bg-gray-100 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-sm">star</span>
                                    </button>
                                    <button wire:click="editDiscussion({{ $disc->id }})" class="p-2 text-gray-400 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </button>
                                </div>
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        {{ $discussions->links() }}
    </div>
</div>
