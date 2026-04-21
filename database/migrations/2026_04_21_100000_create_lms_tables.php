<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('overview')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('estimated_duration')->nullable();
            $table->string('level')->nullable(); // beginner, intermediate, advanced
            
            // Linking to existing topics taxonomy
            $table->foreignId('topic_id')->nullable()->constrained('topics')->onDelete('set null');
            
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lms_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_module_id')->constrained('lms_modules')->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->text('short_description')->nullable();
            $table->string('video_source_type')->default('url'); // upload, url
            $table->string('video_path')->nullable();
            $table->string('video_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_published')->default(false);
            $table->longText('notes')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lms_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_module_id')->constrained('lms_modules')->onDelete('cascade');
            $table->string('title');
            $table->text('short_description')->nullable();
            $table->string('file_path'); // PDF
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_resources');
        Schema::dropIfExists('lms_lessons');
        Schema::dropIfExists('lms_modules');
    }
};
