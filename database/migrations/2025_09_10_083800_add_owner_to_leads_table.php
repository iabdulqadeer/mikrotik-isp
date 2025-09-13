<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads','owner_id')) {
                $table->unsignedBigInteger('owner_id')->after('id');
                $table->index('owner_id','leads_owner_id_index');
                $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            try { $table->dropForeign(['owner_id']); } catch (\Throwable $e) {}
            try { $table->dropIndex('leads_owner_id_index'); } catch (\Throwable $e) {}
            if (Schema::hasColumn('leads','owner_id')) {
                $table->dropColumn('owner_id');
            }
        });
    }
};
