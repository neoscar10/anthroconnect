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
        Schema::create('knowledge_map_node_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->constrained('knowledge_map_nodes')->onDelete('cascade');
            $table->morphs('attachable', 'node_attachable_index'); // Shorter index name
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_map_node_attachments');
    }
};
