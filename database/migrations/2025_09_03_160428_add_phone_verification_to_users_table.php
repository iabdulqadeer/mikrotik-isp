<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_phone_verification_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->string('phone_verification_code', 10)->nullable();
            $table->timestamp('phone_verification_expires_at')->nullable();
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_verified_at','phone_verification_code','phone_verification_expires_at']);
        });
    }
};
