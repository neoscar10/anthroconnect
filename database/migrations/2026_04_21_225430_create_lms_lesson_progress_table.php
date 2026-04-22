<?php
/*
|--------------------------------------------------------------------------
| Migration: Create LMS Lesson Progress Table
|--------------------------------------------------------------------------
|
| This migration defines the schema for tracking scholar advancement through
| academic modules. It supports both manual completion and automatic
| video playback detection.
|
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_lesson_progress', function (Blueprint $table) {
            $table->id();
            
            // Core Relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lms_module_id')->constrained('lms_modules')->onDelete('cascade');
            $table->foreignId('lms_lesson_id')->constrained('lms_lessons')->onDelete('cascade');
            
            // Progress Stats
            $table->timestamp('completed_at')->nullable();
            $table->integer('watched_seconds')->default(0);
            $table->timestamp('last_watched_at')->nullable();
            
            $table->timestamps();

            // Prevent duplicate records for the same user-lesson pair
            $table->unique(['user_id', 'lms_lesson_id'], 'user_lesson_progress_unique');
            
            // Index for faster module progress calculation
            $table->index(['user_id', 'lms_module_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_lesson_progress');
    }
};
