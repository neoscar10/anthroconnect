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
            // Drop the unique index using its actual name from before the table rename.
            // When explore_topics was renamed to topics, the index name explore_topics_slug_unique was preserved.
            $table->dropUnique('explore_topics_slug_unique');
            
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
