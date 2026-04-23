<?php

namespace App\Livewire\Public\Community;

use App\Services\Community\CommunityDiscussionService;
use App\Models\Topic;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class StartDiscussionModal extends Component
{
    public $show = false;
    public $topic_id = '';
    public $title = '';
    public $body = '';
    public $tags = '';

    protected $listeners = ['open-start-discussion' => 'open'];

    /**
     * Open the modal if authenticated, otherwise trigger account promotion.
     */
    public function open()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $this->show = true;
    }

    /**
     * Persist the new discussion through the service layer.
     */
    public function save(CommunityDiscussionService $service)
    {
        if (!Auth::check()) return;

        $this->validate([
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|min:1|max:255',
            'body' => 'required|string|min:1',
            'tags' => 'nullable|string',
        ]);

        $service->createDiscussion([
            'topic_id' => $this->topic_id,
            'title' => $this->title,
            'body' => $this->body,
            'tags' => $this->tags,
        ], Auth::user());

        $this->reset(['topic_id', 'title', 'body', 'tags', 'show']);
        
        // Refresh the feed
        $this->dispatch('discussion-created');
        
        session()->flash('success', 'Your inquiry has been published to the community.');
    }

    public function render()
    {
        $topics = Topic::active()->orderBy('name')->get();
        return view('livewire.public.community.start-discussion-modal', [
            'topics' => $topics
        ]);
    }
}
