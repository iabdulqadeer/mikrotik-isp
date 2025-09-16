<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained('devices')->cascadeOnDelete();

            // ISP fields
            $table->string('router_username')->nullable(); // hotspot/pppoe username
            $table->string('router_password')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();

            // ISP subscription status
            $table->enum('status', ['active','suspended','expired'])->default('active');

            // Stripe fields
            $table->string('type')->nullable();
            $table->string('stripe_id')->unique()->nullable();
            $table->string('stripe_status')->nullable();
            $table->string('stripe_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            // Meta
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'stripe_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
