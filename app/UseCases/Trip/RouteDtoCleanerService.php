<?php


namespace App\UseCases\Trip;


use App\Dto\RouteDto\ResultRouteDto;

class RouteDtoCleanerService
{
    const TAKE_COUNT = 3;

    /** @var ResultRouteDto[] $routes */
    public static function getTopRoutes(array $routes, $take_count = self::TAKE_COUNT) :array
    {
        $result = [];
        $routes = collect($routes);
        $sort_params = [
            'ride_count',
            'price',
            'duration',
            'arrival',
            'departure'
        ];
        foreach ($sort_params as $key => $sort_param){
            $clone = $sort_params;
            unset($clone[$key]);
            array_unshift($clone, $sort_param);
            $result = array_merge($result, $routes->sortBy([
                $clone
            ])->take($take_count)->all());
        }
        return $result;
    }
}
