<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('users', function (Blueprint $t) {
      if (!Schema::hasColumn('users','phone')) $t->string('phone', 32)->nullable()->index();
      if (!Schema::hasColumn('users','last_login_at')) $t->timestamp('last_login_at')->nullable()->index();
      if (!Schema::hasColumn('users','username')) $t->string('username')->nullable()->unique();
      if (!Schema::hasColumn('users','internet_profile_id')) $t->foreignId('internet_profile_id')->nullable()->constrained()->nullOnDelete();
    });
  }
  public function down(): void {
    Schema::table('users', function (Blueprint $t) {
      if (Schema::hasColumn('users','internet_profile_id')) $t->dropConstrainedForeignId('internet_profile_id');
      foreach (['phone','last_login_at','username'] as $c) if (Schema::hasColumn('users',$c)) $t->dropColumn($c);
    });
  }
};