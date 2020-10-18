<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login','App\Http\Controllers\UserController@login');
Route::post('generatetoken', 'App\Http\Controllers\UserController@generateToken');
Route::post('logout', 'App\Http\Controllers\UserController@logout');
Route::post('user/store', 'App\Http\Controllers\UserController@store');
Route::post('company/store', 'App\Http\Controllers\CompanyController@store');

Route::get('store_employee', 'App\Http\Controllers\EmployeeController@store');