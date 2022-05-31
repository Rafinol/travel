<?php
/* @var \App\Models\Route\Route $route*/
?>
@extends('layouts.app')

@section('header', $trip->departure->name.' - '.$trip->arrival->name.', '.$trip->departure_date->format('d-m-y'))

@section('content')
    Trip is searching. Please try to refresh this page later.
@endsection
