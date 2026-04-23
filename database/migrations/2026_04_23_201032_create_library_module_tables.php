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
        // 1. Resource Types
        Schema::create('library_resource_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon_key')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Disciplines
        Schema::create('library_disciplines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Regions
        Schema::create('library_regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Tags
        Schema::create('library_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 5. Main Resources Table
        Schema::create('library_resources', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->longText('abstract');
            $table->longText('description')->nullable();
            $table->string('author_display');
            $table->integer('publication_year')->nullable();
            $table->string('publisher')->nullable();
            
            $table->foreignId('resource_type_id')->constrained('library_resource_types')->onDelete('restrict');
            $table->foreignId('discipline_id')->nullable()->constrained('library_disciplines')->onDelete('set null');
            $table->foreignId('region_id')->nullable()->constrained('library_regions')->onDelete('set null');
            
            $table->string('language')->nullable();
            $table->string('isbn')->nullable();
            $table->string('doi')->nullable();
            $table->string('edition')->nullable();
            $table->integer('pages_count')->nullable();
            
            $table->string('file_path')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('preview_file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->string('source_label')->nullable();
            
            $table->longText('citation_text_apa')->nullable();
            $table->longText('citation_text_mla')->nullable();
            $table->longText('citation_text_chicago')->nullable();
            
            $table->enum('access_type', ['public', 'member_only'])->default('public');
            $table->boolean('allow_download')->default(true);
            $table->boolean('allow_online_read')->default(true);
            
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_latest')->default(false);
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_editors_pick')->default(false);
            $table->boolean('show_in_more_resources')->default(true);
            
            $table->integer('sort_order')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Pivot: Resources & Topics
        Schema::create('library_resource_topic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_resource_id')->constrained()->onDelete('cascade');
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 7. Pivot: Resources & Tags
        Schema::create('library_resource_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_resource_id')->constrained()->onDelete('cascade');
            $table->foreignId('library_tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 8. Pivot: Related Resources
        Schema::create('library_resource_related_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_resource_id')->constrained('library_resources')->onDelete('cascade');
            $table->foreignId('related_resource_id')->constrained('library_resources')->onDelete('cascade');
            $table->enum('relation_type', ['more_resource', 'similar_theme', 'same_author', 'same_topic'])->default('more_resource');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 9. Pivot: Related Learning (Polymorphic)
        Schema::create('library_resource_related_learning', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_resource_id')->constrained('library_resources')->onDelete('cascade');
            $table->morphs('linkable', 'lib_rel_learn_idx');
            $table->string('label')->nullable();
            $table->enum('relation_type', ['module', 'knowledge_map', 'encyclopedia', 'community_topic', 'lesson'])->default('module');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_resource_related_learning');
        Schema::dropIfExists('library_resource_related_resources');
        Schema::dropIfExists('library_resource_tag');
        Schema::dropIfExists('library_resource_topic');
        Schema::dropIfExists('library_resources');
        Schema::dropIfExists('library_tags');
        Schema::dropIfExists('library_regions');
        Schema::dropIfExists('library_disciplines');
        Schema::dropIfExists('library_resource_types');
    }
};
