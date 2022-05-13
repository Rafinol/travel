<?php


namespace App\Models\RouteDto;


class ResultRouteDto
{
    public int $price;
    public array $routes;

    public function __construct(int $price, array $routes)
    {
        $this->price = $price;
        $this->routes = $routes;
    }
}
