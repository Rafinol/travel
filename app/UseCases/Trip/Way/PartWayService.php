<?php
namespace App\UseCases\Trip\Way;

use App\Models\City\City;
use App\Models\Route\RouteSearchForm;
use App\Models\Way\PartWay;
use App\Models\Way\Way;
use App\UseCases\Trip\Route\SearchRoutesService;
use Carbon\Carbon;

class PartWayService
{
    private SearchRoutesService $routesSearchService;
    const HOUR_UNTIL = 20;

    public function __construct(SearchRoutesService $routesSearchService)
    {
        $this->routesSearchService = $routesSearchService;
    }

    public function create(City $departure, City $arrival, Way $way, int $position, Carbon $date) :void
    {
        PartWay::new($departure->id, $arrival->id, $way->id, $position, $date);
    }

    public function search(PartWay $part_way) :void
    {
        if($part_way->isCompleted()){
            return;
        }
        $route_search_form = $this->getAndSaveSearchForm($part_way);
        $routes = $this->routesSearchService->search($route_search_form);
        $this->saveRoutes();
    }

    private function getAndSaveSearchForm(PartWay $part_way) :RouteSearchForm
    {
        if($part_way->route_search_form_id){
            return $part_way->routeSearchForm;
        }
        $route_search_form = $this->routesSearchService->getOrCreateSearchForm($part_way->departure, $part_way->arrival, $this->getDepartureDate($part_way->departure_date));
        $part_way->route_search_form_id = $route_search_form->id;
        $part_way->save();
        return $route_search_form;
    }

    private function getDepartureDate(Carbon $date) :Carbon //if arrival date after e.q. 8 p.m. would be better to find routes for the next day
    {
        if($date->format('H') > self::HOUR_UNTIL){
            $date->addDay();
        }
        return $date->startOfDay();
    }

}
