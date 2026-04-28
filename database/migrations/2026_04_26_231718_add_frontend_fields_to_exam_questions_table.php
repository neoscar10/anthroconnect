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
            if (! Schema::hasColumn('exam_questions', 'is_members_only')) {
                $table->boolean('is_members_only')->default(false)->after('status')->index();
            }
            if (! Schema::hasColumn('exam_questions', 'is_question_of_day')) {
                $table->boolean('is_question_of_day')->default(false)->after('is_members_only')->index();
            }
            if (! Schema::hasColumn('exam_questions', 'question_of_day_date')) {
                $table->date('question_of_day_date')->nullable()->after('is_question_of_day')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropColumn(['is_members_only', 'is_question_of_day', 'question_of_day_date']);
        });
    }
};
