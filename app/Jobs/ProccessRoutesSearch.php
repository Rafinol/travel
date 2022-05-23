<?php

namespace App\Jobs;

use App\Models\Trip\Trip;
use App\UseCases\Trip\Departure\DepartureService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProccessRoutesSearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Trip $trip;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 20*60;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DepartureService $service)
    {
        $service->search($this->trip);
    }
}
