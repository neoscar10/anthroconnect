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
        Schema::table('lms_modules', function (Blueprint $table) {
            $table->dropColumn(['banner_image', 'estimated_duration', 'is_featured', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_modules', function (Blueprint $table) {
            $table->string('banner_image')->nullable();
            $table->integer('estimated_duration')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
        });
    }
};
