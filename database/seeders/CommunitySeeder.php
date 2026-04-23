<?php

namespace Database\Seeders;

use App\Models\Community\CommunityTopic;
use App\Models\Community\CommunityDiscussion;
use App\Models\Community\CommunityDiscussionTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create(['name' => 'Dr. Julian Thorne', 'email' => 'julian@anthroconnect.com']);

        $topics = [
            [
                'name' => 'Cultural Anthropology',
                'slug' => 'cultural',
                'description' => 'The study of cultural variation among humans.',
                'icon' => 'groups',
                'color' => '#9a3412',
            ],
            [
                'name' => 'Social Anthropology',
                'slug' => 'social',
                'description' => 'How people live in social groups and the ways they make their lives meaningful.',
                'icon' => 'diversity_3',
                'color' => '#1e293b',
            ],
            [
                'name' => 'Biological Anthropology',
                'slug' => 'biological',
                'description' => 'Human biological and physiological characteristics and their evolution.',
                'icon' => 'dna',
                'color' => '#166534',
            ],
            [
                'name' => 'Linguistic Anthropology',
                'slug' => 'linguistic',
                'description' => 'How language influences social life and human culture.',
                'icon' => 'translate',
                'color' => '#0369a1',
            ],
            [
                'name' => 'Archaeology',
                'slug' => 'archaeology',
                'description' => 'The study of human activity through the recovery and analysis of material culture.',
                'icon' => 'architecture',
                'color' => '#854d0e',
            ],
            [
                'name' => 'Medical Anthropology',
                'slug' => 'medical',
                'description' => 'Human health and disease, health care systems, and biocultural adaptation.',
                'icon' => 'medical_services',
                'color' => '#991b1b',
            ],
            [
                'name' => 'Political Anthropology',
                'slug' => 'political',
                'description' => 'Structures of political power and governance in human societies.',
                'icon' => 'account_balance',
                'color' => '#3730a3',
            ],
            [
                'name' => 'Digital Anthropology',
                'slug' => 'digital',
                'description' => 'Human-technology interactions and digital cultural manifestations.',
                'icon' => 'devices',
                'color' => '#6b21a8',
            ],
        ];

        foreach ($topics as $topicData) {
            CommunityTopic::updateOrCreate(['slug' => $topicData['slug']], $topicData);
        }

        $allTopics = CommunityTopic::all();

        $tags = ['Kinship', 'Fieldwork', 'Ethnography', 'Evolution', 'Semiotics', 'Urbanism', 'Ritual', 'Power', 'Globalization', 'Health'];
        $tagModels = [];
        foreach ($tags as $tagName) {
            $tagModels[] = CommunityDiscussionTag::updateOrCreate(['slug' => Str::slug($tagName)], ['name' => $tagName]);
        }

        $discussions = [
            [
                'title' => 'The Role of Kinship in Modern Urban Societies',
                'body' => 'In many urban environments, traditional kinship structures are often seen as declining. However, recent ethnographies suggest that kinship is simply mutating into new forms. What are your observations regarding "chosen family" versus biological lineage in your current research areas?',
                'topic' => 'social',
                'featured' => true,
                'expert' => false,
            ],
            [
                'title' => 'Participant Observation in Digital Spaces',
                'body' => 'Is it possible to achieve true "thick description" when our field is entirely mediated through screens? I\'m currently researching Discord communities and finding that the absence of physical presence changes the nature of rapport significantly.',
                'topic' => 'digital',
                'featured' => false,
                'expert' => true,
            ],
            [
                'title' => 'Decolonizing the Museum: A Structural Challenge',
                'body' => 'Returning artifacts is only the first step. How do we restructure the entire archival logic that underpins the Western museum model? I\'m looking for collaborative voices on post-colonial curatorship.',
                'topic' => 'archaeology',
                'featured' => true,
                'expert' => false,
            ],
            [
                'title' => 'Linguistic Relativity in the Age of AI Translation',
                'body' => 'Does the use of LLMs for real-time translation erode the Sapir-Whorf hypothesis, or does it create a new "meta-language" that further proves it? Let\'s discuss the cognitive implications of automated cross-linguistic communication.',
                'topic' => 'linguistic',
                'featured' => false,
                'expert' => false,
            ],
            [
                'title' => 'Structural Violence and Ethnomedicine in the Andes',
                'body' => 'My recent field notes from Peru highlight a direct tension between state health mandates and local ethnomedical practices. How can we bridge this gap without delegitimizing indigenous knowledge?',
                'topic' => 'medical',
                'featured' => false,
                'expert' => false,
            ],
        ];

        foreach ($discussions as $index => $d) {
            $topic = $allTopics->where('slug', $d['topic'])->first();
            $discussion = CommunityDiscussion::updateOrCreate(
                ['slug' => Str::slug($d['title'])],
                [
                    'user_id' => $user->id,
                    'community_topic_id' => $topic->id,
                    'title' => $d['title'],
                    'body' => $d['body'],
                    'excerpt' => Str::words($d['body'], 20),
                    'status' => 'published',
                    'discussion_state' => 'open',
                    'is_featured' => $d['featured'],
                    'is_expert_spotlight' => $d['expert'],
                    'published_at' => now()->subDays(rand(1, 30)),
                    'last_activity_at' => now()->subHours(rand(1, 48)),
                    'views_count' => rand(100, 1000),
                    'replies_count' => rand(5, 50),
                    'likes_count' => rand(10, 100),
                ]
            );

            $discussion->tags()->sync([
                $tagModels[array_rand($tagModels)]->id,
                $tagModels[array_rand($tagModels)]->id,
            ]);
        }

        // Add 15 more random ones to make it feel alive
        for ($i = 0; $i < 15; $i++) {
            $topic = $allTopics->random();
            $title = "Anthropological Inquiry #" . ($i + 1) . " on " . $topic->name;
            $discussion = CommunityDiscussion::create([
                'user_id' => $user->id,
                'community_topic_id' => $topic->id,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(4),
                'body' => 'Automated scholarly inquiry regarding ' . $topic->description . ' This discussion explores the nuances of contemporary human narratives.',
                'excerpt' => 'A brief overview of the research themes within ' . $topic->name . '.',
                'status' => 'published',
                'discussion_state' => rand(0, 1) ? 'open' : 'solved',
                'published_at' => now()->subDays(rand(1, 100)),
                'last_activity_at' => now()->subHours(rand(1, 100)),
                'views_count' => rand(10, 500),
                'replies_count' => rand(0, 20),
                'likes_count' => rand(0, 40),
            ]);
            
            $discussion->tags()->sync([$tagModels[array_rand($tagModels)]->id]);
        }
    }
}
