<?php
namespace App\UseCases\Trip\Departure;

use App\Exceptions\RoutesNotReadyYetException;
use App\Http\Request\Trip\TripRequest;
use App\Models\City\City;
use App\Models\RouteDto\RouteDto;
use App\Models\RouteDto\ResultRouteDto;
use App\Models\Point\Point;
use App\Models\Point\PointType;
use App\Models\Point\StationDto;
use App\Models\Route\Route;
use App\Models\Route\RouteType;
use App\Models\Transport\TransportType;
use App\Models\Trip\Trip;
use App\Models\Way\PartWay;
use App\Models\Way\Way;
use App\Models\Way\WayStatus;
use App\Services\Travel\FlightTravelService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CustomTripService extends DefaultTripService implements DepartureService
{
    public array $routes = [
        ['Moscow'], // Из Казани в Москву, из Москвы в точку направления
        ['St Petersburg'],

        ['Kaliningrad','Gdansk'],// Из Казани в Калининград, из Калининграда в Гдянск, из Гдянска в точку направления
        ['Moscow','Kaliningrad','Gdansk'],// Из Казани в Москву, из Москвы в Калининград, из Калининграда в Гдянск, из Гдянска в точку направления

        ['Minsk','Vilnius',],
        ['Minsk', 'Riga',],
        ['Moscow','Minsk','Vilnius'],
        ['Moscow','Minsk','Riga'],

        ['St Petersburg','Tallinn'],
        ['St Petersburg','Helsinki'],
        ['Moscow','St Petersburg','Tallinn'],
        ['Moscow','St Petersburg','Helsinki'],
    ];

    public array $bus_pairs = [
        ['St Petersburg'=>'Tallinn'],
        ['St Petersburg'=>'Helsinki'],
        ['Minsk'=>'Vilnius'],
        ['Minsk'=>'Riga'],
        ['Kaliningrad'=>'Gdansk'],
    ];

    public function requestAndSave(PartWay $part_way) :void
    {
        if (isset($this->bus_pairs[$part_way->departure->name]) && $this->bus_pairs[$part_way->departure->name] == $part_way->arrival->name) {
            $this->saveRoutes($part_way, $this->getPersonalRoutes($part_way));
            return;
        }
        parent::requestAndSave($part_way);
    }

    private function getPersonalRoutes(PartWay $part_way) :array
    {
        $bus_route = new RouteDto();
        $bus_route->arrival_date = $part_way->arrival_date;
        $bus_route->departure_date = $part_way->arrival_date->addHours(10);
        $bus_route->departure_point = new StationDto(str_replace(" ", '', $part_way->departure->name), $part_way->departure->name);
        $bus_route->arrival_point = new StationDto(str_replace(" ", '', $part_way->arrival->name), $part_way->arrival->name);
        $bus_route->number = 'bus';
        return [
            new ResultRouteDto(3000, [
                $bus_route
            ])
        ];
    }

}
