<?php


namespace App\Repositories\Routes;


use App\Models\Route\Route;
use App\Models\Trip\Trip;
use App\Models\Way\PartWay;

class RouteRepository
{
    public function getCheapestRoutesByTrip(Trip $trip, int $transfer_time=0) :array
    {
        return \DB::select('SELECT * FROM
            (Select w.id as wid,
                    pw.id as pwid,
                    r.id as rid,
                    price,
                    r.departure_date,
                    r.arrival_date,
                    transfers_count,
                    dc.name as departure_name,
                    ac.name as arrival_name,
                    ROW_NUMBER() OVER (PARTITION BY pw.id Order BY price, transfers_count) AS row_num
            FROM routes r
            JOIN part_ways pw on r.route_search_form_id = pw.route_search_form_id
            JOIN ways w on pw.way_id = w.id
            JOIN cities dc on pw.from_id = dc.id
            JOIN cities ac on pw.to_id = ac.id
            WHERE w.trip_id = ? and r.departure_date > IF(pw.position > 0, (pw.departure_date+INTERVAL ? hour), pw.departure_date)
            GROUP BY w.id, pw.id, price, r.departure_date, r.arrival_date, transfers_count, dc.name, ac.name, r.id
            ORDER BY w.id, price, transfers_count) as x
        WHERE row_num < 2
        ORDER BY departure_date, price', [$trip->id, $transfer_time]);
    }

    public function getCheapestRouteByPartWay(PartWay $part_way, int $transfer_time=0) :Route|null
    {
        $part_way->departure_date = $part_way->departure_date->addHours($transfer_time);
        return $this->buildRouteQueryByPartWay($part_way)
             ->orderBy('price')
             ->orderBy('transfers_count')
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
