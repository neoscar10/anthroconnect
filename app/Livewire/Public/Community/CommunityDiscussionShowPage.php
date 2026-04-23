<?php

namespace App\Livewire\Public\Community;

use Livewire\Component;
use App\Services\Community\CommunityDiscussionService;
use App\Models\Community\CommunityDiscussion;
use App\Models\Community\CommunityDiscussionReply;
use Illuminate\Support\Facades\Auth;

class CommunityDiscussionShowPage extends Component
{
    public $slug;
    public $replyBody = '';
    public $replyingTo = null; // ID of the reply being replied to
    public $showTopComposer = false;

    protected $rules = [
        'replyBody' => 'required|min:1|max:5000',
    ];

    public function mount($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Submit a new top-level or nested reply.
     */
    public function submitReply(CommunityDiscussionService $service)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->validate();

        $discussion = $service->getDiscussionDetailBySlug($this->slug);

        $service->createReply($discussion, [
            'body' => $this->replyBody,
            'parent_id' => $this->replyingTo,
        ], Auth::user());

        $lastReplyTo = $this->replyingTo;
        $this->reset(['replyBody', 'replyingTo', 'showTopComposer']);
        
        session()->flash('message', 'Your scholarly contribution has been published.');
        session()->flash('last_reply_to', $lastReplyTo);
    }

    /**
     * Set the state to reply to a specific comment.
     */
    public function setReplyingTo($id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $this->replyingTo = $id;
    }

    /**
     * Cast a vote on the discussion or a reply.
     */
    public function vote($type, $id, $value, CommunityDiscussionService $service)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $votable = $type === 'discussion' 
            ? CommunityDiscussion::find($id) 
            : CommunityDiscussionReply::find($id);

        if ($votable) {
            $service->castVote($votable, Auth::user(), $value);
        }
    }

    public function render(CommunityDiscussionService $service)
    {
        $discussion = $service->getDiscussionDetailBySlug($this->slug);
        
        return view('livewire.public.community.community-discussion-show-page', [
            'discussion' => $discussion,
            'replies' => $service->getDiscussionReplies($discussion),
            'expertInsights' => $service->getExpertInsights($discussion),
            'relatedDiscussions' => $service->getRelatedDiscussions($discussion),
        ])->layout('layouts.public', ['title' => $discussion->title . ' | Community']);
    }
}
