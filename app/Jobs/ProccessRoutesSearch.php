<?php

namespace App\Jobs;

use App\Models\ProxyDto\ProxyDto;
use App\Models\Trip\Trip;
use App\UseCases\Trip\Departure\DepartureService;
use App\UseCases\Trip\Route\LoadRoutesService;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

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
    public function __construct(ProxyDto $proxy = null)
    {
        \App::singleton(Http::class, function () use($proxy) {
            $headers = $proxy ? ['proxy' => $proxy->getStringVersion()] : [];
            return Http::withHeaders($headers);
        });
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LoadRoutesService $service)
    {
        $service->loadRandomSearchForm();
    }
}
