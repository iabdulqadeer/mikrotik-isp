<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('speed_down_kbps')->default(0);
            $table->unsignedBigInteger('speed_up_kbps')->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->enum('billing_cycle', ['daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
