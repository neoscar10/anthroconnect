<div class="p-6 max-w-6xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a wire:navigate href="{{ route('admin.exams.submissions.index') }}" class="w-10 h-10 rounded-xl border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors">
                <span class="material-symbols-outlined text-gray-500">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-headline italic">Review Submission</h1>
                <p class="text-sm text-gray-500">Evaluating work from <strong>{{ $submission->user->name }}</strong></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-xl bg-orange-50 text-orange-800 text-[10px] font-bold uppercase tracking-widest border border-orange-100">
                Attempt #{{ $submission->attempts_count }}
            </span>
            <span class="px-3 py-1.5 rounded-xl {{ $submission->status === 'submitted' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-gray-50 text-gray-600 border-gray-200' }} text-[10px] font-bold uppercase tracking-widest border">
                {{ $submission->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Question Box -->
            <div class="bg-white border border-gray-200 rounded-[2rem] p-8 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">The Question</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 leading-snug">
                    {!! $submission->question->question_text !!}
                </h3>
            </div>

            <!-- Student Answer -->
            <div class="bg-white border border-gray-200 rounded-[2rem] shadow-sm overflow-hidden">
                <div class="px-8 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Student Response</span>
                    <div class="flex gap-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        <span>{{ $submission->word_count }} Words</span>
                        <span>{{ $submission->character_count }} Characters</span>
                    </div>
                </div>
                <div class="p-10 prose prose-stone max-w-none prose-headings:italic prose-headings:font-headline">
                    @if($submission->answer_text)
                        {!! Str::markdown($submission->answer_text) !!}
                    @else
                        <div class="italic text-gray-400">No answer content provided.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Evaluation Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-stone-900 text-white rounded-[2rem] p-8 shadow-xl sticky top-6">
                <h4 class="text-sm font-bold uppercase tracking-[0.2em] mb-8 text-stone-400">Expert Evaluation</h4>
                
                <form wire:submit="saveEvaluation" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Score (out of {{ $submission->question->marks ?: 100 }})</label>
                        <input wire:model="score" type="number" min="0" max="{{ $submission->question->marks ?: 100 }}" class="w-full bg-stone-800 border-none rounded-xl text-white focus:ring-2 focus:ring-orange-800 p-4 text-xl font-bold" placeholder="0">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Feedback & Comments</label>
                        <textarea wire:model="feedback_text" rows="8" class="w-full bg-stone-800 border-none rounded-2xl text-stone-200 focus:ring-2 focus:ring-orange-800 p-4 text-sm leading-relaxed placeholder:text-stone-600" placeholder="Provide detailed feedback on structure, theories, and scholarly depth..."></textarea>
                    </div>

                    <button type="submit" class="w-full py-4 bg-orange-800 hover:bg-orange-900 text-white font-bold rounded-2xl transition-all shadow-xl shadow-orange-900/20 uppercase tracking-widest text-xs">
                        Save Evaluation
                    </button>
                </form>

                @if($submission->evaluated_at)
                    <div class="mt-8 pt-8 border-t border-stone-800 flex items-center gap-3">
                        <span class="material-symbols-outlined text-green-500 text-sm">verified</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-stone-500">Evaluated on {{ $submission->evaluated_at->format('M d, Y') }}</span>
                    </div>
                @endif
            </div>

            <!-- Student Metadata -->
            <div class="bg-white border border-gray-200 rounded-[2rem] p-8 shadow-sm">
                <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-6">Session Metadata</h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Target Time</span>
                        <span class="text-xs font-bold text-gray-900">{{ $submission->target_time_minutes }}m</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Time Spent</span>
                        <span class="text-xs font-bold text-gray-900">{{ floor($submission->time_spent_seconds / 60) }}m {{ $submission->time_spent_seconds % 60 }}s</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Submitted</span>
                        <span class="text-xs font-bold text-gray-900">{{ $submission->submitted_at ? $submission->submitted_at->format('M d, h:i A') : 'Not Submitted' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
