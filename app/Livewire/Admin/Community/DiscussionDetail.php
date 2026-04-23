<?php

namespace App\Livewire\Admin\Community;

use Livewire\Component;
use App\Models\Community\CommunityDiscussion;
use App\Models\Community\CommunityDiscussionReply;
use Livewire\WithPagination;

class DiscussionDetail extends Component
{
    use WithPagination;

    public $discussionId;
    public $confirmingDeletion = null;

    public function mount($id)
    {
        $this->discussionId = $id;
    }

    public function toggleReplyStatus($id)
    {
        $reply = CommunityDiscussionReply::findOrFail($id);
        $reply->status = $reply->status === 'published' ? 'hidden' : 'published';
        $reply->save();
    }

    public function toggleExpert($id)
    {
        $reply = CommunityDiscussionReply::findOrFail($id);
        $reply->is_expert_reply = !$reply->is_expert_reply;
        $reply->save();
    }

    public function deleteReply($id)
    {
        $reply = CommunityDiscussionReply::findOrFail($id);
        $reply->delete();
    }

    public function render()
    {
        $discussion = CommunityDiscussion::with(['author', 'topic'])->findOrFail($this->discussionId);
        $replies = CommunityDiscussionReply::where('community_discussion_id', $this->discussionId)
            ->with(['author'])
            ->latest()
            ->paginate(15);

        return view('livewire.admin.community.discussion-detail', [
            'discussion' => $discussion,
            'replies' => $replies,
        ])->layout('layouts.admin');
    }
}
