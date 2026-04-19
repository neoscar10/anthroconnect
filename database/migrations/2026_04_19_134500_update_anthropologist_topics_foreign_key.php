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
        Schema::table('anthropologist_encyclopedia_topic', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['topic_id']);
            
            // Re-add foreign key pointing to the main topics table
            $table->foreign('topic_id')
                ->references('id')
                ->on('topics')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anthropologist_encyclopedia_topic', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            
            $table->foreign('topic_id')
                ->references('id')
                ->on('encyclopedia_topics')
                ->cascadeOnDelete();
        });
    }
};
