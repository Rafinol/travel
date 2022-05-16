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
use App\Models\Way\RouteSearch;
use App\Services\Travel\FlightTravelService;
use Illuminate\Database\Eloquent\Model;

class AviaTripService
{
    use TripTrait;
    private FlightTravelService $service;

    public function __construct(FlightTravelService $service)
    {
        $this->service = $service;
    }



}
/*try{
    $flight_results = $this->service->getRoutes($just_flight_way);
}
catch (RoutesNotReadyYetException $e){
    $just_flight_way->changeStatusToWaiting();
    return $just_flight_way;
}


$this->processRoutes($part_way);

*/
