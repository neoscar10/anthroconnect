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
            $table->foreignId('lms_module_id')
                ->nullable()
                ->after('id')
                ->constrained('lms_modules')
                ->nullOnDelete();

            $table->foreignId('lms_module_class_id')
                ->nullable()
                ->after('lms_module_id')
                ->constrained('lms_module_classes')
                ->nullOnDelete();

            $table->string('question_type')->default('essay')->after('slug')->index(); // essay, mcq
            $table->text('explanation')->nullable()->after('answer_guidelines');

            $table->index(['lms_module_id', 'lms_module_class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropForeign(['lms_module_id']);
            $table->dropForeign(['lms_module_class_id']);
            $table->dropColumn(['lms_module_id', 'lms_module_class_id', 'question_type', 'explanation']);
        });
    }
};
