<?php
namespace App\Services\Travel\Agregators;

use App\Models\City\City;
use Illuminate\Support\Facades\Storage;

class MockYandexFlightTravelService extends YandexFlightTravelService
{
    public function createSearch(City $from, City $to, $date) :string
    {
        return '220517-195000-567.avia-travel.plane.c43_c10445_2022-05-22_None_economy_1_0_0_ru.ru';
    }

    public function getResults(string $search_id) : array
    {
        $json = file_get_contents('YandexMockResult.json', true);
        return json_decode($json, true);
    }
}
