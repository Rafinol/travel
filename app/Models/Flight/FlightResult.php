<?php


namespace App\Models\Flight;


class FlightResult
{
    public int $price;
    public array $flights;

    public function __construct(int $price, array $flights)
    {
        $this->price = $price;
        $this->flights = $flights;
    }
}
