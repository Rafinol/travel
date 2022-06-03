<?php
namespace App\Services\Travel\Yandex;


use App\Dto\RouteDto\PointType;
use App\Dto\RouteDto\RouteDto;
use App\Dto\RouteDto\ResultRouteDto;
use App\Dto\RouteDto\StationDto;
use App\Models\Transport\TransportType;
use Carbon\Carbon;
use JetBrains\PhpStorm\Pure;

class YandexFlight
{
    public array $flights;
    public array $stations;
    public array $fares;

    public function __construct(array $response)
    {
        $this->flights = collect($response['reference']['flights'])
            ->keyBy('key')
            ->all();
        $this->stations = collect($response['reference']['stations'])
            ->keyBy('id')
            ->all();
        $this->fares = collect($response['variants']['fares'])
            ->sortBy(function ($value, $key){
                return $this->getMinFlightPrice($value['prices']);
        })->all();
    }

    public function getFlights() :array
    {
        $flights = [];
        foreach($this->fares as $fare){
            $routes = [];
            foreach ($fare['route'][0] as $route_key){
                $routes[] = $this->create($route_key);
            }
            $flights[] = new ResultRouteDto(
                $this->getMinFlightPrice($fare['prices']),
                reset($routes)->departure_date,
                last($routes)->arrival_date,
                $routes);
        }
        return $flights;
    }

    #[Pure] public function create(string $route_key) :RouteDto
    {
        $fr = $this->flights[$route_key]; //flight route
        $flight = new RouteDto();
        $flight->departure_date = Carbon::parse($fr['departure']['local']);
        $flight->arrival_date = Carbon::parse($fr['arrival']['local']);
        $flight->number = $fr['number'];
        $flight->departure_point = $this->getStation($fr['from']);
        $flight->arrival_point = $this->getStation($fr['to']);
        $flight->transport_type = TransportType::AIR_TYPE;
        return $flight;
    }

    #[Pure] private function getStation($id) :StationDto
    {
        $station = $this->stations[$id];
        return new StationDto($station['code'], $station['title'], PointType::AIR_TYPE);
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
