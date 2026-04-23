<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Community\CommunityDiscussion;
use App\Models\Community\CommunityDiscussionReply;
use App\Models\User;
use Illuminate\Support\Str;

class CommunityReplySeeder extends Seeder
{
    public function run(): void
    {
        $scholars = User::all();
        $discussions = CommunityDiscussion::all();

        if ($scholars->isEmpty() || $discussions->isEmpty()) {
            return;
        }

        foreach ($discussions as $discussion) {
            // Add a few top-level replies
            $replyCount = rand(3, 6);
            for ($i = 0; $i < $replyCount; $i++) {
                $author = $scholars->random();
                $isExpert = (rand(1, 10) > 8); // 20% chance for expert reply

                $reply = CommunityDiscussionReply::create([
                    'community_discussion_id' => $discussion->id,
                    'user_id' => $author->id,
                    'body' => $this->getRandomAnthropologyReply($discussion->title),
                    'is_expert_reply' => $isExpert,
                    'is_featured' => (rand(1, 10) > 9),
                    'status' => 'published',
                    'upvotes_count' => rand(0, 50),
                    'published_at' => now()->subDays(rand(1, 10))->subHours(rand(1, 23)),
                ]);

                // Update discussion metrics
                $discussion->increment('replies_count');

                // Add nested replies
                if (rand(1, 10) > 5) {
                    $subReplyCount = rand(1, 3);
                    for ($j = 0; $j < $subReplyCount; $j++) {
                        CommunityDiscussionReply::create([
                            'community_discussion_id' => $discussion->id,
                            'user_id' => $scholars->random()->id,
                            'parent_id' => $reply->id,
                            'body' => "This is a profound point regarding the " . Str::lower(Str::words($discussion->title, 2, '')) . ". Have you considered the longitudinal impact on kinship structures?",
                            'depth' => 1,
                            'status' => 'published',
                            'published_at' => $reply->published_at->addHours(rand(1, 12)),
                        ]);
                        $discussion->increment('replies_count');
                        $reply->increment('replies_count');
                    }
                }
            }
            
            $discussion->update(['last_activity_at' => now()]);
        }
    }

    private function getRandomAnthropologyReply($title)
    {
        $replies = [
            "Your analysis of structuralism in this context is fascinating. It reminds me of Lévi-Strauss's work on the 'raw and the cooked' but applied to modern digital kinship.",
            "I would argue that the ethnographic data suggests a more nuanced interpretation. The field observations in Melanesia often contradict this specific theoretical framework.",
            "Have you looked into the post-colonial critiques of this approach? I believe Said's 'Orientalism' provides a necessary lens for deconstructing these power dynamics.",
            "This parallels my own research into urban anthropology. The shifting boundaries of the 'polis' create exactly these kinds of identity ambiguities.",
            "The linguistic turn in anthropology would suggest that the terminology used here is itself a form of cultural artifact that needs deconstruction.",
            "Excellent contribution to the field. I'm currently working on a paper that expands on these exact themes regarding economic reciprocity in gift-giving societies.",
            "While I agree with your premise, the methodology seems to rely heavily on Western ontological assumptions. Indigenous perspectives might offer a counter-narrative.",
        ];

        return $replies[array_rand($replies)];
    }
}
