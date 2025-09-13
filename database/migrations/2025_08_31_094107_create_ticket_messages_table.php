<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void {
Schema::create('ticket_messages', function (Blueprint $t) {
$t->id();
$t->foreignId('ticket_id')->constrained();
$t->foreignId('user_id')->nullable()->constrained();
$t->text('body');
$t->timestamps();
});
}
public function down(): void { Schema::dropIfExists('ticket_messages'); }
};