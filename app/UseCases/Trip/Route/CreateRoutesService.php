<?php


namespace App\UseCases\Trip\Route;


use App\Models\Point\Point;
use App\Models\Point\PointType;
use App\Models\Point\StationDto;
use App\Models\Route\PartRoute;
use App\Models\Route\Route;
use App\Models\Route\RouteSearchForm;
use App\Models\Route\RouteType;
use App\Models\RouteDto\RouteDto;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CreateRoutesService
{
    const MAX_TRANSFERS = 3;

    public function saveRoutes(RouteSearchForm $route_form, array $raw_routes) :void
    {
        foreach ($raw_routes as $raw_route){
            $route = Route::new(
                reset($raw_route->routes)->departure_date,
                last($raw_route->routes)->arrival_date,
                $raw_route->price,
                $route_form->id,
                count($raw_route->routes)
            );
            if(count($raw_route->routes) > self::MAX_TRANSFERS){
                continue;
            }
            foreach ($raw_route->routes as $raw_part_route){
                $this->savePartRoutes($route->id, $raw_part_route);
            }
        }
    }

    public function savePartRoutes(int $route_id, RouteDto $raw_part_route) :void
    {
        $from_point = $this->getOrSavePoint($raw_part_route->departure_point);
        $to_point = $this->getOrSavePoint($raw_part_route->arrival_point);
        $part_route = new PartRoute();
        $part_route->type = RouteType::MOVING_TYPE;
        $part_route->sdate = $raw_part_route->departure_date;
        $part_route->edate = $raw_part_route->arrival_date;
        $part_route->transport_type = $raw_part_route->transport_type;
        $part_route->from_id = $from_point->code;
        $part_route->to_id = $to_point->code;
        $part_route->route_id = $route_id;
        $part_route->save();
    }

    private function getOrSavePoint(StationDto $station) :Point
    {
        return Point::firstOrCreate(
            ['code' => $station->code],
            [
                'name' => $station->name,
                'address' => $station->name,
                'type' => $station->type
            ]);

    }

}
