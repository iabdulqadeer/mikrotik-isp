<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vouchers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $t->string('code', 64)->unique();
            $t->string('plan')->nullable();                 // e.g., "Internet Day", "1 Hour"
            $t->string('profile')->nullable();              // Mikrotik profile (optional)
            $t->unsignedInteger('duration_minutes')->default(60);
            $t->decimal('price', 10, 2)->default(0);
            $t->enum('status', ['active','used','expired','revoked'])->default('active');
            $t->timestamp('valid_from')->nullable();
            $t->timestamp('valid_until')->nullable();
            $t->text('notes')->nullable();

            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('used_by')->nullable()->constrained('users')->nullOnDelete();

            $t->timestamp('used_at')->nullable();
            $t->timestamps();

            $t->index(['status', 'device_id']);
            $t->index('valid_until');
        });
    }

    public function down(): void {
        Schema::dropIfExists('vouchers');
    }
};