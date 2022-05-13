<?php


namespace App\UseCases\Trip\Departure;


use App\Helpers\ArrayHelper;
use App\Http\Request\Trip\TripRequest;
use App\Models\City\City;
use App\Models\Point\Point;
use App\Models\Point\PointType;
use App\Models\Route\Route;
use App\Models\Route\RouteType;
use App\Models\Transport\TransportType;
use App\Models\Trip\Status;
use App\Models\Trip\Trip;
use App\Models\Way\PartWay;
use App\Models\Way\Way;
use App\Models\Way\WaySearch;
use App\UseCases\Trip\Type\BusTripService;
use App\UseCases\Trip\Type\AviaTripService;
use App\UseCases\Trip\Type\TrainTripService;
use Illuminate\Database\Eloquent\Model;

class DefaultTripService implements DepartureService
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
        if($trip->isCompleted() || $trip->isSearching()){
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
        $trip->changeStatusToSearching();
        return $trip;
    }

    private function prepareRouteArray(TripRequest $request) :array
    {
        $routes = array_map(function($value) use ($request){
            return array_merge($request['from'], $value, $request['to']); // Add the beginning and end of the route to each element of the array
        }, $this->routes);
        return array_merge([$request['from'], $request['to']],$routes);
    }

    /*public function search(Trip $trip) :void
    {
        $this->avia->search($trip);
        //$this->train->getWays($trip),
        //$this->bus->getWays($trip),
    }*/

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
            $this->requestAndSave($part_way);
            $part_way->arrival_date = collect($part_way->routes)->min('edate');
            $part_way->save();
        }
    }

    public function requestAndSave(PartWay $part_way) :void //Extendable
    {
        $this->avia->search($part_way);
        sleep(10);
        $routes = $this->avia->getRoutes($part_way);
        $this->saveRoutes($part_way, $routes);

        /* You can also use other services such as bus or train. When it will be reade :)
         * $this->bus->search($part_way);
        $routes = $this->bus->getRoutes($part_way);
        $this->saveRoutes($part_way, $routes);
        */
    }

    protected function saveRoutes(PartWay $part_way, array $routes) :void
    {
        foreach ($routes as $key => $draft_route){
            $part_way->price = $draft_route->price;
            foreach ($draft_route->flights as $flight){
                $from_point = Point::firstOrCreate(['code' => $flight->departure_point->code], ['name' => $flight->departure_point->name, 'address' => $flight->departure_point->name, 'type' => PointType::AIR_TYPE]);
                $to_point = Point::firstOrCreate(['code' => $flight->arrival_point->code], ['name' => $flight->arrival_point->name, 'address' => $flight->arrival_point->name, 'type' => PointType::AIR_TYPE]);
                $route = new Route();
                $route->type = RouteType::MOVING_TYPE;
                $route->price = $draft_route['price']/count($draft_route['flights']);
                $route->sdate = $flight->departure_date;
                $route->edate = $flight->arrival_date;
                $route->part_way_id = $part_way->id;
                $route->transport_type = TransportType::AIR_TYPE;
                $route->from_id = $from_point->code;
                $route->to_id = $to_point->code;
                $route->index = $key;
                $route->save();
            }
            $part_way->save();
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
