<?php

namespace App\Console;

use App\Jobs\ProccessRoutesSearch;
use App\Jobs\ProccessWaysInit;
use App\Models\ProxyDto\ProxyDto;
use App\Services\Travel\Yandex\YandexTravelService;
use App\UseCases\JobsKernelService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Http;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */


    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        /**@var JobsKernelService $job_service*/
        $job_service = \App::make(JobsKernelService::class);
        $job_service->loopDispatch();
    }



    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
