<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->nullable()->after('id');
                $table->index('owner_id', 'users_owner_id_index');
                $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete();
            }

            try { $table->dropUnique('users_email_unique'); } catch (\Throwable $e) {}
            try { $table->dropUnique('users_username_unique'); } catch (\Throwable $e) {}

            $table->unique(['owner_id','email'], 'users_owner_email_unique');
            $table->unique(['owner_id','username'], 'users_owner_username_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            try { $table->dropUnique('users_owner_email_unique'); } catch (\Throwable $e) {}
            try { $table->dropUnique('users_owner_username_unique'); } catch (\Throwable $e) {}

            try { $table->unique('email', 'users_email_unique'); } catch (\Throwable $e) {}
            try { $table->unique('username', 'users_username_unique'); } catch (\Throwable $e) {}

            try { $table->dropForeign(['owner_id']); } catch (\Throwable $e) {}
            try { $table->dropIndex('users_owner_id_index'); } catch (\Throwable $e) {}
            if (Schema::hasColumn('users', 'owner_id')) {
                $table->dropColumn('owner_id');
            }
        });
    }
};
