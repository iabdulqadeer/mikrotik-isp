<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('plan_prices', function (Blueprint $t) {
            $t->id();

            // link back to your plans table
            $t->foreignId('plan_id')
              ->constrained('plans')
              ->cascadeOnDelete();

            // Stripe info
            $t->string('stripe_price_id')->unique();
            $t->string('currency', 10)->default('usd');
            $t->unsignedInteger('amount'); // in cents
            $t->string('interval')->default('month'); // day, week, month, year
            $t->unsignedInteger('interval_count')->default(1);

            // optional role assignment / features
            $t->string('role_name')->nullable();
            $t->json('features')->nullable();

            $t->boolean('active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('plan_prices');
    }
};
