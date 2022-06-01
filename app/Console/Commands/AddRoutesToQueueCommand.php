<?php

namespace App\Console\Commands;

use App\UseCases\JobsKernelService;
use Illuminate\Console\Command;

class AddRoutesToQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'travels:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AddRoutesToQueueCommand';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**@var JobsKernelService $job_service*/
        $job_service = \App::make(JobsKernelService::class);
        $job_service->loopDispatch();
    }
}
