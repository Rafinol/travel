<?php


namespace App\Services\Travel;


use App\Models\Route\RouteSearch;
use App\Models\Route\RouteSearchForm;

interface CommonTravelService
{
    public function getRoutes(RouteSearch $route_search) :array;

    public function search(RouteSearchForm $route_search_form) :string;

    public function getServiceName() :string;
}
