<?php


namespace App\UseCases\Trip\Departure;


use App\Helpers\ArrayHelper;
use App\Http\Request\Trip\TripRequest;
use App\Models\City\City;
use App\Models\Trip\Trip;
use App\Models\Way\PartWay;
use App\Models\Way\Way;
use App\UseCases\Trip\Type\BusTripService;
use App\UseCases\Trip\Type\AviaTripService;
use App\UseCases\Trip\Type\TrainTripService;

class CommonTripService implements DepartureService
{
    protected AviaTripService $avia;
    protected TrainTripService $train;
    protected BusTripService $bus;

    public array $routes = []; //Additional intermediate routes to search

    public function __construct(AviaTripService $avia, TrainTripService $train, BusTripService $bus)
    {
        $this->avia = $avia;
        $this->train = $train;
        $this->bus = $bus;
    }

    public function firstOrNew(TripRequest $request) :Trip
    {
        $trip = Trip::where(['departure_date' => $request['date'], 'from_id' => $request['from_id'], 'to_id' => $request['to_id']])->first();
        if(!$trip){
            $trip = Trip::new($request['from_id'], $request['to_id'], $request['date']);
        }
        return $trip;
    }

    public function getTrip(TripRequest $request) :Trip
    {
        $trip = $this->firstOrNew($request); //I don`t use firstOrCreate from Eloquent because need to set status in trip
        if($trip->isCompleted()){
            return $trip;
        }
        array_map(function($route) use ($trip){
            $way = Way::new($trip->id, implode(' - ',$route));
            foreach (ArrayHelper::splitOfPairs($route) as $key => $pair){
                $city_from = City::where(['name' => $pair[0]])->first();
                $to_from = City::where(['name' => $pair[1]])->first();
                PartWay::new($city_from->id, $to_from->id, $way->id, $key);
            }
        }, $this->prepareRouteArray($request));
        $this->search($trip); //TODO::Вынести в очередь
        return $trip;
    }

    public function search(Trip $trip) :void
    {
        $trip->changeStatusToWaiting();
        $this->avia->search($trip);
        //$this->train->getWays($trip),
        //$this->bus->getWays($trip),
    }

    private function prepareRouteArray(TripRequest $request) :array
    {
        $routes = array_map(function($value) use ($request){
            return array_merge($request['from'], $value, $request['to']); // Add the beginning and end of the route to each element of the array
        }, $this->routes);
        return array_merge([$request['from'], $request['to']],$routes);
    }

}
