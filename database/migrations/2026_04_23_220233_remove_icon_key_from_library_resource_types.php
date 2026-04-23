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
        Schema::table('library_resource_types', function (Blueprint $table) {
            $table->dropColumn('icon_key');
        });
    }

    public function down(): void
    {
        Schema::table('library_resource_types', function (Blueprint $table) {
            $table->string('icon_key')->nullable()->after('slug');
        });
    }
};
