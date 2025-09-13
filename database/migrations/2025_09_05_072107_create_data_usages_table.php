<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_usages', function (Blueprint $table) {
            $table->id();

            // Link to the account/tenant that owns this usage
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Optional link to subscription / plan (if you want per-package tracking)
            $table->foreignId('subscription_id')
                  ->nullable()
                  ->constrained('subscriptions')
                  ->nullOnDelete();

            $table->foreignId('plan_id')
                  ->nullable()
                  ->constrained('plans')
                  ->nullOnDelete();

            // Date of usage
            $table->date('date');

            // Usage values
            $table->decimal('downloaded_mb', 12, 2)->default(0);
            $table->decimal('uploaded_mb', 12, 2)->default(0);

            $table->timestamps();

            $table->unique(['user_id','date','plan_id'], 'usage_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_usages');
    }
};
