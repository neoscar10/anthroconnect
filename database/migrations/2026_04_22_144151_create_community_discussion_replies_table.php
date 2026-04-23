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
        Schema::create('community_discussion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_discussion_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->longText('body');
            $table->boolean('is_expert_reply')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->enum('status', ['published', 'hidden', 'archived'])->default('published');
            $table->unsignedInteger('upvotes_count')->default(0);
            $table->unsignedInteger('downvotes_count')->default(0);
            $table->unsignedInteger('replies_count')->default(0);
            $table->unsignedTinyInteger('depth')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['community_discussion_id', 'status', 'published_at'], 'cd_replies_main_idx');
            $table->index('is_expert_reply');
            $table->index('is_featured');
            $table->index('is_pinned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_discussion_replies');
    }
};
