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
    Route::post('patient-login', 'App\Http\Controllers\Auth\AuthController@patientLogin');
    Route::post('update-phone', 'App\Http\Controllers\Auth\AuthController@updatePhone');
    Route::post('verify-phone', 'App\Http\Controllers\Auth\AuthController@verifyPhone');
    Route::post('login', 'App\Http\Controllers\Auth\AuthController@login')->name('login');
    Route::post('forgot', 'App\Http\Controllers\Auth\AuthController@forgotPassword')->name('forgot');
    Route::post('reset', 'App\Http\Controllers\Auth\AuthController@reset')->name('reset');
    Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\AuthController@reset')->name('password.reset');
    Route::post('password/reset', 'App\Http\Controllers\Auth\AuthController@resetPassword')->name('password.update');
    Route::get('countries', 'App\Http\Controllers\Auth\AuthController@countries')->name('countries');
    Route::get('states', 'App\Http\Controllers\Auth\AuthController@states')->name('states');
    Route::get('cities', 'App\Http\Controllers\Auth\AuthController@cities')->name('cities');
    Route::post('filter-cities', 'App\Http\Controllers\Auth\AuthController@filterCities')->name('filter-cities');
    Route::post('nexmo-send', 'App\Http\Controllers\NexmoController@index')->name('index')->middleware('auth:api');
    Route::post('nexmo-verify', 'App\Http\Controllers\NexmoController@verify')->name('verify')->middleware('auth:api');

