<?php
namespace App\UseCases\Trip\Route;

use App\Models\Route\RouteSearchForm;
use App\UseCases\Trip\RouteDtoCleanerService;

class LoadRoutesService
{
    private SearchRoutesService $searchService;
    private CreateRoutesService $createService;

    const WAITING_TIME = 10;

    public function __construct(SearchRoutesService $searchService, CreateRoutesService $createService)
    {
        $this->searchService = $searchService;
        $this->createService = $createService;
    }

    public function load() :void
    {
        $forms = RouteSearchForm::new()->get();
        foreach ($forms as $form){
            $this->loadForm($form);
        }
    }

    public function loadRandomSearchForm() :void
    {
        $form = RouteSearchForm::new()->first();
        if(!$form){
            return;
        }
        try {
            $this->loadForm($form);
        }
        catch (\Exception $e){
            \Log::error($e->getMessage());
            $form->changeStatusToFail();
        }

    }

    public function loadForm(RouteSearchForm $form) :void
    {
        $form->changeStatusToSearching();
        $routes = $this->searchService->search($form, self::WAITING_TIME);
        $routes = RouteDtoCleanerService::getTopRoutes($routes);
        $this->createService->saveRoutes($form, $routes);
        $form->changeStatusToComplete();
    }

}
