<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // sender
            $table->string('subject');
            $table->longText('message');                 // HTML (from Tiptap/Editor)
            $table->string('to_email')->nullable();      // single or comma-separated
            $table->json('cc')->nullable();              // array of emails
            $table->json('bcc')->nullable();             // array of emails
            $table->enum('status', ['draft','queued','sent','failed'])->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['status','sent_at']);
            $table->index(['subject']);
            $table->index(['to_email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
