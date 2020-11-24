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

//Route::get('store_employee', 'App\Http\Controllers\EmployeeController@store');
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'App\Http\Controllers\Auth\AuthController@login')->name('login');
    Route::post('company/login', 'App\Http\Controllers\CompanyController@login');
    Route::post('company/store', 'App\Http\Controllers\CompanyController@store');
    Route::post('register', 'App\Http\Controllers\Auth\AuthController@register');
//    Route::post('register', 'App\Http\Controllers\Auth\AuthController@register');
    Route::group([
        'middleware' => ['auth:api','role:administrator|co-ordinator|Supervisor|Clinician'],
    ], function () {

        Route::get('logout', 'App\Http\Controllers\Auth\AuthController@logout');
        //Users URLs
        Route::get('user', 'App\Http\Controllers\Auth\AuthController@user');

        //Company URLs

        Route::post('company/updatestatus', 'App\Http\Controllers\CompanyController@updateStatus');
        Route::post('company/saveprofile', 'App\Http\Controllers\CompanyController@saveProfile');
        Route::post('company/resetpassword', 'App\Http\Controllers\CompanyController@resetPassword');
        Route::post('company/confirmpassword', 'App\Http\Controllers\CompanyController@confirmPassword');
        Route::get('company', 'App\Http\Controllers\CompanyController@index');
        Route::get('company/show/{company}', 'App\Http\Controllers\CompanyController@show');
        Route::get('company/{company}/edit', 'App\Http\Controllers\CompanyController@edit');
        // Employees Urls
        Route::get('store_employee', 'App\Http\Controllers\EmployeeController@store');
        Route::get('employee', 'App\Http\Controllers\EmployeeController@index')->name('employee.index');
        Route::get('employee/search', 'App\Http\Controllers\EmployeeController@search')->name('employee.search');
        // Email Template Urls
        Route::get('email/templatelist', 'App\Http\Controllers\EmailTemplateController@index');
        // Patient Referral Urls
        Route::post('patient-referral/store', 'App\Http\Controllers\PatientReferralController@store');

    });
});

Route::group([
    'middleware' => ['auth:api'],
], function () {
// Patient Road L API
    Route::post('patient-request', 'App\Http\Controllers\PatientRequestController@store');
    Route::post('ccm-reading', 'App\Http\Controllers\PatientRequestController@ccmReading');
});

// clincian API
Route::group([
    'middleware' => ['auth:api','role:Clinician'],
], function () {
// Patient Road L API
    Route::post('patient-request-accept', 'App\Http\Controllers\PatientRequestController@patientRequestAccept');
});
