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
        Schema::create('lms_class_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_module_id')->constrained('lms_modules')->cascadeOnDelete();
            $table->foreignId('lms_module_class_id')->constrained('lms_module_classes')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('total_marks')->default(0);
            $table->integer('passing_marks')->nullable();
            $table->boolean('allow_retake')->default(true);
            $table->boolean('show_results_immediately')->default(true);
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_options')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_class_assessments');
    }
};
