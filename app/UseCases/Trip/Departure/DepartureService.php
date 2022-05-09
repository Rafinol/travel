<?php


namespace App\UseCases\Trip\Departure;


use App\Models\Trip\Trip;

interface DepartureService
{
    public function getWays(Trip $trip) :array;
}
