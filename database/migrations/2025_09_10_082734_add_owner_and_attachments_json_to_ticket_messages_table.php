<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            // Add owner_id (same owner as parent ticket)
            if (!Schema::hasColumn('ticket_messages', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->after('id');
                $table->index('owner_id', 'ticket_messages_owner_id_index');
                $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
            }

            // Ensure attachments is JSON (nullable)
            if (Schema::hasColumn('ticket_messages', 'attachments')) {
                // Change to JSON if not already (not all DBs support "change" reliably)
                try {
                    $table->json('attachments')->nullable()->change();
                } catch (\Throwable $e) {
                    // Fallback: drop & re-add as json
                    try { $table->dropColumn('attachments'); } catch (\Throwable $ex) {}
                    $table->json('attachments')->nullable()->after('body');
                }
            } else {
                $table->json('attachments')->nullable()->after('body');
            }

            // Add missing FKs if needed
            try { $table->foreign('ticket_id')->references('id')->on('tickets')->cascadeOnDelete(); } catch (\Throwable $e) {}
            try { $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete(); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            try { $table->dropForeign(['owner_id']); } catch (\Throwable $e) {}
            try { $table->dropIndex('ticket_messages_owner_id_index'); } catch (\Throwable $e) {}
            if (Schema::hasColumn('ticket_messages','owner_id')) {
                $table->dropColumn('owner_id');
            }

            // Keep attachments as json; if you must revert, uncomment:
            // try { $table->dropColumn('attachments'); } catch (\Throwable $e) {}
            // $table->text('attachments')->nullable();

            // Drop optional FKs
            try { $table->dropForeign(['ticket_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
        });
    }
};
