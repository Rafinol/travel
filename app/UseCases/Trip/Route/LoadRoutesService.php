<?php
namespace App\UseCases\Trip\Route;

use App\Models\Route\RouteSearchForm;
use App\UseCases\Trip\RouteDtoCleanerService;

class LoadRoutesService
{
    private SearchRoutesService $searchService;
    private CreateRoutesService $createService;

    public function __construct(SearchRoutesService $searchService, CreateRoutesService $createService)
    {
        $this->searchService = $searchService;
        $this->createService = $createService;
    }

    public function load() :void
    {
        $forms = RouteSearchForm::waiting()->get();
        foreach ($forms as $form){
            $routes = $this->searchService->search($form);
            $routes = RouteDtoCleanerService::getTopRoutes($routes);
            $this->createService->saveRoutes($form, $routes);
        }
    }

}
