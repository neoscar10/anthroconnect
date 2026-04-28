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
        Schema::table('explore_articles', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('is_featured');
        });

        // Initialize sort_order with current IDs to maintain order
        $articles = \DB::table('explore_articles')->orderBy('id')->get();
        foreach ($articles as $index => $article) {
            \DB::table('explore_articles')
                ->where('id', $article->id)
                ->update(['sort_order' => $index + 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('explore_articles', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
