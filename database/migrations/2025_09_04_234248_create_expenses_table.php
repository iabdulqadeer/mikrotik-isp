<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // core fields
            $table->string('type')->index();             // e.g. Internet, Fuel, Rent, Other
            $table->decimal('amount', 15, 2);            // money
            $table->dateTime('spent_at')->index();       // date field
            $table->string('payment_method')->index();   // e.g. Cash, Bank, Mobile Money, Card, Other

            // optional
            $table->string('receipt_path')->nullable();  // stored file path
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
