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
    Route::post('forgot', 'App\Http\Controllers\Auth\AuthController@forgotPassword')->name('forgot');
    Route::post('reset', 'App\Http\Controllers\Auth\AuthController@reset')->name('reset');
    Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\AuthController@reset')->name('password.reset');
    Route::post('password/reset', 'App\Http\Controllers\Auth\AuthController@resetPassword')->name('password.update');

    Route::post('register', 'App\Http\Controllers\UserController@store');
    Route::put('patient/register/{step}', 'App\Http\Controllers\PatientController@storeInfomation')->name('patient.updateInfomation');
    Route::post('company/login', 'App\Http\Controllers\CompanyController@login');
    Route::post('company/store', 'App\Http\Controllers\CompanyController@store');
    // Patient Referral Urls
    Route::post('patient-referral/store', 'App\Http\Controllers\PatientReferralController@store');
    Route::get('patient-referral/{id}', 'App\Http\Controllers\PatientReferralController@index')->name('referral_patients');

    // Employee
    Route::get('employee', 'App\Http\Controllers\EmployeeController@index')->name('employee.index');

    Route::get('company/{id}', 'App\Http\Controllers\CompanyController@index');
    Route::get('company/show/{company}', 'App\Http\Controllers\CompanyController@show');
    Route::post('company/updatestatus', 'App\Http\Controllers\CompanyController@updateStatus');
    Route::group([
        'middleware' => ['auth:api'],
    ], function () {
        Route::get('logout', 'App\Http\Controllers\Auth\AuthController@logout');
        //Services
        Route::get('service', 'App\Http\Controllers\ServicesController@index');
        Route::post('service/store', 'App\Http\Controllers\ServicesController@store');
        Route::put('service/{service}', 'App\Http\Controllers\ServicesController@update');
        //Users URLs
        Route::get('user', 'App\Http\Controllers\Auth\UserController@user');
        //Company URLs
        //Route::post('company/updatestatus', 'App\Http\Controllers\CompanyController@updateStatus');
        Route::post('company/saveprofile', 'App\Http\Controllers\CompanyController@saveProfile');
        Route::post('company/resetpassword', 'App\Http\Controllers\CompanyController@resetPassword');
        Route::post('company/confirmpassword', 'App\Http\Controllers\CompanyController@confirmPassword');
        //Route::get('company', 'App\Http\Controllers\CompanyController@index');
        //Route::get('company/show/{company}', 'App\Http\Controllers\CompanyController@show');
        Route::get('company/{company}/edit', 'App\Http\Controllers\CompanyController@edit');
        // Employees Urls
        Route::get('store_employee', 'App\Http\Controllers\EmployeeController@store');
        //Route::get('employee', 'App\Http\Controllers\EmployeeController@index')->name('employee.index');
        Route::get('employee/search', 'App\Http\Controllers\EmployeeController@search')->name('employee.search');
        // Email Template Urls
        Route::get('email/templatelist', 'App\Http\Controllers\EmailTemplateController@index');
    });
});

Route::group([
    'middleware' => ['auth:api'],
], function () {
// Patient Road L API
    Route::post('patient-request', 'App\Http\Controllers\PatientRequestController@store');
    Route::post('patient-roadl-selected-disease', 'App\Http\Controllers\PatientController@roadlSelectedDisease');
    Route::post('roadl-information', 'App\Http\Controllers\RoadlController@create');
    Route::post('roadl-information-show', 'App\Http\Controllers\RoadlController@show');
    Route::post('ccm-reading', 'App\Http\Controllers\PatientRequestController@ccmReading');
    Route::get('dieses-master', 'App\Http\Controllers\DiesesMasterController@index');
    Route::get('symptoms-master/{dieser_id}', 'App\Http\Controllers\SymptomsMasterController@index');
});

// clincian API
Route::group([
    'middleware' => ['auth:api'],
], function () {
// Patient Road L API
    Route::post('clinician-request-accept', 'App\Http\Controllers\PatientRequestController@clinicianRequestAccept');
    Route::post('clinician-patient-request-list', 'App\Http\Controllers\PatientRequestController@clinicianPatientRequestList');
});
