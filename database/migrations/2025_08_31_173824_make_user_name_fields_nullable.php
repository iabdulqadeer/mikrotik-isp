<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Make new fields nullable so legacy inserts don't fail
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // If you really want to revert, set them back to NOT NULL with empty defaults
            $table->string('first_name')->default('')->nullable(false)->change();
            $table->string('last_name')->default('')->nullable(false)->change();
        });
    }
};
