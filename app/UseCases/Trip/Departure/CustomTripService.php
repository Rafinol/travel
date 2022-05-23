<?php
namespace App\UseCases\Trip\Departure;

use App\Exceptions\RoutesNotReadyYetException;
use App\Http\Request\Trip\TripRequest;
use App\Models\City\City;
use App\Models\Route\RouteSearchForm;
use App\Models\RouteDto\RouteDto;
use App\Models\RouteDto\ResultRouteDto;
use App\Models\Point\Point;
use App\Models\Point\PointType;
use App\Models\Point\StationDto;
use App\Models\Route\PartRoute;
use App\Models\Route\RouteType;
use App\Models\Transport\TransportType;
use App\Models\Trip\Trip;
use App\Models\Way\PartWay;
use App\Models\Way\Way;
use App\Models\Way\WayStatus;
use App\Services\Travel\FlightTravelService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomTripService extends DefaultTripService implements DepartureService
{
    public array $routes = [
        /*['Moscow'], // Из Казани в Москву, из Москвы в точку направления
        ['Saint Petersburg'],
*/
        ['Kaliningrad','Gdansk'],// Из Казани в Калининград, из Калининграда в Гдянск, из Гдянска в точку направления
        ['Moscow','Kaliningrad','Gdansk'],// Из Казани в Москву, из Москвы в Калининград, из Калининграда в Гдянск, из Гдянска в точку направления

        ['Minsk','Vilnius',],
        ['Minsk', 'Riga',],
        ['Moscow','Minsk','Vilnius'],
        ['Moscow','Minsk','Riga'],

        ['Saint Petersburg','Tallinn'],
        ['Saint Petersburg','Helsinki'],
        ['Moscow','Saint Petersburg','Tallinn'],
        ['Moscow','Saint Petersburg','Helsinki'],
    ];

    public array $bus_pairs = [
        ['Saint Petersburg'=>'Tallinn'],
        ['Saint Petersburg'=>'Helsinki'],
        ['Minsk'=>'Vilnius'],
        ['Minsk'=>'Riga'],
        ['Kaliningrad'=>'Gdansk'],
    ];

    public function requestAndSave(RouteSearchForm $search_form) :void
    {
        if ($this->hasBusPairs($search_form)) {
            $this->saveRoutes($search_form, $this->getPersonalRoutes($search_form));
            return;
        }
        parent::requestAndSave($search_form);
    }

    public function hasBusPairs(RouteSearchForm $search_form) :bool
    {
        foreach ($this->bus_pairs as $bus_pair){
            if(isset($bus_pair[$search_form->departure->name]) && $bus_pair[$search_form->departure->name] == $search_form->arrival->name){
                return true;
            }
        }
        return false;
    }

    private function getPersonalRoutes(RouteSearchForm $search_form) :array
    {
        $bus_routes = [];
        for ($i=0; $i<24; $i+=4) {
            $bus_route = new RouteDto();
            $bus_route->departure_date = $search_form->departure_date->addHour($i);
            $bus_route->arrival_date = $search_form->departure_date->addHours($i+10);
            $bus_route->departure_point = new StationDto(str_replace(" ", '', $search_form->departure->name), $search_form->departure->name);
            $bus_route->arrival_point = new StationDto(str_replace(" ", '', $search_form->arrival->name), $search_form->arrival->name);
            $bus_route->transport_type = TransportType::BUS_TYPE;
            $bus_route->number = 'bus';
            $bus_routes[] = new ResultRouteDto(3000, [$bus_route]);
        }
        return $bus_routes;
    }

}
