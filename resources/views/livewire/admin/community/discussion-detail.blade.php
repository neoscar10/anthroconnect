<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.community.discussions') }}" class="text-xs font-medium text-gray-500 hover:text-primary">Discussions</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-gray-400 text-sm mx-1">chevron_right</span>
                            <span class="text-xs font-medium text-gray-700">Moderation</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900 line-clamp-1">{{ $discussion->title }}</h1>
            <p class="text-sm text-gray-500">By {{ $discussion->author?->name }} • {{ $discussion->topic?->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('community.show', $discussion->slug) }}" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all border border-gray-200">
                <span class="material-symbols-outlined text-sm">visibility</span>
                View Public
            </a>
        </div>
    </div>

    <!-- Replies Management -->
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="font-bold text-gray-900">Scholar Contributions ({{ $discussion->replies_count }})</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 italic">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Scholar</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Contribution Body</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Metrics</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($replies as $reply)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200">
                                        <img src="{{ $reply->author?->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($reply->author?->name ?? 'User') }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900">{{ $reply->author?->name }}</span>
                                        <span class="text-[10px] text-gray-400 uppercase tracking-tight">{{ $reply->published_at?->format('M d, H:i') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-md">
                                <p class="text-xs text-gray-600 line-clamp-2 italic">"{{ $reply->body }}"</p>
                                @if($reply->parent_id)
                                    <span class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-500 text-[9px] font-bold rounded-lg uppercase tracking-tight">
                                        <span class="material-symbols-outlined text-[10px]">subdirectory_arrow_right</span>
                                        Threaded
                                    </span>
                                @endif
                                @if($reply->is_expert_reply)
                                    <span class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 bg-primary/10 text-primary text-[9px] font-bold rounded-lg uppercase tracking-tight border border-primary/20">
                                        <span class="material-symbols-outlined text-[10px]">verified</span>
                                        Expert Insight
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5 text-gray-500">
                                        <span class="material-symbols-outlined text-[12px]">thumb_up</span>
                                        <span class="text-[10px] font-bold">{{ $reply->upvotes_count }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-gray-400">
                                        <span class="material-symbols-outlined text-[12px]">forum</span>
                                        <span class="text-[10px] font-bold">{{ $reply->replies_count }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($reply->status === 'published')
                                    <span class="px-2 py-1 bg-green-50 text-green-700 text-[9px] font-bold uppercase tracking-widest rounded-lg border border-green-200">Published</span>
                                @else
                                    <span class="px-2 py-1 bg-red-50 text-red-700 text-[9px] font-bold uppercase tracking-widest rounded-lg border border-red-200">Hidden</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="toggleExpert({{ $reply->id }})" class="p-2 {{ $reply->is_expert_reply ? 'text-primary' : 'text-gray-400' }} hover:bg-gray-100 rounded-lg transition-colors" title="Toggle Expert Insight">
                                        <span class="material-symbols-outlined text-sm">verified</span>
                                    </button>
                                    <button wire:click="toggleReplyStatus({{ $reply->id }})" class="p-2 {{ $reply->status === 'published' ? 'text-gray-400' : 'text-orange-600' }} hover:bg-gray-100 rounded-lg transition-colors" title="Toggle Visibility">
                                        <span class="material-symbols-outlined text-sm">{{ $reply->status === 'published' ? 'visibility' : 'visibility_off' }}</span>
                                    </button>
                                    <button wire:click="deleteReply({{ $reply->id }})" wire:confirm="Are you sure you want to delete this contribution?" class="p-2 text-gray-400 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors" title="Delete Contribution">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <span class="material-symbols-outlined text-4xl text-gray-200 mb-4">forum</span>
                                <p class="text-gray-400 text-sm font-medium">No scholarship contributions found for this inquiry.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $replies->links() }}
        </div>
    </div>
</div>
