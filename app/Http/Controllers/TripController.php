<?php

namespace App\Http\Controllers;

use App\Http\Request\Trip\TripRequest;
use App\Jobs\ProccessRoutesSearch;
use App\Jobs\ProccessWaysInit;
use App\Dto\ProxyDto\ProxyDto;
use App\Models\Trip\Trip;
use App\UseCases\JobsKernelService;
use App\UseCases\Trip\Departure\DepartureService;
use App\UseCases\Trip\TripService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use MenaraSolutions\Geographer\City;
use MenaraSolutions\Geographer\Divisible;
use MenaraSolutions\Geographer\Earth;

class TripController extends Controller
{
    private TripService $service;
    private JobsKernelService $kernelService;

    public function __construct(TripService $service, JobsKernelService $kernelService)
    {
        $this->service = $service;
        $this->kernelService = $kernelService;
    }

    public function index()
    {
        return view('trip.index');
    }

    public function create(TripRequest $request)
    {
        $trip = $this->service->getPreparedTrip(
            $request->input('from'),
            $request->input('to'),
            Carbon::createFromFormat('Y-m-d',$request->input('date'))
        );
        $this->kernelService->loopDispatch();
        return redirect()->route('trip.show', ['id' => $trip->id]);
    }

    public function show($id)
    {
        $trip = Trip::findOrFail($id);
        if($trip->isSearching() || $trip->isCreated()){
            return view('trip.searching-show', compact('trip'));
        }
        $best_ways = $this->service->getCheapestWays($trip);
        $transport_classes = [
            'air' => ['class' => 'airplane', 'color' => 'red'],
            'bus' => ['class' => 'buss', 'color' => 'green'],
            'train' => ['class' => 'train', 'color' => 'blue'],
        ];
        return view('trip.show', compact('trip', 'best_ways', 'transport_classes'));
    }
}
