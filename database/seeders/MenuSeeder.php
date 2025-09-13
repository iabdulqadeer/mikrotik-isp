<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use Spatie\Permission\Models\Role;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard','web');

        // ---- (optional but recommended once) clean reset to avoid stale self-parent rows
        DB::table('menu_role')->truncate();
        Menu::truncate();

        // Ensure roles exist
        foreach (['admin','operator','support','accountant','viewer','user'] as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => $guard]);
        }
        $roles = Role::where('guard_name', $guard)->pluck('id','name');

        $roleIdsFor = function(array $roleNames) use ($roles): array {
            if (empty($roleNames)) return $roles->values()->all(); // visible to all
            return $roles->only($roleNames)->values()->all();
        };

        // IMPORTANT: key by (label + parent_id) so children can reuse labels safely
        $mk = function(array $data, array $roleNames = [], $parent = null) use ($roleIdsFor) {
            $menu = Menu::updateOrCreate(
                [
                    'label'     => $data['label'],
                    'parent_id' => $parent?->id,   // <-- part of the upsert key
                ],
                [
                    'icon'       => $data['icon'] ?? null,
                    'route_name' => $data['route'] ?? null,
                    'url'        => $data['url'] ?? null,
                    'permission' => $data['permission'] ?? null,
                    'sort_order' => $data['order'] ?? 0,
                    'is_active'  => $data['active'] ?? true,
                ]
            );
            $menu->roles()->sync($roleIdsFor($roleNames));
            return $menu;
        };

        // 1) Dashboard
        $dashboard = $mk([
            'label' => 'Dashboard',
            'icon'  => 'dashboard',
            'route' => 'dashboard',
            'permission' => 'dashboard.view',
            'order' => 10,
        ], ['admin','operator','support','accountant','viewer','user']);

        // 2) Users (category)
        $usersCat = $mk([
            'label' => 'Users',
            'order' => 20,
        ], ['admin','operator','support','accountant','viewer','user']);

        $mk(['label'=>'Active Users','icon'=>'activity','url'=>'/users/active','route'=>'users.active','permission'=>'users.active','order'=>21],
            ['admin','operator','support','accountant','viewer'], $usersCat);

        // Rename child label to avoid visual confusion (optional). You can keep "Users" too.
        $mk(['label'=>'Users','icon'=>'users','route'=>'users.index','permission'=>'users.list','order'=>22],
            ['admin','operator','accountant','viewer'], $usersCat);

        $mk(['label'=>'Tickets','icon'=>'ticket','route'=>'tickets.index','permission'=>'tickets.list','order'=>23],
            ['admin','operator','support','viewer'], $usersCat);

        $mk(['label'=>'Leads','icon'=>'user-plus','route'=>'leads.index','permission'=>'leads.list','order'=>24],
            ['admin','operator','support','accountant','viewer'], $usersCat);

        // 3) Finance
        $finCat = $mk([
            'label' => 'Finance',
            'order' => 30,
        ], ['admin','operator','support','accountant','viewer','user']);

        $mk(['label'=>'Packages','icon'=>'layers','route'=>'plans.index','permission'=>'plans.list','order'=>31],
            ['admin','operator','support','accountant','viewer'], $finCat);

        $mk(['label'=>'Payments','icon'=>'money','route'=>'invoices.index','permission'=>'invoices.list','order'=>32],
            ['admin','operator','accountant','viewer'], $finCat);

        $mk(['label'=>'Vouchers','icon'=>'ticket','route'=>'vouchers.index','permission'=>'vouchers.list','order'=>33],
            ['admin','operator','support','accountant','viewer','user'], $finCat);

        $mk(['label'=>'Expenses','icon'=>'receipt','route'=>'expenses.index','permission'=>'expenses.list','order'=>34],
            ['admin','accountant'], $finCat);

        // 4) Communication
        $comCat = $mk([
            'label' => 'Communication',
            'order' => 40,
        ], ['admin','operator','support','accountant','viewer']);

        $mk(['label'=>'Sms','icon'=>'message','route'=>'sms.index','permission'=>'sms.list','order'=>41],
            ['admin','operator'], $comCat);

        $mk(['label'=>'Emails','icon'=>'mail','route'=>'emails.index','permission'=>'emails.list','order'=>42],
            ['admin','operator','support','accountant','viewer'], $comCat);

        $mk(['label'=>'Campaigns','icon'=>'megaphone','route'=>'campaigns.index','permission'=>'campaigns.list','order'=>43],
            ['admin','operator','support','accountant','viewer'], $comCat);

        // 5) Devices
        $devCat = $mk([
            'label' => 'Devices',
            'order' => 50,
        ], ['admin','operator','support','accountant','viewer']);

        $mk(['label'=>'MikroTik','icon'=>'server','route'=>'devices.index','permission'=>'devices.list','order'=>51],
            ['admin','operator','support','accountant','viewer'], $devCat);

        $mk(['label'=>'Equipment','icon'=>'hard-drive','route'=>'equipment.index','permission'=>'equipment.list','order'=>52],
            ['admin','operator','support','accountant','viewer'], $devCat);
    }
}
