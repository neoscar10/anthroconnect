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
        Schema::create('explore_topics', function (Blueprint $col) {
            $col->id();
            $col->string('name');
            $col->string('slug')->unique();
            $col->text('short_description')->nullable();
            $col->boolean('is_active')->default(true);
            $col->integer('sort_order')->default(0);
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('explore_topics');
    }
};
