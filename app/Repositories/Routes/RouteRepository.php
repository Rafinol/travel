<?php


namespace App\Repositories\Routes;


use App\Models\Route\Route;
use App\Models\Way\PartWay;
use Carbon\Carbon;

class RouteRepository
{
    public function getCheapestRouteByPartWay(PartWay $part_way, Carbon|null $departure_date=null) :Route|null
    {
        return Route::where('route_search_form_id', $part_way->route_search_form_id)
             ->where('departure_date', '>=', ($departure_date ?: $part_way->departure_date)->addHours(2))
             //->where('arrival_date', '<=', $part_way->arrival_date)
             ->orderBy('transfers_count')
             ->orderBy('price')
             ->first();
    }
}
