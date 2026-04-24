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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'whatsapp_phone')) {
                $table->string('whatsapp_phone')->nullable()->unique()->after('email');
            }

            if (!Schema::hasColumn('users', 'whatsapp_phone_verified_at')) {
                $table->timestamp('whatsapp_phone_verified_at')->nullable()->after('whatsapp_phone');
            }

            if (!Schema::hasColumn('users', 'otp_verified_at')) {
                $table->timestamp('otp_verified_at')->nullable()->after('whatsapp_phone_verified_at');
            }

            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('remember_token');
            }

            // Make email nullable
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_phone',
                'whatsapp_phone_verified_at',
                'otp_verified_at',
                'last_login_at',
            ]);
            
            // Revert email to non-nullable (careful if nulls exist)
            $table->string('email')->nullable(false)->change();
        });
    }
};
