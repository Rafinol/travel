<?php
namespace App\UseCases\Trip\Departure;

use App\Exceptions\RoutesNotReadyYetException;
use App\Http\Request\Trip\TripRequest;
use App\Models\City\City;
use App\Models\Point\Point;
use App\Models\Point\PointType;
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

class CustomTripService extends CommonTripService implements DepartureService
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


    public function search(Trip $trip) :void
    {
        foreach($trip->ways as $way){
            if(!$way->isCreated()){
                continue;
            }
            $way->changeStatusToWaiting();
            foreach ($way->part_way as $part_way){
                if (isset($this->bus_pairs[$part_way->departure->name]) && $this->bus_pairs[$part_way->departure->name] == $part_way->arrival->name) {
                    //TODO:: create bus route
                    continue;
                }
                $this->avia->search($part_way);
            }
        }
    }

}
