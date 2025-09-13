<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['Admin',      'admin@example.com',      'admin'],
            ['Operator',   'operator@example.com',   'operator'],
            ['Support',    'support@example.com',    'support'],
            ['Accountant', 'accountant@example.com', 'accountant'],
            ['Viewer',     'viewer@example.com',     'viewer'],
            ['Demo User',  'user@example.com',       'user'],
        ];

        foreach ($users as [$name, $email, $role]) {
            // split name -> first_name, last_name (simple split; adjust to your needs)
            [$first, $last] = array_pad(explode(' ', $name, 2), 2, null);

            $u = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'       => $name,                 // keep legacy 'name' too
                    'first_name' => $first,
                    'last_name'  => $last,
                    'password'   => Hash::make('password'),
                ]
            );
            $u->syncRoles([$role]);
        }
    }
}
