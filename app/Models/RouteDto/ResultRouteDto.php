<?php


namespace App\Models\RouteDto;


use Carbon\Carbon;

class ResultRouteDto
{
    public int $price;
    public array $routes;
    public int $duration;
    public Carbon $departure;
    public Carbon $arrival;
    public int $ride_count;

    /**
     * @param int $price
     * @param Carbon $departure
     * @param Carbon $arrival
     * @var RouteDto[] $routes
     */
    public function __construct(int $price, Carbon $departure, Carbon $arrival,  array $routes)
    {
        $this->price = $price;
        $this->routes = $routes;
        $this->duration = $arrival->diffInSeconds($departure);
        $this->departure = $departure;
        $this->arrival = $arrival;
        $this->ride_count = count($routes);
    }
}
