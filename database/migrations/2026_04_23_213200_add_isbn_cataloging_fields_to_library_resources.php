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
            $table->string('cover_source')->nullable()->after('cover_image_path');
            $table->string('cover_external_url')->nullable()->after('cover_source');
        });
    }

    public function down(): void
    {
        Schema::table('library_resources', function (Blueprint $table) {
            $table->dropColumn(['cover_source', 'cover_external_url']);
        });
    }
};
