<?php


namespace App\UseCases\Trip\Type;


use App\Models\Route\RouteSearchForm;
use App\Models\Way\PartWay;
use App\Models\Route\RouteSearch;
use App\Models\Route\RouteSearchStatus;
use App\Services\Travel\FlightTravelService;

trait TripTrait
{
    public function search(RouteSearchForm $route_search) :void
    {
        $way_search = RouteSearch::where(['type' => $this->service->getServiceName(), 'route_search_form_id' => $route_search->id,])->where('created_at', '>', now()->subDay())->first();
        if(!$way_search) {
            $way_search = RouteSearch::new($route_search->id, $this->service->getServiceName());
        }
        if($way_search->isDone() || $way_search->isWaiting()){
            return;
        }
        $search_id = $this->service->search($route_search);
        $way_search->update(['search_id'=> $search_id, 'status' => RouteSearchStatus::WAITING_STATUS]);
    }

    public function getRoutes(RouteSearchForm $route_search_form) :array
    {
        $route_search = RouteSearch::where(['type' => $this->service->getServiceName(), 'route_search_form_id' => $route_search_form->id,])->first();
        return $this->service->getRoutes($route_search);
    }

    public function changeRouteSearchStatusToDone(RouteSearchForm $route_search_form) :void
    {
        $route_search = RouteSearch::where(['type' => $this->service->getServiceName(), 'route_search_form_id' => $route_search_form->id,])->first();
        $route_search->changeStatusToDone();
    }

}
