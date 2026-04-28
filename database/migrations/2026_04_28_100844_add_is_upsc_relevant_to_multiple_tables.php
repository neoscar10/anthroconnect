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
        $tables = [
            'explore_articles' => 'is_members_only',
            'lms_modules' => 'is_published',
            'encyclopedia_anthropologists' => 'nationality',
            'encyclopedia_core_concepts' => 'status',
            'encyclopedia_major_theories' => 'status',
            'library_resources' => 'status',
        ];

        foreach ($tables as $tableName => $afterColumn) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $afterColumn) {
                if (!Schema::hasColumn($tableName, 'is_upsc_relevant')) {
                    $column = $table->boolean('is_upsc_relevant')->default(false)->index();
                    
                    if (Schema::hasColumn($tableName, $afterColumn)) {
                        $column->after($afterColumn);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'explore_articles',
            'lms_modules',
            'encyclopedia_anthropologists',
            'encyclopedia_core_concepts',
            'encyclopedia_major_theories',
            'library_resources',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'is_upsc_relevant')) {
                    $table->dropColumn('is_upsc_relevant');
                }
            });
        }
    }
};
