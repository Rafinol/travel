<?php

use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('trip.index');
    return view('welcome');
});

Route::get('/trip/index', [TripController::class, 'index']);
Route::get('/trip/show/{id}', [TripController::class, 'show'])->name('trip.show');
Route::post('/trip/create', [TripController::class, 'create'])->name('trip.create');
Route::get('/trip/dispatch/{id}', [TripController::class, 'dispatch'])->name('trip.dispatch');
