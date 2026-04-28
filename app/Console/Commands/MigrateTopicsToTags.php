<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Topic;
use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Support\Facades\DB;

class MigrateTopicsToTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anthro:topics-to-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing topics and their relationships to the new Tagging system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Topics to Tags migration...');

        // 1. Create or get the "Topics" Tag Group
        $group = TagGroup::firstOrCreate(
            ['slug' => 'topics'],
            [
                'name' => 'Topics',
                'description' => 'Primary academic and research topics',
                'selection_type' => 'multi_select',
                'display_order' => 1,
                'is_active' => true,
            ]
        );

        $this->info('Tag Group "Topics" ensured.');

        // 2. Migrate Topics to Tags
        $topics = Topic::all();
        $topicIdToTagId = [];

        foreach ($topics as $topic) {
            $tag = Tag::updateOrCreate(
                ['tag_group_id' => $group->id, 'slug' => $topic->slug],
                [
                    'name' => $topic->name,
                    'description' => $topic->short_description,
                    'is_active' => $topic->is_active,
                    'display_order' => 0,
                ]
            );
            $topicIdToTagId[$topic->id] = $tag->id;
        }

        $this->info(count($topics) . ' topics migrated to tags.');

        // 3. Migrate Relationships
        
        // Explore Articles
        $articles = \App\Models\ExploreArticle::whereNotNull('topic_id')->get();
        foreach ($articles as $article) {
            if (isset($topicIdToTagId[$article->topic_id])) {
                $article->tags()->syncWithoutDetaching([$topicIdToTagId[$article->topic_id]]);
            }
        }
        $this->info(count($articles) . ' Explore Articles relationships migrated.');

        // LMS Modules
        $modules = \App\Models\Lms\LmsModule::whereNotNull('topic_id')->get();
        foreach ($modules as $module) {
            if (isset($topicIdToTagId[$module->topic_id])) {
                $module->tags()->syncWithoutDetaching([$topicIdToTagId[$module->topic_id]]);
            }
        }
        $this->info(count($modules) . ' LMS Modules relationships migrated.');

        // Community Discussions
        $discussions = \App\Models\Community\CommunityDiscussion::whereNotNull('topic_id')->get();
        foreach ($discussions as $discussion) {
            if (isset($topicIdToTagId[$discussion->topic_id])) {
                $discussion->tags()->syncWithoutDetaching([$topicIdToTagId[$discussion->topic_id]]);
            }
        }
        $this->info(count($discussions) . ' Community Discussions relationships migrated.');

        // Anthropologists (Pivot Table)
        $anthropologistTopics = DB::table('anthropologist_encyclopedia_topic')->get();
        $count = 0;
        foreach ($anthropologistTopics as $row) {
            if (isset($topicIdToTagId[$row->topic_id])) {
                $anthropologist = \App\Models\Encyclopedia\Anthropologist::find($row->anthropologist_id);
                if ($anthropologist) {
                    $anthropologist->tags()->syncWithoutDetaching([$topicIdToTagId[$row->topic_id]]);
                    $count++;
                }
            }
        }
        $this->info($count . ' Anthropologist relationships migrated.');

        // Library Resources (Pivot Table)
        $libraryTopics = DB::table('library_resource_topic')->get();
        $count = 0;
        foreach ($libraryTopics as $row) {
            if (isset($topicIdToTagId[$row->topic_id])) {
                $resource = \App\Models\LibraryResource::find($row->library_resource_id);
                if ($resource) {
                    $resource->tags()->syncWithoutDetaching([$topicIdToTagId[$row->topic_id]]);
                    $count++;
                }
            }
        }
        $this->info($count . ' Library Resource relationships migrated.');

        // 4. Migrate Legacy Tags (Optional but recommended for consistency)
        
        // Library Tags
        $libGroup = TagGroup::firstOrCreate(
            ['slug' => 'library-tags'],
            ['name' => 'Library Tags', 'selection_type' => 'multi_select', 'display_order' => 2]
        );
        $libTags = \App\Models\LibraryTag::all();
        foreach ($libTags as $lt) {
            $tag = Tag::updateOrCreate(
                ['tag_group_id' => $libGroup->id, 'slug' => $lt->slug],
                ['name' => $lt->name]
            );
            
            $resourceIds = DB::table('library_resource_tag')->where('library_tag_id', $lt->id)->pluck('library_resource_id');
            foreach ($resourceIds as $rid) {
                $res = \App\Models\LibraryResource::find($rid);
                if ($res) $res->tags()->syncWithoutDetaching([$tag->id]);
            }
        }
        $this->info('Legacy Library Tags migrated.');

        // Community Discussion Tags
        $commGroup = TagGroup::firstOrCreate(
            ['slug' => 'discussion-tags'],
            ['name' => 'Discussion Tags', 'selection_type' => 'multi_select', 'display_order' => 3]
        );
        $commTags = \App\Models\Community\CommunityDiscussionTag::all();
        foreach ($commTags as $ct) {
            $tag = Tag::updateOrCreate(
                ['tag_group_id' => $commGroup->id, 'slug' => $ct->slug],
                ['name' => $ct->name]
            );
            
            $discussionIds = DB::table('community_discussion_tag')->where('community_discussion_tag_id', $ct->id)->pluck('community_discussion_id');
            foreach ($discussionIds as $did) {
                $disc = \App\Models\Community\CommunityDiscussion::find($did);
                if ($disc) $disc->tags()->syncWithoutDetaching([$tag->id]);
            }
        }
        $this->info('Legacy Discussion Tags migrated.');

        $this->info('Migration completed successfully!');
    }
}
