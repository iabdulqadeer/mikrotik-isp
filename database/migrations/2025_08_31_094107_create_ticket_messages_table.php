<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_messages', function (Blueprint $t) {
            $t->id();

            // Columns
            $t->unsignedBigInteger('ticket_id');
            $t->unsignedBigInteger('user_id')->nullable();
            $t->text('body');
            $t->timestamps();

            // Indexes
            $t->index('ticket_id', 'ticket_messages_ticket_id_index');
            $t->index('user_id', 'ticket_messages_user_id_index');

            // Explicit, globally-unique FK names (avoid default names to prevent collisions)
            $t->foreign('ticket_id', 'fk_ticket_messages_ticket_id')
              ->references('id')->on('tickets')
              ->onDelete('cascade');

            $t->foreign('user_id', 'fk_ticket_messages_user_id')
              ->references('id')->on('users')
              ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_messages', function (Blueprint $t) {
            // Drop FKs by the explicit names we set
            try { $t->dropForeign('fk_ticket_messages_ticket_id'); } catch (\Throwable $e) {}
            try { $t->dropForeign('fk_ticket_messages_user_id'); } catch (\Throwable $e) {}
        });

        Schema::dropIfExists('ticket_messages');
    }
};
