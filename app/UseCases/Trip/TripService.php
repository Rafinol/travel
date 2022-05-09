<?php
namespace App\UseCases\Trip;

use App\Exceptions\RoutesNotReadyYetException;
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

    }

    public function getWays(TripRequest $request)
    {
        $trip = Trip::where(['departure_date' => $request['date'], 'from_id' => $request['from_id'], 'to_id' => $request['to_id']])->first();
        if(!$trip){
            $trip = Trip::new($request['from_id'], $request['to_id'], $request['date']);
        }
        $service = self::getService($trip->departure->name);
        try{
            return $service->getWays($trip);
        }
        catch (RoutesNotReadyYetException $e){
            return []; //TODO::Выдавать развернутый ответ и записывать в лог
        }

    }

    public static function getService($name) :DepartureService
    {
        if($name == 'Kazan'){
            $service = \App::make(KazanTripService::class);
        }
        else{
            $service = \App::make(DefaultTripService::class);
        }
        return $service;
    }
}
