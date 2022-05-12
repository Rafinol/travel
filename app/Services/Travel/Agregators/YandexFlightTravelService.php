<?php


namespace App\Services\Travel\Agregators;


use App\Exceptions\RoutesAlreadyDoneException;
use App\Exceptions\RoutesNotReadyYetException;
use App\Models\City\City;
use App\Models\Flight\Flight;
use App\Models\Flight\FlightResult;
use App\Models\Point\Station;
use App\Models\Trip\Trip;
use App\Models\Way\PartWay;
use App\Models\Way\Way;
use App\Models\Way\WaySearch;
use App\Services\Travel\FlightTravelService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class YandexFlightTravelService implements FlightTravelService
{
    const INIT_URL = 'https://travel.yandex.ru/api/avia/search/init';
    const GET_CITY_URL = 'https://travel.yandex.ru/api/avia/searchSuggest';
    const GET_RESULT_URL = 'https://travel.yandex.ru/api/avia/search/results';

    const SERVICE_NAME = 'yandex_travel';

    public function getServiceName() :string
    {
        return self::SERVICE_NAME;
    }

    public function search(PartWay $part_way) :void
    {
        $way_search = WaySearch::new($part_way->id, $this->getServiceName());
        $search_id = $this->createSearch($part_way->departure, $part_way->arrival, $part_way->departure_date);
        $way_search->changeStatusToWaiting();
        $way_search->update(['search_id'=> $search_id]);
    }

    private function createSearch(City $from, City $to, $date) :string
    {
        if(!$from->yandex_id || !$to->yandex_id){
            $from = $from->updateForeignId($this->getCityId($from->name));
            $to = $to->updateForeignId($this->getCityId($to->name));
        }
        $body = [
            'fromId' => $from->yandex_id,
            'toId' => $to->yandex_id,
            'when' => $date,
            'adult_seats' => 1,
            'children_seats' => 0,
            'infant_seats' => 0,
            'klass' => 'economy',
            'oneway' => 1,
        ];
        $result = Http::get(self::INIT_URL, $body);
        return $result['id'];
    }

    private function getCityId(string $city_name) :int
    {
        $result = Http::get(self::GET_CITY_URL, [
            'field' => 'from',
            'query' => $city_name,
            'otherPoint' => 'c11475',
            'clientCity' => 43,
            'showCountries' => 0,
            'showAnywhere' => 0,
        ]);
        return $result['items'][0]['pointKey'];
    }

    public function getRoutes(PartWay $part_way): array
    {
        $way_search = WaySearch::where(['type' => $this->getServiceName(), 'way_id' => $part_way->id,])->where('created_at', '>', now()->subDay())->first();
        if($way_search->isWaiting()){
            throw new RoutesNotReadyYetException();
        }
        if($way_search->isDone()){
            throw new RoutesAlreadyDoneException();
        }
        $result = $this->getResults($way_search->search_id);
        $way_search->changeStatusToDone();
        return $result;
    }

    private function getResults(string $search_id) :array
    {
        $params = [
            'qid' => $search_id,
            'cont' => 0,
            'allowPortional' => 1,
        ];
        $response = Http::get(self::GET_RESULT_URL, $params);
        if(!$response){
            throw new \DomainException('travels not found:(');
        }
        $ya_flights = new YandexFlight($response->object());
        return $ya_flights->getFlights();
    }
}
