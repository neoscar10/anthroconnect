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
            $table->string('submission_type')->default('text')->after('answer_text'); // 'text' or 'file'
            $table->string('attachment_path')->nullable()->after('submission_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_answer_submissions', function (Blueprint $table) {
            $table->dropColumn(['submission_type', 'attachment_path']);
        });
    }
};
