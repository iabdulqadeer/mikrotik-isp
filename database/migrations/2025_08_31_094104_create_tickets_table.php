<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void {
Schema::create('tickets', function (Blueprint $t) {
$t->id();
$t->string('number')->unique();
$t->foreignId('user_id')->nullable()->constrained();
$t->foreignId('opened_by')->constrained('users');
$t->string('subject');
$t->enum('priority', ['low','normal','high','urgent'])->default('normal');
$t->enum('status', ['open','pending','resolved','closed'])->default('open');
$t->timestamps();
});
}
public function down(): void { Schema::dropIfExists('tickets'); }
};
