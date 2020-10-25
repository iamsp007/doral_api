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
Route::post('company/login', 'App\Http\Controllers\CompanyController@login');
Route::post('company/updatestatus', 'App\Http\Controllers\CompanyController@updateStatus');
Route::post('company/saveprofile', 'App\Http\Controllers\CompanyController@saveProfile');
Route::post('company/resetpassword', 'App\Http\Controllers\CompanyController@resetPassword');
Route::post('company/confirmpassword', 'App\Http\Controllers\CompanyController@confirmPassword');

Route::get('store_employee', 'App\Http\Controllers\EmployeeController@store');

// Email Template
Route::get('email/templatelist', 'App\Http\Controllers\EmailTemplateController@index');
// Patient store
Route::post('patient-referral/store', 'App\Http\Controllers\PatientReferralController@store');
