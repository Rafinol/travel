<?php
namespace App\UseCases\Trip\Departure;

use App\Models\Point\Point;
use App\Models\Route\Route;
use App\Models\Route\RouteType;
use App\Models\Transport\TransportType;
use App\Models\Trip\Trip;
use App\Models\Way\Way;
use App\Services\Travel\TravelService;
use Illuminate\Database\Eloquent\Model;

class KazanTripService implements DepartureService
{
    private TravelService $service;

    public function __construct(TravelService $service)
    {
        $this->service = $service;
    }

    public function getWays(Trip $trip) :array
    {
        $ways = $trip->ways;
        if($ways && $this->isCompletedWays($ways)){
            return $ways;
        }

        /** Kazan - Destination_point part (only flights) */
        $way = Way::new($trip->id, 'Just Flights');
        $flight_results = $this->service->getRoutes($way);
        $routes = [];
        foreach ($flight_results as $key => $flights){
            foreach ($flights['flights'] as $flight){
                $from_point = Point::findOrNew($flight->departure_point->code, ['name' => $flight->departure_point->name])->save();
                $to_point = Point::findOrNew($flight->arrival_point->code, ['name' => $flight->arrival_point->name])->save();
                $route = new Route();
                $route->type = RouteType::MOVING_TYPE;
                $route->price = $flights['price']/count($flights['flights']);
                $route->sdate = $flight->departure_date;
                $route->edate = $flight->arrival_date;
                $route->way_id = $way->id;
                $route->transport_type = TransportType::AIR_TYPE;
                $route->from_id = $from_point->code;
                $route->to_id = $to_point->code;
                $route->index = $key;
                $route->save();
                $routes[$way->id][$key][] = $route;
            }
        }
        $way->changeStatusToCompleted();
        return $routes;
    }

    private function isCompletedWays(array $ways){
        foreach ($ways as $way){
            if(!$way->isCompleted()){
                return false;
            }
        }
        return true;
    }


}
