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
        Schema::table('library_resources', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });

        Schema::dropIfExists('library_regions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('library_regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('library_resources', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->constrained('library_regions')->onDelete('set null');
        });
    }
};
