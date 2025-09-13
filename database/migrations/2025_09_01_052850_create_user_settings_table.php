<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_settings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('key');
            $t->longText('value')->nullable(); // JSON or string
            $t->timestamps();
            $t->unique(['user_id','key']);
        });
    }
    public function down(): void { Schema::dropIfExists('user_settings'); }
};
