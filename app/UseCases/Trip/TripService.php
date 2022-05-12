<?php
namespace App\UseCases\Trip;

use App\Exceptions\RoutesNotReadyYetException;
use App\Http\Request\Trip\TripRequest;
use App\Models\Trip\Trip;
use App\UseCases\Trip\Departure\DefaultTripService;
use App\UseCases\Trip\Departure\DepartureService;
use App\UseCases\Trip\Departure\CustomTripService;

class TripService
{
    private DepartureService $service;

    public function __construct(DepartureService $service)
    {
        $this->service = $service;
    }
    public function createOrFirst(TripRequest $request) :Trip
    {
        $trip = Trip::where(['departure_date' => $request['date'], 'from_id' => $request['from_id'], 'to_id' => $request['to_id']])->first();
        if(!$trip){
            $trip = Trip::new($request['from_id'], $request['to_id'], $request['date']);
        }
        return $trip;
    }

    public function getWays(TripRequest $request)
    {
        $trip = $this->createOrFirst($request); // Don`t use firstOrCreate from Eloquent because need to set status in trip
        $ways[] = $this->service->getWays($trip);
    }

    public function getWayOptions(string $city_from, string $city_to) :array
    {
        $options[$city_from][] = $city_to;
        foreach ($array as $key => $items){
            if($key == $city_from){
                foreach ($items as $item){
                    $options[$city_from][] = $this->getWayOptions($item, $city_to);
                }
                break;
            }
            else{
                $options[$city_from] = $city_to;
            }
        }
        return $options;
    }

    public static function getService($name) :DepartureService
    {
        if($name == 'Kazan'){
            $service = \App::make(CustomTripService::class);
        }
        else{
            $service = \App::make(DefaultTripService::class);
        }
        return $service;
    }
}
