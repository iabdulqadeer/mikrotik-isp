<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('plans', function (Blueprint $t) {
            $t->string('stripe_price_id')->nullable()->after('price');
        });
    }

    public function down(): void {
        Schema::table('plans', function (Blueprint $t) {
            $t->dropColumn('stripe_price_id');
        });
    }
};
