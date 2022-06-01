<?php
namespace App\Models\Transport;

class TransportType
{
    const AIR_TYPE = 'air';
    const BUS_TYPE = 'bus';
    const TRAIN_TYPE = 'train';

    public static function getTypes() :array
    {
        return [self::AIR_TYPE, self::BUS_TYPE, self::TRAIN_TYPE];
    }
}
