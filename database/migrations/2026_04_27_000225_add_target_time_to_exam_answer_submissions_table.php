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
            $table->unsignedInteger('target_time_minutes')->default(15)->after('time_spent_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('exam_answer_submissions', function (Blueprint $table) {
            $table->dropColumn('target_time_minutes');
        });
    }
};
