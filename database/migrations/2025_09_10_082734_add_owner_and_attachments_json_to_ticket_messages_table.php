<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Add owner_id (nullable), index, and FK with an explicit name
        Schema::table('ticket_messages', function (Blueprint $t) {
            if (!Schema::hasColumn('ticket_messages', 'owner_id')) {
                $t->unsignedBigInteger('owner_id')->nullable()->after('id');
                $t->index('owner_id', 'ticket_messages_owner_id_index');
            }
        });

        Schema::table('ticket_messages', function (Blueprint $t) {
            // Only add the FK if it doesn't already exist in this exact name
            // (Laravel doesn't expose FK-exists, so we just try and ignore if present)
            try {
                $t->foreign('owner_id', 'fk_ticket_messages_owner_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');
            } catch (\Throwable $e) {
                // ignore if already exists
            }
        });

        // 2) Ensure attachments is JSON (nullable), and place it after 'body'
        Schema::table('ticket_messages', function (Blueprint $t) {
            $afterCol = Schema::hasColumn('ticket_messages', 'body') ? 'body' : 'id';

            if (Schema::hasColumn('ticket_messages', 'attachments')) {
                // Convert to JSON if needed; not all DBs support change() smoothly, so try/catch
                try {
                    $t->json('attachments')->nullable()->change();
                } catch (\Throwable $e) {
                    try { $t->dropColumn('attachments'); } catch (\Throwable $ex) {}
                    $t->json('attachments')->nullable()->after($afterCol);
                }
            } else {
                $t->json('attachments')->nullable()->after($afterCol);
            }
        });

        // IMPORTANT: We do NOT touch ticket_id/user_id FKs here.
        // They are defined in the base migration with explicit names already.
    }

    public function down(): void
    {
        Schema::table('ticket_messages', function (Blueprint $t) {
            // Drop our explicit owner FK and index if present
            try { $t->dropForeign('fk_ticket_messages_owner_id'); } catch (\Throwable $e) {}
            try { $t->dropIndex('ticket_messages_owner_id_index'); } catch (\Throwable $e) {}

            // Drop the column if present
            if (Schema::hasColumn('ticket_messages', 'owner_id')) {
                try { $t->dropColumn('owner_id'); } catch (\Throwable $e) {}
            }

            // We leave attachments as-is (JSON). If you need to revert, uncomment:
            // if (Schema::hasColumn('ticket_messages', 'attachments')) {
            //     try { $t->dropColumn('attachments'); } catch (\Throwable $e) {}
            // }
        });
    }
};
