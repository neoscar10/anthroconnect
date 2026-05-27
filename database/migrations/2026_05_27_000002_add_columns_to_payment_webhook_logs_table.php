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
        Schema::table('payment_webhook_logs', function (Blueprint $table) {
            $table->text('exception_trace')->nullable()->after('failure_reason');
            $table->integer('retry_count')->default(0)->after('exception_trace');
            $table->string('transaction_reference')->nullable()->index()->after('retry_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_webhook_logs', function (Blueprint $table) {
            $table->dropColumn(['exception_trace', 'retry_count', 'transaction_reference']);
        });
    }
};
