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
});
Route::prefix('trip')->group(function(){

    Route::get('index', [TripController::class, 'index']);
    Route::get('show/{id}', [TripController::class, 'show'])->name('trip.show');
    Route::post('create', [TripController::class, 'create'])->name('trip.create');
});
