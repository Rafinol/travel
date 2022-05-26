<?php


namespace App\Repositories\Routes;


use App\Models\Route\Route;
use App\Models\Way\PartWay;

class RouteRepository
{
    const TRANSFER_TIME = 1; //HOUR
    public function getCheapestRouteByPartWay(PartWay $part_way, int $transfer_time=self::TRANSFER_TIME) :Route|null
    {
        $part_way->departure_date = $part_way->departure_date->addHours($transfer_time);
        return $this->buildRouteQueryByPartWay($part_way)
             ->orderBy('transfers_count')
             ->orderBy('price')
             ->first();
    }

    public function getEarliestRouteByPartWay(PartWay $part_way) :Route|null
    {
        return $this->buildRouteQueryByPartWay($part_way)
             ->orderBy('transfers_count')
             ->orderBy('arrival_date')
             ->first();
    }

    public function getLatestRouteByPartWay(PartWay $part_way) :Route|null
    {
        return $this->buildRouteQueryByPartWay($part_way)
             ->orderBy('transfers_count')
             ->orderBy('arrival_date', 'desc')
             ->first();
    }

    private function buildRouteQueryByPartWay(PartWay $part_way)
    {
        return Route::where('route_search_form_id', $part_way->route_search_form_id)
            ->where('departure_date', '>=', $part_way->departure_date);
    }
}
