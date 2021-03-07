<?php

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
    return view('welcome');
});

//Route::get('/map', function (\Illuminate\Support\Facades\Request $request) {
//    $lat = 21.455414;
//    $long = 69.145474;
//    $location = ["lat"=>$lat, "long"=>$long];
//    event(new \App\Events\ActionEvent($location));
//    return response()->json(['status'=>'success', 'data'=>$location]);
//});

