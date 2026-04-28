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
        Schema::table('exam_answer_submissions', function (Blueprint $table) {
            // Add a non-unique index for performance FIRST
            $table->index(['exam_question_id', 'user_id']);
            
            // Now drop the existing unique constraint
            $table->dropUnique(['exam_question_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_answer_submissions', function (Blueprint $table) {
            $table->dropIndex(['exam_question_id', 'user_id']);
            $table->unique(['exam_question_id', 'user_id']);
        });
    }
};
