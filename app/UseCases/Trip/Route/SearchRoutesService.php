<?php


namespace App\UseCases\Trip\Route;


use App\Models\City\City;
use App\Models\Route\RouteSearchForm;
use App\UseCases\Trip\Type\AviaTripService;
use App\UseCases\Trip\Type\BusTripService;
use App\UseCases\Trip\Type\TrainTripService;
use Carbon\Carbon;

class SearchRoutesService
{
    private BusTripService $bus;
    private TrainTripService $train;
    private AviaTripService $avia;

    public function __construct(AviaTripService $avia, TrainTripService $train, BusTripService $bus)
    {
        $this->avia = $avia;
        $this->train = $train;
        $this->bus = $bus;
    }

    public function getOrCreateSearchForm(City $departure, City $arrival, Carbon $date) :RouteSearchForm
    {
        return RouteSearchForm::firstOrCreate([
            'from_id' => $departure->id,
            'to_id' => $arrival->id,
            'departure_date' => $date
        ]);
    }

    public function search(RouteSearchForm $form) :array
    {
        $route_search = $this->avia->getOrCreate($form);
        return $this->avia->getRoutes($route_search);
    }
}
