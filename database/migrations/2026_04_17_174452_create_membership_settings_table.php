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
        Schema::create('membership_settings', function (Blueprint $header) {
            $header->id();
            $header->string('title')->nullable();
            $header->decimal('price_inr', 12, 2);
            $header->text('description')->nullable();
            $header->boolean('is_active')->default(true);
            $header->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_settings');
    }
};
