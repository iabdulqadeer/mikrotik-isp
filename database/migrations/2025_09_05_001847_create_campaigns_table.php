<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->enum('type', ['banner', 'image']);

            // Banner fields
            $table->string('banner_text')->nullable();

            // Image fields
            $table->string('image_size')->nullable(); // e.g., 'full','wide','square'
            $table->string('image_path')->nullable();

            // Scheduling
            $table->date('start_date');
            $table->date('end_date')->nullable();

            // Metrics
            $table->unsignedBigInteger('views')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
