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
            if (!Schema::hasColumn('exam_questions', 'lms_class_assessment_id')) {
                $table->foreignId('lms_class_assessment_id')
                    ->nullable()
                    ->after('lms_module_class_id')
                    ->constrained('lms_class_assessments')
                    ->nullOnDelete();

                $table->index('lms_class_assessment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lms_class_assessment_id');
        });
    }
};
