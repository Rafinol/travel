<?php


namespace App\Services\Travel;


use App\Exceptions\RoutesNotReadyYetException;
use App\Models\City\City;
use App\Models\Flight\Flight;
use App\Models\Point\Station;
use App\Models\Trip\Trip;
use App\Models\Way\Way;
use App\Models\Way\WaySearch;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class YandexFlightTravelService implements TravelService
{
    const INIT_URL = 'https://travel.yandex.ru/api/avia/search/init';
    const GET_CITY_URL = 'https://travel.yandex.ru/api/avia/searchSuggest';
    const GET_RESULT_URL = 'https://travel.yandex.ru/api/avia/search/results';

    const SERVICE_NAME = 'yandex_travel';

    public function getServiceName() :string
    {
        return self::SERVICE_NAME;
    }

    private function createSearch(City $from, City $to, $date)
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

    public function getRoutes(Way $way): array
    {
        $way_search = WaySearch::where(['type' => $this->getServiceName(), 'way_id' => $way->id,])->where(['created_at', '>', now()->subDay()])->first();
        if(!$way_search){
            $way_search = WaySearch::new($way->id, $this->getServiceName());
        }
        if($way_search->isNew()){
            $trip = $way->trip;
            $search_id = $this->createSearch($trip->departure, $trip->arrival, $trip->departure_date);
            $way->changeStatusToWaiting();
        }
        if($way_search->isWaiting()){
            throw new RoutesNotReadyYetException();
        }
        if($way_search->isDone()){
            $search_id = $way_search->search_id;
        }

        $result = $this->getResults($search_id);
        dd($result->getBody()->getContents());
        //$result = $this->client->get();
    }

    private function getCityId(string $city_name){
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

    public function getResults(string $search_id) :array
    {
        $params = [
            'qid' => $search_id,
            'cont' => 0,
            'allowPortional' => 1,
        ];
        $result = Http::get(self::GET_RESULT_URL, $params);
        if(!$result){
            throw new \DomainException('travels not found:(');
        }
        $reference_flights = collect($result['reference']['flights'])->keyBy('key')->values()->all();
        $stations = collect($result['reference']['stations'])->keyBy('id')->values()->all();
        $collection = collect($result['variants']['fares']);
        $sorted_collection = $collection->sortBy(function ($value, $key){
            return min(array_column($value['prices'], 'tariff["value"]'));
        })
        ->slice(0, 10)
        ->map(function ($value, $key) use ($reference_flights, $stations){
            $flights = array_map(function($value) use ($reference_flights, $stations){
                $flight = new Flight();
                $flight->departure_date = $reference_flights[$value]['departure']['local'];
                $flight->arrival_date = $reference_flights[$value]['arrival']['local'];
                $flight->flight_number = $reference_flights[$value]['number'];
                $station_from = $stations[$reference_flights[$value]['from']];
                $flight->departure_point = new Station($station_from['code'], $station_from['title']);
                $station_to = $stations[$reference_flights[$value]['to']];
                $flight->arrival_point = new Station($station_to['code'], $station_to['title']);
                }, $value['route'][0]);

            return [
                'price' => min(array_column($value['prices'], 'tariff["value"]')),
                'flights' => $flights
            ];
        });
        return $sorted_collection->values()->all();
    }

    private function parseRouteCode(string $hash) :Flight
    {
        $date = substr($hash, 0, 4); // The first 4 characters are the search date
        preg_match("/".$date."([0-9]{2})([0-9]{4})(^[a-zA-Z0-9]{1, }$)".$date."([0-9]{2})([0-9]{4})/", $hash, $matches);
        $flight = new Flight();
        $flight->arrival_date = Carbon::createFromFormat('d-m', $matches[1]."-".$matches[2]);
        $flight->departure_date = Carbon::createFromFormat('d-m', $matches[4]."-".$matches[5]);
        $flight->flight_number = $matches[3];
        return $flight;
    }
}
