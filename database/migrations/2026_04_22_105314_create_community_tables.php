<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // We are using the central 'topics' table instead of 'community_topics'
        
        Schema::create('community_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->enum('status', ['published', 'hidden', 'archived'])->default('published');
            $table->enum('discussion_state', ['open', 'closed', 'solved'])->default('open');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_expert_spotlight')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedInteger('replies_count')->default(0);
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('bookmarks_count')->default(0);
            $table->unsignedInteger('shares_count')->default(0);
            $table->unsignedBigInteger('solved_reply_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['topic_id', 'published_at']);
            $table->index(['status', 'published_at']);
            $table->index('is_featured');
            $table->index('is_trending');
            $table->index('is_popular');
            $table->index('last_activity_at');
            $table->index('created_at');
        });

        Schema::create('community_discussion_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('community_discussion_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_discussion_id')->constrained('community_discussions')->onDelete('cascade');
            $table->foreignId('community_discussion_tag_id')->constrained('community_discussion_tags', 'id', 'cd_tag_fk')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['community_discussion_id', 'community_discussion_tag_id'], 'comm_disc_tag_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_discussion_tag');
        Schema::dropIfExists('community_discussion_tags');
        Schema::dropIfExists('community_discussions');
    }
};
