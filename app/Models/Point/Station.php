<?php


namespace App\Models\Point;


class Station
{
    public $code;
    public $name;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }
}
