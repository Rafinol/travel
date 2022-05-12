<?php
namespace App\Services\Travel\Agregators;


use App\Models\Flight\Flight;
use App\Models\Flight\FlightResult;
use App\Models\Point\Station;
use JetBrains\PhpStorm\Pure;

class YandexFlight
{
    public array $flights;
    public array $stations;
    public array $fares;

    public function __construct(array $response)
    {
        $this->flights = collect($response['reference']['flights'])->keyBy('key')->all();
        $this->stations = collect($response['reference']['stations'])->keyBy('id')->all();
        $this->fares = collect($response['variants']['fares'])->sortBy(function ($value, $key){
            return $this->getMinFlightPrice($value['prices']);
        })->all();
    }

    public function getFlights() :array
    {
        $flights = [];
        foreach($this->fares as $fare){
            $routes = [];
            foreach ($fare['route'[0]] as $route_key){
                $routes[] = $this->create($route_key);
            }
            $flights[] = new FlightResult($this->getMinFlightPrice($fare['prices']), $routes);
        }
        return $flights;
    }

    #[Pure] public function create(string $route_key) :Flight
    {
        $fr = $this->flights[$route_key]; //flight route
        $flight = new Flight();
        $flight->departure_date = $fr['departure']['local'];
        $flight->arrival_date = $fr['arrival']['local'];
        $flight->flight_number = $fr['number'];
        $flight->departure_point = $this->getStation($fr['from']);
        $flight->arrival_point = $this->getStation($fr['to']);
        return $flight;
    }

    #[Pure] private function getStation($id) :Station
    {
        $station = $this->stations[$id];
        return new Station($station['code'], $station['title']);
    }

    private function getMinFlightPrice(array $values) :int
    {
        return min(
            array_map(function($element){
                return $element['tariff']['value'];
            }, $values)
        );
    }
}
