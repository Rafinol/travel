<?php


namespace App\Jobs;


use App\Models\ProxyDto\ProxyDto;
use App\Models\Trip\Trip;
use App\UseCases\Trip\Route\LoadRoutesService;
use App\UseCases\Trip\Way\LoadWaysService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProccessRoutesAndWaysSearch  implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
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
        if($proxy){
            \App::singleton(Http::class, function () use($proxy) {
                return Http::withHeaders(['proxy' => $proxy->getStringVersion()]);
            });
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LoadRoutesService $loadRoutesService, LoadWaysService $loadWaysService)
    {
        $loadWaysService->load();
        $loadRoutesService->loadRandomSearchForm();
    }
}
