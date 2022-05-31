<?php
/* @var \App\Models\Route\Route $route*/
?>
@extends('layouts.app')

@section('header', $trip->departure->name.' - '.$trip->arrival->name.', '.$trip->departure_date->format('d-m-y'))

@section('content')
    @foreach ($best_ways as $way)
        <div style="margin-bottom: 20px;">
            <h3>{{$way['price']}} p. - {{$way['flights_count']}} поездки</h3>
            <div style="border: 1px solid #000;padding:5px 10px;">
                @foreach($way['details'] as $detail)
                    <h4>{{$detail['route']->departure_name}} - {{$detail['route']->arrival_name}}
                        {{$detail['route']->price}} p.</h4>
                    @foreach($detail['part_routes'] as $key => $part_route)
                        <div>
                            <div style="width:67%; float: left;">{{ svg('vaadin-'.$transport_classes[$part_route->transport_type]['class'], '', ['style' => 'color:'.$transport_classes[$part_route->transport_type]['color'].';display:inline', 'width'=>'11']) }} {{$part_route->departure->name}} - {{$part_route->arrival->name}}</div>
                            <div style="color:#8f918f; font-size:75%; float:left;width:31%; margin-left:2%;line-height: 2.1">{{$part_route->sdate->format('d.m H:i')}} - {{$part_route->edate->format('H:i')}}</div>
                            <div style="clear:both;"></div>
                        </div>
                        <div style="clear:both;"></div>
                    @endforeach

                @endforeach
            </div>
        </div>
    @endforeach
@endsection

