<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_lessons', function (Blueprint $table) {
            $table->foreignId('lms_module_class_id')
                ->nullable()
                ->after('lms_module_id')
                ->constrained('lms_module_classes')
                ->nullOnDelete();

            $table->index(['lms_module_id', 'lms_module_class_id']);
        });

        Schema::table('lms_resources', function (Blueprint $table) {
            $table->foreignId('lms_module_class_id')
                ->nullable()
                ->after('lms_module_id')
                ->constrained('lms_module_classes')
                ->nullOnDelete();

            $table->index(['lms_module_id', 'lms_module_class_id']);
        });
    }

    public function down(): void
    {
        Schema::table('lms_resources', function (Blueprint $table) {
            $table->dropForeign(['lms_module_class_id']);
            $table->dropColumn('lms_module_class_id');
        });

        Schema::table('lms_lessons', function (Blueprint $table) {
            $table->dropForeign(['lms_module_class_id']);
            $table->dropColumn('lms_module_class_id');
        });
    }
};
