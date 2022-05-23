<?php

namespace App\Console\Commands;

use App\Http\Request\Trip\TripRequest;
use App\Models\City\City;
use App\Services\Travel\FlightTravelService;
use App\UseCases\Trip\Departure\DepartureService;
use App\UseCases\Trip\TripService;
use Carbon\Carbon;
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
        $request['from'] = $this->argument('from');
        $request['to'] = $this->argument('to');
        $request['date'] = $this->argument('date');
        $request->validate($request->rules());
        $trip = $service->getTrip($this->argument('from'), $this->argument('to'), Carbon::createFromFormat('Y-m-d',$this->argument('date')));
        $service->search($trip);
        $this->info(json_encode($trip->ways));
    }
}
