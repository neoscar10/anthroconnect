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
            $table->dropForeign(['discipline_id']);
            $table->dropColumn('discipline_id');
        });

        Schema::dropIfExists('library_disciplines');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('library_disciplines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('library_resources', function (Blueprint $table) {
            $table->foreignId('discipline_id')->nullable()->constrained('library_disciplines')->nullOnDelete();
        });
    }
};
