<?php


namespace App\UseCases\Trip\Route;


use App\Models\City\City;
use App\Models\Route\RouteSearch;
use App\Models\Route\RouteSearchForm;
use App\UseCases\Trip\Type\AviaTripService;
use App\UseCases\Trip\Type\BusTripService;
use App\UseCases\Trip\Type\TrainTripService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SearchRoutesService
{
    private BusTripService $bus;
    private TrainTripService $train;
    private AviaTripService $avia;

    const DELAY_BETWEEN_REQUEST = 1; //SECONDS

    public function __construct(AviaTripService $avia, TrainTripService $train, BusTripService $bus)
    {
        $this->avia = $avia;
        $this->train = $train;
        $this->bus = $bus;
    }

    public function getOrCreateSearchForm(City $departure, City $arrival, Carbon $date) :RouteSearchForm
    {
        $form = RouteSearchForm::firstOrCreate([
            'from_id' => $departure->id,
            'to_id' => $arrival->id,
            'departure_date' => $date
        ]);
        if(!$form->status) {
            $form->status = RouteSearchForm::CREATED_STATUS;
            $form->save();
        }
        return $form;
    }

    public function search(RouteSearchForm $form, int $delay = self::DELAY_BETWEEN_REQUEST) :array
    {
        if($this->bus->hasExclusiveRoute($form)){
            $route_search = new RouteSearch();
            $route_search->searchForm = $form;
            return $this->bus->getRoutes($route_search);  // Temporarily use a mock until busService is ready
        }
        $route_search = $this->avia->getOrCreate($form);
        sleep($delay);
        return $this->avia->getRoutes($route_search);
    }
}
