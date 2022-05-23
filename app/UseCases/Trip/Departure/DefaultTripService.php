<?php
namespace App\UseCases\Trip\Departure;

use App\Helpers\ArrayHelper;
use App\Http\Request\Trip\TripRequest;
use App\Models\City\City;
use App\Models\Point\Point;
use App\Models\Point\PointType;
use App\Models\Route\PartRoute;
use App\Models\Route\Route;
use App\Models\Route\RouteSearchForm;
use App\Models\Route\RouteType;
use App\Models\Transport\TransportType;
use App\Models\Trip\Status;
use App\Models\Trip\Trip;
use App\Models\Way\PartWay;
use App\Models\Way\Way;
use App\Repositories\Routes\RouteRepository;
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

    const SLEEP_TIME = 25;

    public array $routes = []; //Additional intermediate routes to search
    private RouteRepository $repository;

    public function __construct(AviaTripService $avia, TrainTripService $train, BusTripService $bus, RouteRepository $repository)
    {
        $this->avia = $avia;
        $this->train = $train;
        $this->bus = $bus;
        $this->repository = $repository;
    }

    public function firstOrNew(string $from, string $to, Carbon $date) :Trip
    {
        $departure_city = City::where('name', $from)->first();
        $arrival_city = City::where('name', $to)->first();
        $trip = Trip::where(['departure_date' => $date, 'from_id' => $departure_city->id, 'to_id' => $arrival_city->id])->first();
        if(!$trip){
            $trip = Trip::new($departure_city->id, $arrival_city->id, $date->startOfDay());
        }
        return $trip;
    }

    public function getTrip(string $from, string $to, Carbon $date) :Trip
    {
        $trip = $this->firstOrNew($from, $to, $date); //I don`t use firstOrCreate from Eloquent because need to set status in trip
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
        foreach ($way->partWays as $part_way){
            if($part_way->arrival_date){ //if part_way has an arrival date, that`s mean it is done
                continue;
            }
            if($part_way->position == 0){
                $part_way->departure_date = $trip->departure_date;
            }
            else{
                $part_way->departure_date = $way->partWays[$part_way->position-1]->arrival_date;
            }
            $route_search_form = RouteSearchForm::firstOrCreate(['from_id' => $part_way->from_id, 'to_id' => $part_way->to_id, 'departure_date' => $this->getDepartureDate($part_way->departure_date)]);
            $part_way->route_search_form_id = $route_search_form->id;
            $part_way->save();
            $this->requestAndSave($route_search_form);
            $this->setArrivalDate($part_way);
        }
    }

    private function getDepartureDate(Carbon $date) :Carbon
    {
        if($date->format('H') > 20){
            $date->addDay();
        }
        return $date->startOfDay();
    }

    private function setArrivalDate(PartWay $part_way) :void
    {
        $route = $this->repository->getCheapestRouteByPartWay($part_way);
        $part_way->arrival_date = $route->arrival_date;
        $part_way->min_price = $route->price;
        $part_way->save();
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
        foreach ($routes as $draft_route){
            $route = Route::new(reset($draft_route->routes)->departure_date, last($draft_route->routes)->arrival_date, $draft_route->price, $route_search->id, count($draft_route->routes));
            if(count($draft_route->routes) > 3){
                continue;
            }
            foreach ($draft_route->routes as $droute){
                $from_point = Point::firstOrCreate(['code' => $droute->departure_point->code], ['name' => $droute->departure_point->name, 'address' => $droute->departure_point->name, 'type' => PointType::AIR_TYPE]);
                $to_point = Point::firstOrCreate(['code' => $droute->arrival_point->code], ['name' => $droute->arrival_point->name, 'address' => $droute->arrival_point->name, 'type' => PointType::AIR_TYPE]);
                $part_route = new PartRoute();
                $part_route->type = RouteType::MOVING_TYPE;
                $part_route->sdate = $droute->departure_date;
                $part_route->edate = $droute->arrival_date;
                $part_route->transport_type = $droute->transport_type;
                $part_route->from_id = $from_point->code;
                $part_route->to_id = $to_point->code;
                $part_route->route_id = $route->id;
                $part_route->save();
            }
        }
    }

    public function getBestWays(Trip $trip) :array
    {
        $ways = [];
        foreach ($trip->ways as $way){
            foreach ($way->partWays as $part_way){
                $route = $this->repository->getCheapestRouteByPartWay($part_way, $ways[$way->id]['last_arrival']->addHours(2) ?? null);
                if(!$route){
                    unset($ways[$way->id]);
                    break;
                }
                $ways[$way->id]['details'][] = ['part_way' => $part_way, 'route' => $route];
                $ways[$way->id]['last_arrival'] = $route->partRoutes->last()->edate;
                $ways[$way->id]['price'] = $route->price + ($ways[$way->id]['price'] ?? 0);
                $ways[$way->id]['flights_count'] = $route->transfers_count + ($ways[$way->id]['flights_count'] ?? 0);
            }
        }
        return collect($ways)->sortBy('price')->all();
    }


}
