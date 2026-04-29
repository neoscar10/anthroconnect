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
            $table->string('evaluation_attachment_path')->nullable()->after('feedback_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_answer_submissions', function (Blueprint $table) {
            $table->dropColumn('evaluation_attachment_path');
        });
    }
};
