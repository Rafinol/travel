<?php


namespace App\UseCases\Trip\Type;

use App\Services\Travel\FlightTravelService;

/* * * * * * * * * * * * * * * * * * * * * * * * *
 *                                               *
 *  https://openflights.org/data.html#schedule   *
 *                                               *
 * * * * * * * * * * * * * * * * * * * * * * * * */

class AviaTripService
{
    use TripTrait;
    private FlightTravelService $service;

    public function __construct(FlightTravelService $service)
    {
        $this->service = $service;
    }

}
