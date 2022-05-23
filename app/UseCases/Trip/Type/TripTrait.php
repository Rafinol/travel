<?php


namespace App\UseCases\Trip\Type;


use App\Models\Route\RouteSearchForm;
use App\Models\Way\PartWay;
use App\Models\Route\RouteSearch;
use App\Models\Route\RouteSearchStatus;
use App\Services\Travel\FlightTravelService;

trait TripTrait
{
    public function getOrCreate(RouteSearchForm $route_search_form) :RouteSearch
    {
        $route_search = RouteSearch::where(['type' => $this->service->getServiceName(), 'route_search_form_id' => $route_search_form->id,])->where('created_at', '>', now()->subDay())->first();
        if(!$route_search) {
            $route_search = RouteSearch::new($route_search_form->id, $this->service->getServiceName());
        }
        if($route_search->isDone()){
            return $route_search;
        }
        $search_id = $this->service->search($route_search_form);
        $route_search->update(['search_id'=> $search_id, 'status' => RouteSearchStatus::DONE_STATUS]);
        return $route_search;
    }

    public function getRoutes(RouteSearch $route_search) :array
    {
        return $this->service->getRoutes($route_search);
    }

    public function changeRouteSearchStatusToDone(RouteSearchForm $route_search_form) :void
    {
        $route_search = RouteSearch::where(['type' => $this->service->getServiceName(), 'route_search_form_id' => $route_search_form->id,])->first();
        $route_search->changeStatusToDone();
    }

}
