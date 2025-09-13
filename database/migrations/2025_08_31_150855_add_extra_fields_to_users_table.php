<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','first_name')) {
                $table->string('first_name')->after('id');
            }
            if (!Schema::hasColumn('users','last_name')) {
                $table->string('last_name')->after('first_name');
            }
            if (!Schema::hasColumn('users','phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users','whatsapp')) {
                $table->string('whatsapp')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users','customer_care')) {
                $table->string('customer_care')->nullable()->after('whatsapp');
            }
            if (!Schema::hasColumn('users','business_address')) {
                $table->text('business_address')->nullable()->after('customer_care');
            }
            if (!Schema::hasColumn('users','country')) {
                $table->string('country', 5)->nullable()->after('business_address');
            }

            // If you previously added a plain "role" column, remove it:
            if (Schema::hasColumn('users','role')) {
                $table->dropColumn('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name','last_name','phone','whatsapp','customer_care','business_address','country'
            ]);
            // Do not re-add a "role" column; Spatie handles roles in its own tables.
        });
    }
};

