<?php
namespace App\UseCases\Trip;

use App\Models\City\City;
use App\Models\Route\PartRoute;
use App\Models\Trip\Trip;
use App\Models\Way\PartWay;
use App\Repositories\Routes\RouteRepository;
use App\UseCases\Trip\Way\WayService;
use Carbon\Carbon;

class TripService
{
    private WayService $wayService;
    private RouteRepository $repository;

    const TRANSFER_TIME = 1;

    public function __construct(WayService $wayService, RouteRepository $repository)
    {
        $this->wayService = $wayService;
        $this->repository = $repository;
    }

    public function getPreparedTrip(string $from, string $to, Carbon $date) :Trip
    {
        $trip = $this->create($from, $to, $date);
        $this->prepareForSearch($trip);
        return $trip;
    }

    public function create(string $from, string $to, Carbon $date) :Trip
    {
        $departure = City::where('name', $from)->first();
        $arrival = City::where('name', $to)->first();
        $trip = $this->getOrCreateTrip($departure, $arrival, $date);
        if(!$trip->isCreated()){ //if already created and has any processing status
            return $trip;
        }
        $this->wayService->create($trip);
        $trip->changeStatusToWaiting();
        return $trip;
    }

    public function prepareForSearch(Trip $trip) :void
    {
        if($trip->isCompleted()){
            return;
        }
        $trip->changeStatusToSearching();
        foreach ($trip->ways as $way){
            $this->wayService->prepareForSearch($way);
        }
    }

    private function getOrCreateTrip(City $departure_city, City $arrival_city, Carbon $date) :Trip
    {
        $trip = Trip::where(['departure_date' => $date, 'from_id' => $departure_city->id, 'to_id' => $arrival_city->id])->first();
        if(!$trip){
            $trip = Trip::new($departure_city->id, $arrival_city->id, $date->startOfDay());
        }
        return $trip;
    }

    public function getCheapestWays(Trip $trip) :array
    {
        $ways = [];

        $routes = $this->repository->getCheapestRoutesByTrip($trip);
        $route_ids = collect($routes)->unique('rid')->pluck('rid')->all();

        $part_routes = PartRoute::with('departure', 'arrival')->whereIn('route_id', $route_ids)->get();
        $collect_part_routes = collect($part_routes)->groupBy('route_id')->all();

        foreach ($routes as $route){
            $ways[$route->wid]['details'][] = ['route' => $route, 'part_routes' => $collect_part_routes[$route->rid]];
            $ways[$route->wid]['price'] = $route->price + ($ways[$route->wid]['price'] ?? 0);
            $ways[$route->wid]['flights_count'] = $route->transfers_count + ($ways[$route->wid]['flights_count'] ?? 0);
        }

        return collect($ways)->sortBy('price')->all();

        /*$trip = $trip->load('ways.partWays');
        foreach ($trip->ways as $way){
            foreach ($way->partWays as $part_way){
                if($part_way->position != 0){
                    $part_way->departure_date = $part_way->departure_date->addHours(self::TRANSFER_TIME);
                }
                $route = $this->repository->getCheapestRouteByPartWay($part_way, 2);
                if(!$route){
                    unset($ways[$way->id]);
                    break;
                }
                $ways[$way->id]['details'][] = ['part_way' => $part_way, 'route' => $route];
                $ways[$way->id]['price'] = $route->price + ($ways[$way->id]['price'] ?? 0);
                $ways[$way->id]['flights_count'] = $route->transfers_count + ($ways[$way->id]['flights_count'] ?? 0);
            }
        }
        return collect($ways)->sortBy('price')->all();*/
    }

}
