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
        Schema::create('encyclopedia_anthropologists', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->longText('biography_markdown');
            $table->integer('birth_year')->nullable();
            $table->integer('death_year')->nullable();
            $table->string('discipline_or_specialization')->nullable();
            $table->string('nationality')->nullable();
            $table->string('profile_image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encyclopedia_anthropologists');
    }
};
