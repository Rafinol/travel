<?php
namespace App\UseCases\Trip\Departure;

use App\Helpers\ArrayHelper;
use App\Http\Request\Trip\TripRequest;
use App\Models\City\City;
use App\Models\Point\Point;
use App\Models\Point\PointType;
use App\Models\Route\Route;
use App\Models\Route\RouteSearchForm;
use App\Models\Route\RouteType;
use App\Models\Transport\TransportType;
use App\Models\Trip\Status;
use App\Models\Trip\Trip;
use App\Models\Way\PartWay;
use App\Models\Way\Way;
use App\UseCases\Trip\Type\BusTripService;
use App\UseCases\Trip\Type\AviaTripService;
use App\UseCases\Trip\Type\TrainTripService;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultTripService implements DepartureService
{
    protected AviaTripService $avia;
    protected TrainTripService $train;
    protected BusTripService $bus;

    const SLEEP_TIME = 10;

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
        if($trip->isCompleted() || $trip->isSearching()){
            return $trip;
        }
        $this->checkExistCities();
        array_map(function($route) use ($trip){
            $way = Way::new($trip->id, implode(' - ',$route));
            foreach (ArrayHelper::splitOfPairs($route) as $key => $pair){
                $city_from = City::where(['name' => $pair[0]])->first();
                $to_from = City::where(['name' => $pair[1]])->first();
                PartWay::new($city_from->id, $to_from->id, $way->id, $key);
            }
        }, $this->prepareRouteArray($trip));
        $trip->changeStatusToSearching();
        return $trip;
    }

    private function prepareRouteArray(Trip $trip) :array
    {
        $routes = array_map(function($value) use ($trip){
            array_unshift($value, $trip->departure->name);
            if(last($value) != $trip->arrival->name) {
                array_push($value, $trip->arrival->name);
            }
            return $value; // Add the beginning and end of the route to each element of the array
        }, $this->routes);
        array_unshift($routes, [$trip->departure->name, $trip->arrival->name]);
        return $routes;
    }

    /*public function search(Trip $trip) :void
    {
        $this->avia->search($trip);
        //$this->train->getWays($trip),
        //$this->bus->getWays($trip),
    }*/


    private function checkExistCities() :void
    {
        $cities = [];
        foreach ($this->routes as $route){
            foreach($route as $city){
                $cities[] = $city;
            }
        }
        $unique_cities  = array_unique($cities);
        if(count($unique_cities) != City::whereIn('name' , $unique_cities)->count()){
            throw new NotFoundHttpException('Some cities not found');
        }
    }

    public function search(Trip $trip) :void
    {
        /** @var Way $way */
        foreach ($trip->ways as $way){
            if($way->isCompleted()){
                continue;
            }
            $way->changeStatusToWaiting();
            $this->searchWay($trip, $way);
            $way->changeStatusToCompleted();
        }
        $trip->changeStatusToCompleted();
    }

    protected function searchWay(Trip $trip, Way $way) :void
    {
        foreach ($way->part_way as $part_way){
            if($part_way->arrival_date){ //if part_way has an arrival date, that`s mean it is done
                continue;
            }
            if($part_way->position == 0){
                $part_way->departure_date = $trip->departure_date;
            }
            else{
                $part_way->departure_date = $way->part_way[$part_way->position-1]->arrival_date;
            }
            $route_search_form = RouteSearchForm::firstOrCreate(['from_id' => $part_way->from_id, 'to_id' => $part_way->to_id, 'departure_date' => Carbon::parse($part_way->departure_date)->startOfDay()]);
            $part_way->route_search_form_id = $route_search_form->id;
            $this->requestAndSave($route_search_form);
            $part_way->arrival_date = collect($part_way->routeSearchForm->routes)->min('edate');
            $part_way->save();
        }
    }

    public function requestAndSave(RouteSearchForm $route_search_form) :void //Extendable
    {
        $this->avia->search($route_search_form);
        sleep(self::SLEEP_TIME);
        $routes = $this->avia->getRoutes($route_search_form);
        $this->saveRoutes($route_search_form, $routes);
        $this->avia->changeRouteSearchStatusToDone($route_search_form);

        /* You can also use other services such as bus or train. When it will be ready :)
            $this->bus->search($route_search_form);
            $routes = $this->bus->getRoutes($route_search_form);
            $this->saveRoutes($route_search_form, $routes);
            $this->bus->changeRouteSearchStatusToDone($route_search_form);
        */
    }

    protected function saveRoutes(RouteSearchForm $route_search, array $routes) :void
    {
        foreach ($routes as $key => $draft_route){
            foreach ($draft_route->routes as $droute){
                $from_point = Point::firstOrCreate(['code' => $droute->departure_point->code], ['name' => $droute->departure_point->name, 'address' => $droute->departure_point->name, 'type' => PointType::AIR_TYPE]);
                $to_point = Point::firstOrCreate(['code' => $droute->arrival_point->code], ['name' => $droute->arrival_point->name, 'address' => $droute->arrival_point->name, 'type' => PointType::AIR_TYPE]);
                $route = new Route();
                $route->type = RouteType::MOVING_TYPE;
                $route->price = $draft_route->price/count($draft_route->routes);
                $route->sdate = $droute->departure_date;
                $route->edate = $droute->arrival_date;
                $route->route_search_form_id = $route_search->id;
                $route->transport_type = TransportType::AIR_TYPE;
                $route->from_id = $from_point->code;
                $route->to_id = $to_point->code;
                $route->index = $key;
                $route->save();
            }
        }
    }

    public function processTrips() //For cron or queue
    {
        $trips = Trip::where(['status' => Status::SEARCHING_STATUS])->all();
        foreach ($trips as $trip){
            $this->search($trip);
        }
    }

}
