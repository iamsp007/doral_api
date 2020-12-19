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

//    Route::post('register', 'App\Http\Controllers\UserController@store');
    Route::post('register', 'App\Http\Controllers\Auth\AuthController@register');
    Route::put('patient/register/{step}', 'App\Http\Controllers\PatientController@storeInfomation')->name('patient.updateInfomation');
    Route::post('company/login', 'App\Http\Controllers\CompanyController@login');
    Route::post('company/store', 'App\Http\Controllers\CompanyController@store');
    // Patient Referral Urls
    Route::post('patient-referral/store', 'App\Http\Controllers\PatientReferralController@store');
    Route::post('patient-referral/storecert', 'App\Http\Controllers\PatientReferralController@storeCertDate');
    Route::get('patient-referral/{id}', 'App\Http\Controllers\PatientReferralController@index')->name('referral_patients');

    // Employee
    Route::get('designation', 'App\Http\Controllers\DesignationController@index')->name('designation.index');
    Route::get('employee', 'App\Http\Controllers\EmployeeController@index')->name('employee.index');
    Route::get('employee/show/{employee}', 'App\Http\Controllers\EmployeeController@show')->name('employee.show');
    Route::get('employee/remove/{employee}', 'App\Http\Controllers\EmployeeController@destroy')->name('employee.remove');
    Route::post('employee/store', 'App\Http\Controllers\EmployeeController@store')->name('employee.store');
    Route::post('employee/work', 'App\Http\Controllers\EmployeeController@work')->name('employee.work');

    Route::get('company/{id}', 'App\Http\Controllers\CompanyController@index');
    Route::get('company/show/{company}', 'App\Http\Controllers\CompanyController@show');
    Route::post('company/updatestatus', 'App\Http\Controllers\CompanyController@updateStatus');

    Route::post('caregiver/actionstore', 'App\Http\Controllers\CaregiverController@actionStore')->name('caregiver.actionstore');

    Route::group([
        'middleware' => ['auth:api'],
    ], function () {
        Route::get('logout', 'App\Http\Controllers\Auth\AuthController@logout');
        //Patient
        Route::get('patient/search/{keyword}', 'App\Http\Controllers\PatientController@searchByEmailNamePhone');
        //Services
        Route::get('service', 'App\Http\Controllers\ServicesController@index');
        Route::post('service/store', 'App\Http\Controllers\ServicesController@store');
        Route::put('service/{service}', 'App\Http\Controllers\ServicesController@update');
        //Request
        Route::get('request', 'App\Http\Controllers\RequestController@index');
        Route::post('request/store', 'App\Http\Controllers\RequestController@store');
        Route::put('request/{request}', 'App\Http\Controllers\RequestController@update');
        //Appointment
        Route::get('appointment', 'App\Http\Controllers\AppointmentController@index');
        Route::post('appointment/store', 'App\Http\Controllers\AppointmentController@store');
        Route::put('appointment/{appointment}', 'App\Http\Controllers\AppointmentController@update');
        Route::post('appointment/upcoming-patient-appointment', 'App\Http\Controllers\AppointmentController@upcomingPatientAppointment' );
        Route::post('appointment/cancel-patient-appointment', 'App\Http\Controllers\AppointmentController@cancelPatientAppointment' );
        Route::post('appointment/past-patient-appointment', 'App\Http\Controllers\AppointmentController@pastPatientAppointment' );
        //Users URLs
        Route::get('user', 'App\Http\Controllers\Auth\AuthController@user');
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
        Route::get('employee', 'App\Http\Controllers\EmployeeController@index')->name('employee.index');
        Route::get('employee/search/getAppoinment', 'App\Http\Controllers\EmployeeController@getAppoinment');
        // Email Template Urls
        Route::get('email/templatelist', 'App\Http\Controllers\EmailTemplateController@index');
    });
});

Route::group([
    'middleware' => ['auth:api','role:patient'],
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
    'middleware' => ['auth:api','role:clinician|co-ordinator'],
], function () {
// Patient Road L API
    Route::post('clinician-request-accept', 'App\Http\Controllers\PatientRequestController@clinicianRequestAccept');
    Route::post('clinician-patient-request-list', 'App\Http\Controllers\PatientRequestController@clinicianPatientRequestList');
    Route::get('get-near-by-clinician-list/{patient_request_id}', 'App\Http\Controllers\RoadlController@getNearByClinicianList');
    Route::get('get-roadl-proccess/{patient_request_id}', 'App\Http\Controllers\RoadlController@getRoadLProccess');
    Route::post('create-virtual-room', 'App\Http\Controllers\SessionsController@createRoom');
    Route::get('get-patient-list', 'App\Http\Controllers\PatientController@getPatientList');
    Route::get('get-new-patient-list', 'App\Http\Controllers\PatientController@getNewPatientList');
    Route::post('change-patient-status', 'App\Http\Controllers\PatientController@changePatientStatus');
});

// Referral
Route::group([
    'middleware' => ['auth:api','role:referral'],
], function () {

});

// Co Ordinator
Route::group([
    'middleware' => ['auth:api','role:co-ordinator'],
], function () {
//    Route::get('/get-patient-list','');
});
