<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Fill slugs for existing topics
        DB::table('topics')->get()->each(function ($topic) {
            DB::table('topics')->where('id', $topic->id)->update([
                'slug' => Str::slug($topic->name)
            ]);
        });

        Schema::table('topics', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
