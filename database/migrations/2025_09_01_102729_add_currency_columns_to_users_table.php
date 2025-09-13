<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('currency_code', 8)->nullable()->after('country');     // e.g., UGX
            $table->string('currency_symbol', 8)->nullable()->after('currency_code'); // e.g., USh
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['currency_code','currency_symbol']);
        });
    }
};
