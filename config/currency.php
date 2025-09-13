<?php

return [
    // Map ISO-3166-1 alpha-2 country -> currency info
    'by_country' => [
        'UG' => ['code' => 'UGX', 'symbol' => 'USh', 'name' => 'Ugandan Shilling'],
        'KE' => ['code' => 'KES', 'symbol' => 'KSh', 'name' => 'Kenyan Shilling'],
        'TZ' => ['code' => 'TZS', 'symbol' => 'TSh', 'name' => 'Tanzanian Shilling'],
        'RW' => ['code' => 'RWF', 'symbol' => 'FRw', 'name' => 'Rwandan Franc'],
        'ET' => ['code' => 'ETB', 'symbol' => 'Br',  'name' => 'Ethiopian Birr'],
        'SS' => ['code' => 'SSP', 'symbol' => '£',   'name' => 'South Sudanese Pound'],
        'CD' => ['code' => 'CDF', 'symbol' => 'FC',  'name' => 'Congolese Franc'],
        'SO' => ['code' => 'SOS', 'symbol' => 'Sh',  'name' => 'Somali Shilling'],
        'BI' => ['code' => 'BIF', 'symbol' => 'FBu', 'name' => 'Burundian Franc'],
    ],

    // Fallback when not mapped
    'default' => ['code' => 'USD', 'symbol' => '$', 'name' => 'US Dollar'],
];
