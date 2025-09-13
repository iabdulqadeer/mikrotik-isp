<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('type', 40);                 // enum string (Router, Switch, etc.)
            $t->string('name');                     // Equipment name/model
            $t->string('serial_number')->nullable()->index();
            $t->decimal('price', 12, 2)->default(0);       // Equipment price
            $t->decimal('paid_amount', 12, 2)->nullable(); // Amount user has already paid
            $t->string('currency', 8)->default('USD');     // keep simple; align with your settings if you have one
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->softDeletes();

            $t->index(['type', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
