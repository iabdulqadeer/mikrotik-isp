<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void {
Schema::create('invoices', function (Blueprint $t) {
$t->id();
$t->string('number')->unique();
$t->foreignId('customer_id')->constrained();
$t->foreignId('subscription_id')->nullable()->constrained();
$t->decimal('amount', 10, 2);
$t->date('due_date');
$t->enum('status', ['draft','unpaid','paid','void'])->default('unpaid');
$t->json('meta')->nullable();
$t->timestamps();
});
}
public function down(): void { Schema::dropIfExists('invoices'); }
};
