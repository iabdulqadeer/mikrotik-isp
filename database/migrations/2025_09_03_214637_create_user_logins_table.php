<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ip', 45)->nullable();          // IPv4/IPv6
            $table->text('user_agent')->nullable();
            $table->timestamp('logged_in_at')->useCurrent();
            $table->timestamps();                           // for bookkeeping
            $table->index(['user_id','logged_in_at']);
            $table->index('ip');
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_logins');
    }
};
