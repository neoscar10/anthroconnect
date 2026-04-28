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
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->longText('question_text');
            $table->string('slug')->unique()->nullable();
            
            // Metadata
            $table->string('exam_type')->nullable()->index(); // UPSC, NET, University, Practice
            $table->string('paper')->nullable()->index();     // Paper I, Paper II
            $table->string('section')->nullable();           // Cultural Anthropology, etc.
            $table->string('year')->nullable()->index();      // 2022, 2023...
            $table->integer('marks')->nullable();
            $table->integer('word_limit')->nullable();
            $table->integer('suggested_time_minutes')->nullable();
            $table->string('difficulty')->nullable()->index(); // beginner, intermediate, advanced
            
            // Content
            $table->text('short_context')->nullable();
            $table->longText('answer_guidelines')->nullable();
            $table->longText('model_answer')->nullable();
            $table->longText('model_answer_structure')->nullable();
            $table->json('evaluation_rubric')->nullable();
            $table->json('learning_resources')->nullable();
            
            // Status & Features
            $table->string('status')->default('draft')->index(); // draft, published, archived
            $table->boolean('is_question_of_day')->default(false)->index();
            $table->date('question_of_day_date')->nullable()->index();
            $table->integer('sort_order')->default(0);
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
