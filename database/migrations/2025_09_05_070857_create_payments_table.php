<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Who owns this payment (the ISP/admin account)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Optional link to subscription
            $table->foreignId('subscription_id')
                  ->nullable()
                  ->constrained('subscriptions')
                  ->nullOnDelete();

            // Optional link to plan/package
            $table->foreignId('plan_id')
                  ->nullable()
                  ->constrained('plans')
                  ->nullOnDelete();

            // Payment details
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('USD');
            $table->enum('status', ['pending','paid','failed','refunded'])
                  ->default('paid');
            $table->string('method')->nullable();     // e.g. momo, airtel, visa
            $table->string('reference')->nullable();  // transaction ref
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
