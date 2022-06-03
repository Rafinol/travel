<?php
namespace App\Dto\RouteDto;

class PointType
{
    const BUS_TYPE = 'bus_station';
    const AIR_TYPE = 'airport';
    const TRAIN_TYPE = 'train_station';

    public static function getTypes() :array
    {
        return [self::AIR_TYPE, self::BUS_TYPE, self::TRAIN_TYPE];
    }
}
