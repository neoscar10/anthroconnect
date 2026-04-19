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
        Schema::create('explore_articles', function (Blueprint $col) {
            $col->id();
            $col->foreignId('topic_id')->constrained('explore_topics')->onDelete('cascade');
            $col->string('title');
            $col->string('slug')->unique();
            $col->text('excerpt');
            $col->string('featured_image')->nullable();
            $col->longText('markdown_content');
            $col->longText('rendered_content_html')->nullable();
            
            $col->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $col->boolean('is_featured')->default(false);
            
            $col->timestamp('published_at')->nullable();
            
            $col->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $col->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $col->string('seo_title')->nullable();
            $col->text('seo_description')->nullable();
            $col->integer('reading_time_minutes')->nullable();
            
            $col->timestamps();
            $col->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('explore_articles');
    }
};
