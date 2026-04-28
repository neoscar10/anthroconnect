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
        Schema::create('knowledge_maps', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->enum('visibility', ['public', 'members_only'])->default('public');
            $table->decimal('default_zoom', 5, 2)->nullable()->default(1.00);
            $table->json('canvas_settings')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('knowledge_map_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_map_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->enum('node_type', [
                'core_concept', 'concept', 'thinker', 'theory', 'topic', 'region', 'lesson', 'material', 'custom'
            ]);
            $table->enum('importance', [
                'core', 'primary', 'secondary', 'advanced', 'optional'
            ])->default('secondary');
            $table->text('short_description')->nullable();
            $table->longText('full_description')->nullable();
            $table->decimal('position_x', 10, 2)->default(0);
            $table->decimal('position_y', 10, 2)->default(0);
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_upsc_relevant')->default(false);
            $table->boolean('is_members_only')->default(false);
            
            // External References
            $table->foreignId('encyclopedia_concept_id')->nullable()->constrained('encyclopedia_core_concepts')->onDelete('set null');
            $table->foreignId('anthropologist_id')->nullable()->constrained('encyclopedia_anthropologists')->onDelete('set null');
            $table->foreignId('theory_id')->nullable()->constrained('encyclopedia_major_theories')->onDelete('set null');
            $table->foreignId('lms_module_id')->nullable()->constrained('lms_modules')->onDelete('set null');
            $table->foreignId('lms_lesson_id')->nullable()->constrained('lms_lessons')->onDelete('set null');
            $table->foreignId('lms_material_id')->nullable()->constrained('lms_resources')->onDelete('set null');
            
            $table->string('manual_concept_title')->nullable();
            $table->text('manual_concept_summary')->nullable();
            $table->string('estimated_read_time')->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('knowledge_map_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_map_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_node_id')->constrained('knowledge_map_nodes')->onDelete('cascade');
            $table->foreignId('to_node_id')->constrained('knowledge_map_nodes')->onDelete('cascade');
            $table->string('label')->nullable();
            $table->enum('connection_type', [
                'relates_to', 'leads_to', 'depends_on', 'contrasts_with', 'influenced_by', 'example_of', 'part_of', 'custom'
            ])->default('relates_to');
            $table->enum('direction', ['one_way', 'two_way'])->default('one_way');
            $table->enum('line_style', ['solid', 'dashed', 'dotted'])->default('solid');
            $table->string('color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('knowledge_map_learning_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_map_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->string('estimated_duration')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('knowledge_map_learning_path_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_path_id')->constrained('knowledge_map_learning_paths')->onDelete('cascade');
            $table->foreignId('node_id')->constrained('knowledge_map_nodes')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_map_learning_path_nodes');
        Schema::dropIfExists('knowledge_map_learning_paths');
        Schema::dropIfExists('knowledge_map_connections');
        Schema::dropIfExists('knowledge_map_nodes');
        Schema::dropIfExists('knowledge_maps');
    }
};
