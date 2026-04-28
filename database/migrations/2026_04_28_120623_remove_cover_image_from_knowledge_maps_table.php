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
        Schema::table('knowledge_maps', function (Blueprint $table) {
            $table->dropColumn('cover_image');
            $table->boolean('is_featured')->default(true)->change();
        });
        
        // Ensure the main map is featured
        \DB::table('knowledge_maps')->update(['is_featured' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge_maps', function (Blueprint $table) {
            $table->string('cover_image')->nullable();
            $table->boolean('is_featured')->default(false)->change();
        });
    }
};
