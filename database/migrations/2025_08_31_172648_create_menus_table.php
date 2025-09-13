<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('menus', function (Blueprint $t) {
            $t->id();
            $t->string('label');
            $t->string('icon')->nullable();          // e.g. 'home', 'users', ...
            $t->string('route_name')->nullable();    // e.g. 'users.index'
            $t->string('url')->nullable();           // fallback for external or url(...)
            $t->string('permission')->nullable();    // spatie permission (e.g. 'users.list')
            $t->foreignId('parent_id')->nullable()->constrained('menus')->cascadeOnDelete();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('menu_role', function (Blueprint $t) {
            $t->id();
            $t->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $t->foreignId('role_id')->constrained('roles')->cascadeOnDelete(); // spatie roles table
            $t->unique(['menu_id','role_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('menu_role');
        Schema::dropIfExists('menus');
    }
};