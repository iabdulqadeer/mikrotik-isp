<?php

namespace App\Enums;

enum EquipmentType:string {
    case Router = 'Router';
    case Switch = 'Switch';
    case Server = 'Server';
    case AccessPoint = 'Access Point';
    case Other = 'Other';

    public static function options(): array
    {
        return array_map(fn($c) => $c->value, self::cases());
    }
}
