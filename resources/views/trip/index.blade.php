@extends('layouts.app')

@section('header', 'Find your trip!')

@section('content')
    <form action="{{route('trip.create')}}" method="post">
        @csrf
        <div style="line-height: 2.5">
            <div class="">
                <label for="from" style="float: left;width: 20%;">From</label>
                <input class="text-lg" style="float: left;width: 80%;line-height: 2rem;" type="text" id="from" name="from" required>
                <div style="clear: both"></div>
            </div>
            <div class="">
                <label for="to" style="float: left;width: 20%;">To</label>
                <input class="text-lg" style="float: left;width: 80%;line-height: 2rem;" type="text" id="to" name="to" required>
                <div style="clear: both"></div>
            </div>
            <div class="">
                <label for="date" style="float: left;width: 20%;">Date</label>
                <input class="text-lg" style="float: left;width: 80%;line-height: 2rem;" type="date" id="date" name="date" required>
                <div style="clear: both"></div>
            </div>
            <div class="">
                <button type="submit" class="btn btn-primary" style="margin-top:0.7rem; background-color: #2d3748;color: #fff;font-size: 1.2rem;line-height: 1.8;width: 100%;">Search</button>
            </div>
        </div>
    </form>
@endsection
