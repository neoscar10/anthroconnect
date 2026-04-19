<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            // SQLite specific fix for orphan index from rename
            if (DB::getDriverName() === 'sqlite') {
                $table->dropUnique('explore_topics_slug_unique');
            } else {
                $table->dropUnique(['slug']);
            }
            
            $table->dropColumn(['slug', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable();
            $table->integer('sort_order')->default(0);
        });
    }
};
