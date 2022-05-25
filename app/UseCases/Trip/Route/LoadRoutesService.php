<?php
namespace App\UseCases\Trip\Route;

use App\Models\Route\RouteSearchForm;

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
            $this->createService->saveRoutes($form, $routes);
        }
    }

}
