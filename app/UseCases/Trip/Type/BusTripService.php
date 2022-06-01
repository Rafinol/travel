<?php


namespace App\UseCases\Trip\Type;


use App\Models\Route\RouteSearchForm;
use App\Services\Travel\BusRideTravelService;

class BusTripService
{
    use TripTrait;

    private BusRideTravelService $service;
    private array $exclusive_bus_pairs;

    public function __construct(array $exclusive_bus_pairs, BusRideTravelService $service)
    {
        //config/travelways.php
        $this->exclusive_bus_pairs = $exclusive_bus_pairs;
        $this->service = $service;
    }

    public function hasExclusiveRoute(RouteSearchForm $form) :bool
    {
        foreach ($this->exclusive_bus_pairs as $route){
            $departure = key($route);
            $arrival = reset($route);
            if($departure == $form->departure->name && $arrival == $form->arrival->name){
                return true;
            }
        }
        return false;
    }
}
