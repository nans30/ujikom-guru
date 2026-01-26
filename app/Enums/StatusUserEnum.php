<?php

namespace App\Enums;

class StatusUserEnum
{
    const INACTIVE = 0;
    const ACTIVE = 1;

    public static function getStatus()
    {
        return [
            self::INACTIVE => 'Inactive',
            self::ACTIVE => 'Active',
        ];
    }
}
