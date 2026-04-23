<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Models\Community\CommunityDiscussion;
use App\Models\User;
use Illuminate\Support\Str;

class UnifiedTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $topicsData = [
            ['name' => 'Cultural Anthropology', 'color' => '#F97316', 'icon' => 'people'],
            ['name' => 'Archaeology', 'color' => '#10B981', 'icon' => 'architecture'],
            ['name' => 'Linguistic Anthropology', 'color' => '#3B82F6', 'icon' => 'translate'],
            ['name' => 'Biological Anthropology', 'color' => '#EF4444', 'icon' => 'biotech'],
            ['name' => 'Medical Anthropology', 'color' => '#8B5CF6', 'icon' => 'medical_services'],
            ['name' => 'Visual Anthropology', 'color' => '#EC4899', 'icon' => 'movie'],
            ['name' => 'Urban Anthropology', 'color' => '#6B7280', 'icon' => 'location_city'],
            ['name' => 'Economic Anthropology', 'color' => '#059669', 'icon' => 'payments'],
        ];

        foreach ($topicsData as $data) {
            Topic::updateOrCreate(
                ['name' => $data['name']],
                [
                    'short_description' => "Exploring the complexities of {$data['name']} within global scholarship.",
                    'is_active' => true,
                    'color' => $data['color'],
                    'icon' => $data['icon'],
                ]
            );
        }

        // Re-seed some discussions
        $topics = Topic::all();
        $users = User::all();

        if ($users->isEmpty()) {
            User::factory()->create(['name' => 'Scholar Admin', 'email' => 'admin@anthroconnect.com']);
            $users = User::all();
        }

        // Clear existing to avoid confusion with old FKs if needed
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\DB::table('community_discussion_votes')->truncate();
        \Illuminate\Support\Facades\DB::table('community_discussion_replies')->truncate();
        CommunityDiscussion::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        foreach ($topics as $topic) {
            for ($i = 0; $i < 3; $i++) {
                $title = "Inquiry into " . $topic->name . " " . Str::random(5);
                CommunityDiscussion::create([
                    'user_id' => $users->random()->id,
                    'topic_id' => $topic->id,
                    'title' => $title,
                    'slug' => Str::slug($title),
                    'excerpt' => "A deep analysis of current methodologies in {$topic->name}.",
                    'body' => "This scholarly inquiry explores the intersections of modern fieldwork and theoretical frameworks within {$topic->name}. We invite peer reviews and analysis of existing ethnographic data.",
                    'status' => 'published',
                    'published_at' => now()->subDays(rand(1, 30)),
                    'last_activity_at' => now(),
                    'views_count' => rand(100, 500),
                    'replies_count' => 0,
                    'likes_count' => rand(10, 50),
                ]);
            }
        }
    }
}
