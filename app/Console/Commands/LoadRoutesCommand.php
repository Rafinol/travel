<?php

namespace App\Console\Commands;

use App\UseCases\Trip\Route\LoadRoutesService;
use App\UseCases\Trip\Route\SearchRoutesService;
use Illuminate\Console\Command;

class LoadRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'travels:load_routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update waiting routes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(LoadRoutesService $service)
    {
        $service->loadRandomSearchForm();
    }
}
