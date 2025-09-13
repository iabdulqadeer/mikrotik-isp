<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_trial_flags_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','trial_started_at')) {
                $table->timestamp('trial_started_at')->nullable()->after('trial_ends_at');
            }
            if (!Schema::hasColumn('users','trial_used')) {
                $table->boolean('trial_used')->default(false)->after('trial_started_at');
            }
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['trial_started_at','trial_used']);
        });
    }
};
