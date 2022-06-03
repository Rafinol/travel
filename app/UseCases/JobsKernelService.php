<?php


namespace App\UseCases;


use App\Jobs\ProccessRoutesAndWaysSearch;
use App\Jobs\ProccessRoutesSearch;
use App\Jobs\ProccessWaysInit;
use App\Dto\ProxyDto\ProxyDto;
use Laravel\Horizon\Contracts\JobRepository;

class JobsKernelService
{
    private JobRepository $jobs;
    const MAX_QUEUE_COUNT = 15;

    public function __construct(JobRepository $jobs)
    {
        $this->jobs = $jobs;
    }

    public function loopDispatch()
    {
        /***@var ProxyDto $proxy**/
        $proxies = ProxyDto::getAll();
        array_unshift($proxies, null);
        $count_pending = $this->jobs->countPending();
        for ($i = $count_pending; $i < self::MAX_QUEUE_COUNT; $i=$i+count($proxies)){
            foreach ($proxies as $key => $proxy){
                ProccessRoutesAndWaysSearch::dispatch($proxy)->delay($i+$key);
            }
         }
    }


}
