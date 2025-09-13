<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            AdminUserSeeder::class,
            // PlanSeeder::class,
            MenuSeeder::class,
        ]);

    }
}
