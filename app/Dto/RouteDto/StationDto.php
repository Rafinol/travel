<?php


namespace App\Dto\RouteDto;


class StationDto
{
    public string $code;
    public string $name;
    public string $type;

    public function __construct(string $code, string $name, string $type)
    {
        $this->code = $code;
        $this->name = $name;
        $this->type = $type;
    }
}
