<?php

namespace App\Console\Commands;

use App\Models\City\City;
use App\UseCases\Trip\Route\SearchRoutesService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SearchRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'travel:routes {from} {to} {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get routes from aggregators. Date format: Y-m-d';

    /**
     * Execute the console command.
     *
     */
    public function handle(SearchRoutesService $service)
    {
        $departure = City::where(['name' => $this->argument('from')])->firstOrFail();
        $arrival = City::where(['name' => $this->argument('to')])->firstOrFail();
        $form = $service->getOrCreateSearchForm(
            $departure,
            $arrival,
            Carbon::createFromFormat('Y-m-d',$this->argument('date'))
        );
        $result = $service->search($form, 20);
        $this->info(json_encode($result));
    }
}
