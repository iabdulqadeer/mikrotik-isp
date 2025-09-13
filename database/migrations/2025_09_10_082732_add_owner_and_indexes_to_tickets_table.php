<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Add owner_id (who owns/controls this ticket)
            if (!Schema::hasColumn('tickets', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->after('id');
                $table->index('owner_id', 'tickets_owner_id_index');
                $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
            }

            // Helpful indexes (filtering/sorting)
            if (!Schema::hasColumn('tickets', 'status')) {
                // (your table already has status per provided schema)
            } else {
                $table->index('status', 'tickets_status_index');
            }
            if (!Schema::hasColumn('tickets', 'priority')) {
                // (already exists)
            } else {
                $table->index('priority', 'tickets_priority_index');
            }

            // Make ticket number unique per owner (optional but recommended)
            try { $table->unique(['owner_id','number'], 'tickets_owner_number_unique'); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            try { $table->dropUnique('tickets_owner_number_unique'); } catch (\Throwable $e) {}
            try { $table->dropForeign(['owner_id']); } catch (\Throwable $e) {}
            try { $table->dropIndex('tickets_owner_id_index'); } catch (\Throwable $e) {}
            try { $table->dropIndex('tickets_status_index'); } catch (\Throwable $e) {}
            try { $table->dropIndex('tickets_priority_index'); } catch (\Throwable $e) {}

            if (Schema::hasColumn('tickets','owner_id')) {
                $table->dropColumn('owner_id');
            }
        });
    }
};
