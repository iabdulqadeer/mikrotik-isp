<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        foreach ([
            'admin',
            'operator',
            'support',
            'accountant',
            'viewer',
            'user',
        ] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => $guard]);
        }
    }
}
