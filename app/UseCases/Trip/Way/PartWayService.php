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

    public function __construct(SearchRoutesService $routesSearchService)
    {
        $this->routesSearchService = $routesSearchService;
    }

    public function create(City $departure, City $arrival, Way $way, int $position, Carbon $date) :void
    {
        PartWay::new($departure->id, $arrival->id, $way->id, $position, $date);
    }

    public function prepareForSearch(PartWay $part_way) :void
    {
        if($part_way->isCompleted()){
            return;
        }
        if(!$part_way->departure_date){
            return;
        }
        $this->saveSearchForm($part_way);
    }

    private function saveSearchForm(PartWay $part_way) :void
    {
        if($part_way->route_search_form_id){
            return;
        }
        $route_search_form = $this->routesSearchService->getOrCreateSearchForm($part_way->departure, $part_way->arrival, $part_way->departure_date);
        $part_way->route_search_form_id = $route_search_form->id;
        $part_way->save();
    }
}
