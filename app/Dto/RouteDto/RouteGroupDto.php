<?php


namespace App\Dto\RouteDto;


use Carbon\Carbon;

class RouteGroupDto
{
    public Carbon $departure_date;
    public Carbon $arrival_date;
    public int $price;
    public array $routes;
}
