<?php
namespace Database\Seeders;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['Basic 5 Mbps',  5120, 1024,  '999.00',  'monthly', true],
            ['Starter 10',   10240, 2048, '1499.00', 'monthly', true],
            ['Weekly Burst', 20480, 4096,  '450.00',  'weekly',  true],
            ['Daily Lite',    2048,  512,   '60.00',  'daily',   false],
        ];

        foreach ($rows as [$name,$down,$up,$price,$cycle,$active]) {
            Plan::firstOrCreate(
                ['name' => $name],
                [
                    'speed_down_kbps' => $down,
                    'speed_up_kbps'   => $up,
                    'price'           => $price,
                    'billing_cycle'   => $cycle,
                    'active'          => $active,
                ]
            );
        }
    }
}
