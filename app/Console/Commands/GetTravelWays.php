<?php

namespace App\Console\Commands;

use App\Http\Request\Trip\TripRequest;
use App\Models\City\City;
use App\Services\Travel\FlightTravelService;
use App\UseCases\Trip\Departure\DepartureService;
use App\UseCases\Trip\TripService;
use Illuminate\Console\Command;

class GetTravelWays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'travel:get {from} {to} {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get travel ways from any aggregators. Date format: Y-m-d';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(DepartureService $service)
    {
        $request = new TripRequest();
        $request['from_id'] = City::where(['name' => $this->argument('from')])->firstOrFail()->id;
        $request['to_id'] = City::where(['name' => $this->argument('to')])->firstOrFail()->id;
        $request['date'] = $this->argument('date');
        $request->validate($request->rules());
        $trip = $service->getTrip($request);
        $service->search($trip);
        $this->info(json_encode($trip->ways));
    }
}
