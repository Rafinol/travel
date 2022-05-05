<?php
namespace App\UseCases\Trip;

use App\Http\Request\Trip\TripRequest;
use App\Models\Trip\Trip;
use App\UseCases\Trip\Departure\DefaultTripService;
use App\UseCases\Trip\Departure\DepartureService;
use App\UseCases\Trip\Departure\KazanTripService;

class TripService
{
    public function create(TripRequest $request)
    {
        $trip = Trip::new($request['from_id'], $request['to_id'], $request['date']);
        $service = self::getService($trip->departure->name);
        var_dump($service->getWays());

    }

    public static function getService($name) :DepartureService
    {
        if($name == 'Kazan'){
            $service = \App::make(KazanTripService::class);
        }
        else{
            $service = \App::make(DefaultTripService::class);
        }
    }
}