//    Route::post('register', 'App\Http\Controllers\UserController@store');
    Route::post('register', 'App\Http\Controllers\Auth\AuthController@register');
    Route::put('patient/register/{step}', 'App\Http\Controllers\PatientController@storeInfomation')->name('patient.updateInfomation');
    Route::post('company/login', 'App\Http\Controllers\CompanyController@login');
    Route::post('company/store', 'App\Http\Controllers\CompanyController@store');
    // Patient Referral Urls
    Route::post('patient-referral/store', 'App\Http\Controllers\PatientReferralController@store');
    Route::post('patient-referral/storecert', 'App\Http\Controllers\PatientReferralController@storeCertDate');
    /*Route::get('patient-referral/{id}', 'App\Http\Controllers\PatientReferralController@index')->name('referral_patients');*/
    Route::post('patient-occupational/storeoccupational', 'App\Http\Controllers\PatientOccupationalController@storeOccupational');
    Route::get('patient-occupational/{id}', 'App\Http\Controllers\PatientOccupationalController@index')->name('occupational_patients');
    Route::get('mdforms', 'App\Http\Controllers\MDFormsController@index')->name('mdforms.index');

    // Employee
    Route::get('designation', 'App\Http\Controllers\DesignationController@index')->name('designation.index');
    Route::get('employee', 'App\Http\Controllers\EmployeeController@index')->name('employee.index');
    Route::get('employee/show/{employee}', 'App\Http\Controllers\EmployeeController@show')->name('employee.show');
    Route::get('employee/remove/{employee}', 'App\Http\Controllers\EmployeeController@destroy')->name('employee.remove');
    Route::post('employee/store', 'App\Http\Controllers\EmployeeController@store')->name('employee.store');
    Route::post('employee/work', 'App\Http\Controllers\EmployeeController@work')->name('employee.work');

    Route::get('company/{id}', 'App\Http\Controllers\CompanyController@index');
    Route::get('company/show/{company}', 'App\Http\Controllers\CompanyController@show');
    Route::post('company_referral/update', 'App\Http\Controllers\CompanyController@update');
    Route::post('company/updatestatus', 'App\Http\Controllers\CompanyController@updateStatus');

    Route::post('caregiver/actionstore', 'App\Http\Controllers\CaregiverController@actionStore')->name('caregiver.actionstore');

    // supervisour api
    Route::get('getNewPatientListAll', 'App\Http\Controllers\PatientController@getPatientList');
    Route::get('getNewPatientList', 'App\Http\Controllers\PatientController@getNewPatientList');

    Route::group([
        'middleware' => ['auth:api'],
    ], function () {
        Route::get('logout', 'App\Http\Controllers\Auth\AuthController@logout');
        Route::get('ccm-readings', 'App\Http\Controllers\UserController@ccmReadings');
        Route::post('save-token', 'App\Http\Controllers\Auth\AuthController@saveToken');
        //Patient
        Route::get('patient/search/{keyword}', 'App\Http\Controllers\PatientController@searchByEmailNamePhone');
        //Services
        Route::get('service', 'App\Http\Controllers\ServicesController@index');
        Route::post('service/store', 'App\Http\Controllers\ServicesController@store');
        Route::put('service/{service}', 'App\Http\Controllers\ServicesController@update');
        Route::get('service-master', 'App\Http\Controllers\ServicesController@serviceMaster');
        //Request
        Route::get('request', 'App\Http\Controllers\RequestController@index');
        Route::post('request/store', 'App\Http\Controllers\RequestController@store');
        Route::put('request/{request}', 'App\Http\Controllers\RequestController@update');
        //Appointment
        Route::get('appointment', 'App\Http\Controllers\AppointmentController@index');

        Route::put('appointment/{appointment}', 'App\Http\Controllers\AppointmentController@update');
        Route::post('appointment/upcoming-patient-appointment', 'App\Http\Controllers\AppointmentController@upcomingPatientAppointment' );
        Route::post('appointment/cancel-patient-appointment', 'App\Http\Controllers\AppointmentController@cancelPatientAppointment' );
        Route::post('appointment/past-patient-appointment', 'App\Http\Controllers\AppointmentController@pastPatientAppointment' );
        Route::get('appointment/cancel-appointment-reasons', 'App\Http\Controllers\AppointmentController@getCancelAppointmentReasons');
        Route::post('appointment/bydate', 'App\Http\Controllers\AppointmentController@getAppointmentsByDate');
        Route::get('appointment/{id}', 'App\Http\Controllers\AppointmentController@edit');
        Route::post('appointment/getAppointmentsByDate', 'App\Http\Controllers\AppointmentController@getAppointmentsByDate');
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

        //Applicant
        Route::get('get-clinician-list', 'App\Http\Controllers\ApplicantController@getClinicianList');
        Route::get('get-clinician-detail/{id}', 'App\Http\Controllers\ApplicantController@getClinicianDetail');

        Route::get('applicants', 'App\Http\Controllers\ApplicantController@index');
        /*Route::post('applicants/step-one', 'App\Http\Controllers\ApplicantController@stepOne');
        Route::post('applicants/step-two', 'App\Http\Controllers\ApplicantController@stepTwo');
        Route::post('applicants/step-three', 'App\Http\Controllers\ApplicantController@stepThree');
        Route::post('applicants/step-four', 'App\Http\Controllers\ApplicantController@stepFour');*/
        Route::post('applicants/steps', 'App\Http\Controllers\ApplicantController@allStepTogether');
        Route::get('address-life', 'App\Http\Controllers\ApplicantController@addressLife');
        Route::get('relationship', 'App\Http\Controllers\ApplicantController@relationship');
        Route::get('age-range-treated', 'App\Http\Controllers\ApplicantController@ageRangeTreated');
        Route::get('ccm', 'App\Http\Controllers\ApplicantController@ccm');
        Route::get('clinician-services', 'App\Http\Controllers\ApplicantController@clinicianServices');
        Route::get('certifying-board', 'App\Http\Controllers\ApplicantController@certifyingBoard');
        Route::get('certifying-board-status', 'App\Http\Controllers\ApplicantController@certifyingBoardStatus');
        Route::get('work-gap-reasons', 'App\Http\Controllers\ApplicantController@workGapReasons');
        Route::get('bank-account-types', 'App\Http\Controllers\ApplicantController@bankAccountTypes');
        Route::get('send-tax-documents-to', 'App\Http\Controllers\ApplicantController@sendTaxDocumentsTo');
        Route::get('legal-entities', 'App\Http\Controllers\ApplicantController@legalEntities');
        Route::get('security-questions', 'App\Http\Controllers\ApplicantController@securityQuestions');
        Route::post('education', 'App\Http\Controllers\ApplicantController@education');
        Route::get('education', 'App\Http\Controllers\ApplicantController@getEducation');
        Route::get('certificates', 'App\Http\Controllers\CertificateController@index');
        Route::post('certificates', 'App\Http\Controllers\CertificateController@store');
        Route::get('work-history', 'App\Http\Controllers\ApplicantController@getWorkHistories');
        Route::post('work-history', 'App\Http\Controllers\ApplicantController@workHistory');
        Route::get('attestation', 'App\Http\Controllers\ApplicantController@getAttestations');
        Route::post('attestation', 'App\Http\Controllers\ApplicantController@attestation');
        Route::get('bank-account', 'App\Http\Controllers\ApplicantController@getBankAccount');
        Route::post('bank-account', 'App\Http\Controllers\ApplicantController@bankAccount');
        Route::get('security', 'App\Http\Controllers\ApplicantController@getSecurities');
        Route::post('security', 'App\Http\Controllers\ApplicantController@security');
        Route::post('document-verification', 'App\Http\Controllers\ApplicantController@documentVerification');
        Route::get('get-documents', 'App\Http\Controllers\ApplicantController@getDocuments');
        Route::post('remove-documents', 'App\Http\Controllers\ApplicantController@removeDocument');
        Route::post('change-availability', 'App\Http\Controllers\UserController@changeAvailability');
    });
});

