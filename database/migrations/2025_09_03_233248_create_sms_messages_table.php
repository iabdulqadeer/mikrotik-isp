<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone', 32);
            $table->text('message');
            $table->string('status', 32)->default('queued'); // queued|sent|delivered|failed
            $table->string('twilio_sid', 64)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->string('error_code', 32)->nullable();
            $table->string('error_message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('twilio_sid');
            $table->index('phone');
        });
    }
    public function down(): void {
        Schema::dropIfExists('sms_messages');
    }
};