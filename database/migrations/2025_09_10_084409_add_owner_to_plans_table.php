<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->after('id');
                $table->index('owner_id', 'plans_owner_id_index');
                $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
            }

            // Optional but recommended if "name" should be unique per owner
            try { $table->unique(['owner_id','name'], 'plans_owner_name_unique'); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            try { $table->dropUnique('plans_owner_name_unique'); } catch (\Throwable $e) {}
            try { $table->dropForeign(['owner_id']); } catch (\Throwable $e) {}
            try { $table->dropIndex('plans_owner_id_index'); } catch (\Throwable $e) {}
            if (Schema::hasColumn('plans','owner_id')) {
                $table->dropColumn('owner_id');
            }
        });
    }
};
