<?php
namespace App\Models\Route;

class RouteType
{
    const MOVING_TYPE = 'moving';
    const WAITING_TYPE = 'waiting';

    public static function getTypes() :array
    {
        return [self::MOVING_TYPE, self::WAITING_TYPE];
    }
}
