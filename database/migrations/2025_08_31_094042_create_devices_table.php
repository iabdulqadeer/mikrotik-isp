<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void {
Schema::create('devices', function (Blueprint $t) {
$t->id();
$t->uuid('uuid')->unique();
$t->string('name');
$t->string('host'); // IP or DNS
$t->unsignedSmallInteger('port')->default(8728);
$t->boolean('ssl')->default(false);
$t->string('username');
$t->text('password_encrypted'); // encrypted cast
$t->string('identity')->nullable();
$t->json('options')->nullable(); // timeouts, attempts, etc.
$t->string('provision_token')->unique();
$t->timestamp('last_seen_at')->nullable();
$t->foreignId('created_by')->constrained('users');
$t->timestamps();
});
}
public function down(): void { Schema::dropIfExists('devices'); }
};
