<?php


namespace App\UseCases\Trip\Type;


use App\Exceptions\RoutesNotReadyYetException;
use App\Models\Point\Point;
use App\Models\Point\PointType;
use App\Models\Route\Route;
use App\Models\Route\RouteType;
use App\Models\Transport\TransportType;
use App\Models\Trip\Trip;
use App\Models\Way\PartWay;
use App\Models\Way\Way;
use App\Services\Travel\FlightTravelService;
use Illuminate\Database\Eloquent\Model;

abstract class AviaTripService
{
    private FlightTravelService $service;

    public function __construct(FlightTravelService $service)
    {
        $this->service = $service;
    }

    public function search(Trip $trip) :void
    {
        /** @var Way $way */
        foreach ($trip->ways as $way){
            $way->changeStatusToWaiting();
            $this->waySearch($trip, $way);
            $way->changeStatusToCompleted();
        }
    }

    private function waySearch(Trip $trip, Way $way) :void
    {
        foreach ($way->part_way as $key => $part_way){
            if($key == 0){
                $part_way->departure_date = $trip->departure_date;
            }
            else{
                $part_way->departure_date = $way->part_way[$key-1]->arrival_date;
            }
            $this->service->search($part_way);
            sleep(10);
            $this->saveRoutes($part_way);
            $part_way->arrival_date = collect($part_way->routes)->min('edate');
            $part_way->save();
        }
    }

    private function saveRoutes(PartWay $part_way) :void
    {
        $draft_routes = $this->service->getRoutes($part_way);
        foreach ($draft_routes as $key => $draft_route){
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
        }
    }


}
/*try{
    $flight_results = $this->service->getRoutes($just_flight_way);
}
catch (RoutesNotReadyYetException $e){
    $just_flight_way->changeStatusToWaiting();
    return $just_flight_way;
}*/
