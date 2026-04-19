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
        Schema::create('anthropologist_core_concept', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anthropologist_id')->constrained('encyclopedia_anthropologists')->cascadeOnDelete();
            $table->foreignId('core_concept_id')->constrained('encyclopedia_core_concepts')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anthropologist_core_concept');
    }
};
