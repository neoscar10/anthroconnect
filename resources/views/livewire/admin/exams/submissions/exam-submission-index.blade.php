<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Exam Submissions</h1>
            <p class="text-gray-600">Track and evaluate student answer writing practice.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100 flex flex-col md:flex-row gap-4 justify-between">
            <div class="relative flex-1 max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <span class="material-symbols-outlined text-sm">search</span>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by user or question..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
            
            <div class="flex gap-4">
                <select wire:model.live="status" class="block w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                </select>

                <select wire:model.live="question_kind" class="block w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Question Types</option>
                    <option value="model">Model Questions</option>
                    <option value="past">Past Questions</option>
                </select>
                
                <select wire:model.live="perPage" class="block w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto pb-32">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] font-bold tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">Student</th>
                        <th class="px-6 py-4 whitespace-nowrap">Question</th>
                        <th class="px-6 py-4 whitespace-nowrap">Stats</th>
                        <th class="px-6 py-4 whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 whitespace-nowrap">Date</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($submissions as $submission)
                        <tr class="hover:bg-gray-50/50 transition-all duration-300 group {{ $submission->evaluated_at ? 'bg-white' : ($submission->status === 'submitted' ? 'bg-orange-50/30' : 'bg-white') }}">
                            <td class="px-6 py-4 whitespace-nowrap border-l-4 {{ $submission->evaluated_at ? 'border-blue-600' : ($submission->status === 'submitted' ? 'border-orange-500 animate-pulse' : 'border-stone-100') }}">
                                <a wire:navigate href="{{ route('admin.exams.submissions.show', $submission->id) }}" class="flex items-center gap-3 group">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs group-hover:bg-blue-600 group-hover:text-white transition-all">
                                        {{ substr($submission->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $submission->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $submission->user->email }}</div>
                                    </div>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a wire:navigate href="{{ route('admin.exams.submissions.show', $submission->id) }}" class="flex flex-col group/q">
                                    <span class="block max-w-xs truncate text-sm font-medium text-gray-700 group-hover/q:text-blue-600 transition-colors" title="{{ strip_tags($submission->question->question_text) }}">
                                        {{ strip_tags($submission->question->question_text) }}
                                    </span>
                                    @if($submission->evaluated_at)
                                        <div class="flex items-center gap-2 mt-1">
                                            <div class="flex -space-x-1">
                                                @for($i = 0; $i < 5; $i++)
                                                    <span class="material-symbols-outlined text-[10px] {{ $i < ($submission->score / ($submission->question->marks ?: 100) * 5) ? 'text-blue-600' : 'text-gray-200' }}">star</span>
                                                @endfor
                                            </div>
                                            <span class="text-[9px] font-bold text-blue-600 uppercase tracking-widest">{{ $submission->score }} / {{ $submission->question->marks }}</span>
                                        </div>
                                    @endif
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-[10px] space-y-1">
                                    <div class="flex items-center gap-1.5 text-gray-600">
                                        <span class="material-symbols-outlined text-xs">history</span>
                                        <span>Attempt #{{ $submission->attempts_count }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-gray-600">
                                        <span class="material-symbols-outlined text-xs">timer</span>
                                        <span>{{ floor($submission->time_spent_seconds / 60) }}m {{ $submission->time_spent_seconds % 60 }}s</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                @if($submission->evaluated_at)
                                    <div class="flex flex-col gap-1">
                                        <span class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-blue-600 text-white flex items-center gap-2 w-fit shadow-md shadow-blue-900/20">
                                            <span class="material-symbols-outlined text-[14px]">verified</span>
                                            Evaluated
                                        </span>
                                        <span class="text-[8px] text-gray-400 font-bold uppercase tracking-widest ml-1">Archive Ready</span>
                                    </div>
                                @elseif($submission->status === 'submitted')
                                    <div class="flex flex-col gap-1">
                                        <span class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-orange-500 text-white flex items-center gap-2 w-fit shadow-md shadow-orange-900/20">
                                            <span class="material-symbols-outlined text-[14px]">priority_high</span>
                                            Pending
                                        </span>
                                        <span class="text-[8px] text-orange-600 font-bold uppercase tracking-widest ml-1 animate-pulse">Awaiting Faculty</span>
                                    </div>
                                @else
                                    <span class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest bg-stone-100 text-stone-500 border border-stone-200 flex items-center gap-2 w-fit">
                                        <span class="material-symbols-outlined text-[14px]">edit_note</span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                {{ $submission->updated_at->format('M d, Y') }}
                                <div class="text-[10px] opacity-75">{{ $submission->updated_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" type="button" class="p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-400 hover:text-gray-600">
                                        <span class="material-symbols-outlined">more_vert</span>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-100 z-[100] py-2 overflow-hidden text-left">
                                        <a wire:navigate href="{{ route('admin.exams.submissions.show', $submission->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all">
                                            <span class="material-symbols-outlined text-sm">rate_review</span>
                                            Review Assessment
                                        </a>
                                        <button wire:click="deleteSubmission({{ $submission->id }})" wire:confirm="Are you sure? This will delete the student's work forever." class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-all">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                            Delete Submission
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-400">
                                    <span class="material-symbols-outlined text-4xl">inventory_2</span>
                                    <p class="text-sm font-medium">No submissions found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            {{ $submissions->links() }}
        </div>
    </div>
</div>
