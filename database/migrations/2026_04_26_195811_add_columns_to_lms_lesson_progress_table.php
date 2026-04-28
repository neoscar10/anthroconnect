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
        Schema::table('lms_lesson_progress', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('lms_module_id')->after('user_id')->constrained('lms_modules')->cascadeOnDelete();
            $table->foreignId('lms_lesson_id')->after('lms_module_id')->constrained('lms_lessons')->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable()->after('lms_lesson_id');
            $table->integer('watched_seconds')->default(0)->after('completed_at');
            $table->timestamp('last_watched_at')->nullable()->after('watched_seconds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_lesson_progress', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['lms_module_id']);
            $table->dropForeign(['lms_lesson_id']);
            $table->dropColumn(['user_id', 'lms_module_id', 'lms_lesson_id', 'completed_at', 'watched_seconds', 'last_watched_at']);
        });
    }
};
