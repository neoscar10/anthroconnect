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
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropColumn(['is_question_of_day', 'question_of_day_date']);
        });
    }

    public function down(): void
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->boolean('is_question_of_day')->default(false)->index();
            $table->date('question_of_day_date')->nullable()->index();
        });
    }
};