Route::group([
    'middleware' => ['auth:api','role:patient|clinician'],
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
    'middleware' => ['auth:api','role:clinician|co-ordinator|patient'],
], function () {
// Patient Road L API
    Route::post('clinician-request-accept', 'App\Http\Controllers\PatientRequestController@clinicianRequestAccept');
    Route::post('clinician-patient-request-list', 'App\Http\Controllers\PatientRequestController@clinicianPatientRequestList');
    Route::get('get-near-by-clinician-list/{patient_request_id}', 'App\Http\Controllers\RoadlController@getNearByClinicianList');
    Route::get('get-roadl-proccess/{patient_request_id}', 'App\Http\Controllers\RoadlController@getRoadLProccess');
    Route::post('create-virtual-room', 'App\Http\Controllers\SessionsController@createRoom');
    Route::get('get-patient-list', 'App\Http\Controllers\PatientController@getPatientList');
    Route::get('get-new-patient-list', 'App\Http\Controllers\PatientController@getNewPatientList');
    Route::get('get-schedule-appoiment-list', 'App\Http\Controllers\PatientController@scheduleAppoimentList');
    Route::get('get-cancel-appoiment-list', 'App\Http\Controllers\PatientController@cancelAppoimentList');
    Route::get('get-roadl-status', 'App\Http\Controllers\PatientRequestController@getRoadLStatus');
    Route::post('change-patient-status', 'App\Http\Controllers\PatientController@changePatientStatus');    
    //new patient list for appointment
    Route::post('getNewPatientListForAppointment', 'App\Http\Controllers\PatientController@getNewPatientListForAppointment');
    //Appointment
    Route::post('send-video-meeting-notification', 'App\Http\Controllers\SessionsController@sendVideoMeetingNotification');
    Route::post('start-video-meeting-notification', 'App\Http\Controllers\SessionsController@startVideoMeetingNotification');
    Route::post('leave-video-meeting', 'App\Http\Controllers\SessionsController@leaveVideoMeeting');
});

// Referral
Route::group([
    'prefix' => 'auth'
], function () {
    Route::get('patient-referral/{id}', 'App\Http\Controllers\PatientReferralController@index')->name('referral_patients');
    Route::get('get-patient-detail/{id}', 'App\Http\Controllers\UserController@getPatientDetail')->name('patient.detail');
    Route::post('store-patient', 'App\Http\Controllers\PatientReferralController@storePatient');
});

// Co Ordinator
Route::group([
    'middleware' => ['auth:api','role:co-ordinator'],
], function () {
    //Route::get('/get-patient-list','');
    Route::get('getNewPatientListForAppointment', 'App\Http\Controllers\PatientController@getNewPatientListForAppointment');
});

// Supervisor
Route::group([
    'middleware' => ['auth:api','role:supervisor'],
], function () {
    /*Route::get('getNewPatientListForAppointment1', 'App\Http\Controllers\PatientController@getNewPatientListForAppointment');*/
    Route::get('assign-clinician-to-patient', 'App\Http\Controllers\AssignClinicianToPatientController@index');
    Route::post('assign-clinician-to-patient', 'App\Http\Controllers\AssignClinicianToPatientController@store');
    Route::post('filter-by-clinician', 'App\Http\Controllers\AssignClinicianToPatientController@filter');
    Route::post('assign-clinician', 'App\Http\Controllers\AssignClinicianToPatientController@assign');
    Route::post('remove-clinician', 'App\Http\Controllers\AssignClinicianToPatientController@remove');
});


// Co Ordinator
Route::group([
    'middleware' => ['auth:api'],
], function () {
    Route::post('get-clinician-time-slots', 'App\Http\Controllers\AppointmentController@getClinicianTimeSlots');
    Route::post('appointment/store', 'App\Http\Controllers\AppointmentController@store')->middleware('role:co-ordinator|patient');
    Route::put('appointment/{id}/update', 'App\Http\Controllers\AppointmentController@store')->middleware('role:co-ordinator|patient');
    Route::post('appointment/cancel-appointment', 'App\Http\Controllers\AppointmentController@cancelAppointment' )->middleware('role:co-ordinator|patient|clinician');
    Route::post('appointment/patient-md-form', 'App\Http\Controllers\PatientMdFormController@store' )->middleware('role:co-ordinator|patient|clinician');
    Route::post('add-insurance', 'App\Http\Controllers\PatientInsuranceController@updateOrCreateInsurance');
    Route::post('demographyData-update', 'App\Http\Controllers\UserController@demographyDataUpdate');
    Route::get('patient-medicine-list/{patient_id}', 'App\Http\Controllers\MedicineController@index');
    Route::post('add-medicine', 'App\Http\Controllers\MedicineController@store');
    Route::get('ccm-reading-level-high', 'App\Http\Controllers\UserController@ccmReadingLevelHigh');
    Route::post('appointments', 'App\Http\Controllers\AppointmentController@appointments');
});

// Get list of meetings.
Route::get('/meetings', 'App\Http\Controllers\Zoom\MeetingController@list');

// Create meeting room using topic, agenda, start_time.
Route::post('/meetings', 'App\Http\Controllers\Zoom\MeetingController@create');

// Get information of the meeting room by ID.
Route::get('/meetings/{id}', 'App\Http\Controllers\Zoom\MeetingController@get')->where('id', '[0-9]+');
Route::patch('/meetings/{id}', 'App\Http\Controllers\Zoom\MeetingController@update')->where('id', '[0-9]+');
Route::delete('/meetings/{id}', 'App\Http\Controllers\Zoom\MeetingController@delete')->where('id', '[0-9]+');

Route::post('/lab-report/store', 'App\Http\Controllers\PatientLabReportController@store')->name('lab-report.store');
Route::post('/lab-report-note/store', 'App\Http\Controllers\PatientLabReportController@addNote')->name('lab-report-note.store');
