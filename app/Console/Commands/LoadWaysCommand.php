<?php

namespace App\Console\Commands;

use App\UseCases\Trip\Way\LoadWaysService;
use Illuminate\Console\Command;

class LoadWaysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'travels:load_ways';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update statuses in ways, trips and other tables';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(LoadWaysService $service)
    {
        $service->load();
    }
}
