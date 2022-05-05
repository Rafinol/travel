<?php
namespace App\UseCases\Trip\Departure;

class DefaultTripService implements DepartureService
{
    public function getWays() :array
    {
        return ['one_way', 'two_way'];
    }
}
