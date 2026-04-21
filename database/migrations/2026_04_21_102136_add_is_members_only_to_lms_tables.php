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
        Schema::table('lms_lessons', function (Blueprint $table) {
            $table->boolean('is_members_only')->default(false)->after('is_published');
        });

        Schema::table('lms_resources', function (Blueprint $table) {
            $table->boolean('is_members_only')->default(false)->after('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_lessons', function (Blueprint $table) {
            $table->dropColumn('is_members_only');
        });

        Schema::table('lms_resources', function (Blueprint $table) {
            $table->dropColumn('is_members_only');
        });
    }
};
