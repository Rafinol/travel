<?php

namespace App\Console\Commands;

use App\Http\Request\Trip\TripRequest;
use App\Models\City\City;
use App\Services\Travel\TravelService;
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
    public function handle(TripService $service)
    {
        $request = new TripRequest();
        $request['from_id'] = City::where(['name' => $this->argument('from')])->first();
        $request['to_id'] = City::where(['name' => $this->argument('to')])->first();
        $request['date'] = $this->argument('date');

        $request->validate();

        $ways = $service->getWays($request);
        $this->info(json_encode($ways));
    }
}
