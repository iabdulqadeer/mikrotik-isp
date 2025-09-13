<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void {
Schema::create('customers', function (Blueprint $t) {
$t->id();
$t->string('name');
$t->string('email')->nullable();
$t->string('phone')->nullable();
$t->string('address')->nullable();
$t->timestamps();
});
}
public function down(): void { Schema::dropIfExists('customers'); }
};