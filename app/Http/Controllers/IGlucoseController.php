<?php

namespace App\Http\Controllers;

//use App\Models\SymptomsMaster;
//use App\Models\Test;

use App\Models\CareTeam;
use App\Models\CaseManagement;
use App\Models\Company;
use App\Models\Demographic;
use App\Models\Iglucose;
use App\Models\UserDevice;
use App\Models\UserDeviceLog;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class IGlucoseController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getReading(Request $request)
    {
        $input = $request->all();
        $userDevice = UserDevice::with(['user','demographic.company','demographic'])->where('user_id', $input['device_id'])->first();
        
        if($userDevice) {
            if ($input['reading_type'] === 'blood_pressure') {
                $input['value'] = $input['systolic_mmhg'] . '/' . $input['diastolic_mmhg'];                
            } else if ($input['reading_type'] === 'blood_glucose') {
                $input['value'] = $input['blood_glucose_mgdl'];
            }

            $userDeviceLog = new UserDeviceLog();
            $userDeviceLog->user_device_id = $userDevice->id;
            $userDeviceLog->value = $input['value'];
            $userDeviceLog->reading_time = date('Y-m-d H:i:s', strtotime($input['date_received']));
            $userDeviceLog->reading_json = $request->all();

            $readingLevel = 1;
            $level_message = '';
                
            if ($input['device_type'] == 1) {
                $readingLevel = 1;
               
                if($input['systolic_mmhg'] >= 140 || $input['diastolic_mmhg'] >= 90) {
                    $readingLevel = 3;
                    $level_message = 'blood pressure is higher';
                } else if($input['systolic_mmhg'] <= 100 || $input['diastolic_mmhg'] <= 60) {
                    $readingLevel = 3;
                    $level_message = 'blood pressure is lower';
                }
            } else if ($input['device_type'] == 2) {
                $readingLevel = 1;
                if($input['blood_glucose_mgdl'] >= 300) {
                    $readingLevel = 3;
                    $level_message = 'blood sugar is higher';
                } else if($input['blood_glucose_mgdl'] <= 60) {
                    $readingLevel = 3;
                    $level_message = 'blood sugar is lower';
                }
            }
                
            $userDeviceLog->level = $readingLevel;

            $userDeviceLog->save();

            if ($readingLevel == 3) {
                                        
                $patient_name = $userDevice->user->first_name . ' ' . $userDevice->user->last_name;
            
                $message = 'Doral Health Connect | Your family member - ' . $patient_name . ' ' . $level_message . ' than regular. Need immediate attention. http://app.doralhealthconnect.com/ccm/'.$userDeviceLog->id;

                $demographic = Demographic::with(['user'=> function($q){
                    $q->select('id','first_name', 'last_name');
                }])->where('user_id',$input['patient_id'])->select('id', 'user_id', 'patient_id', 'company_id')->first();
              
                if ($demographic && $demographic->company_id) {
                    $company = Company::where('id',$demographic->company_id)->first();
                    if ($company->texed === '1') {
                        $details = [
                            'patient_id' => $userDevice->patient_id,
                            'message' => $message,
                            'company' => $company,
                            'demographic' => $demographic
                        ];

                        $this->sendEmailToVisitor($details);
                    }
                }
            }

            return $this->generateResponse(true,'User device log added successfully.',$userDeviceLog,200);
        } 

        Iglucose::create([
            $input['reading'] => $request->all()
        ]);
       
        return $this->generateResponse(true,'Please attached user device.',[],200);
    }

    public function sendEmailToVisitor($details)
    {
        $company = $details['company'];
        $patient_id = $details['patient_id'];
        $message = $details['message'];
        $demographic = $details['demographic'];

        if ($company->account_sid != '' && $company->auth_token != '' && $company->from_sms != '') {
            $account_sid = $company->account_sid;
            $auth_token = $company->auth_token;
            $twilio_number = '+1'.$company->from_sms;
        } else {
            $account_sid = 'AC509601378833a11b18935bf0fe6387cc';
            $auth_token = '7c6296070a54f124911fa4098467f03a';
            $twilio_number = '+12184133934';
        }       
        
        $client = new Client($account_sid, $auth_token);
       
        $input['patientId'] = $demographic->patient_id;
        $date = Carbon::now();
        $today = $date->format("Y-m-d");

        $input['from_date'] = $today;
        $input['to_date'] = $today;
    
        $curlFunc = searchVisits($input);   

        if (isset($curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits'])) {
            $visitID = $curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits']['VisitID'];
            if(is_array($visitID)) {
                foreach ($visitID as $viId) {
                    self::getSchedule($viId, $twilio_number, $client, $message);
                }
            } else {
                $viId = $curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits']['VisitID'];
                self::getSchedule($viId, $twilio_number, $client, $message);
            }
        }
            
        $caseManagers = CaseManagement::with('clinician')->where([['patient_id', '=', $patient_id]])->get();
        foreach ($caseManagers as $key => $caseManager) {
            $to = $caseManager->clinician->phone;
            static::sendSMS($client, $to, $twilio_number, $message);
        }

        $careTeams = CareTeam::where([['patient_id', '=', $patient_id],['detail->texed', '=', 'on']])->whereIn('type',['1','2'])->get();
    
        foreach ($careTeams as $key => $value) {
            $to = $value->detail['phone'];
            static::sendSMS($client, $to, $twilio_number, $message);
        }
    }
    
    public static function getSchedule($viId, $twilio_number, $client, $message)
    {	
        $scheduleInfo = getScheduleInfo($viId);
        $getScheduleInfo = $scheduleInfo['soapBody']['GetScheduleInfoResponse']['GetScheduleInfoResult']['ScheduleInfo'];
        $caregiver_id = ($getScheduleInfo['Caregiver']['ID']) ? $getScheduleInfo['Caregiver']['ID'] : '' ;
        
        $demographicModal = Demographic::select('id','user_id','patient_id')->where('patient_id', $caregiver_id)->with(['user' => function($q) {
            $q->select('id', 'email', 'phone');
        }])->first();
        $phoneNumber = '';
        if ($demographicModal && $demographicModal->user->phone != '') {
            $phoneNumber = $demographicModal->user->phone;
        } else {
            $getdemographicDetails = getCaregiverDemographics($caregiver_id);
            if (isset($getdemographicDetails['soapBody']['GetCaregiverDemographicsResponse']['GetCaregiverDemographicsResult']['CaregiverInfo'])) {
                $demographics = $getdemographicDetails['soapBody']['GetCaregiverDemographicsResponse']['GetCaregiverDemographicsResult']['CaregiverInfo'];

                $phoneNumber = $demographics['Address']['HomePhone'] ? $demographics['Address']['HomePhone'] : '';
            }
        }
	
        if($phoneNumber) {
            static::sendSMS($client, $phoneNumber, $twilio_number, $message);
        }
    }

    public static function sendSMS($client, $to, $twilio_number, $message)
    {
        try {
            $client->messages->create('+1'.setPhone($to), [
                'from' => $twilio_number, 
                'body' => $message]);  	    
            
        }catch (\Exception $exception){
            Log::info($exception);
        }
    }
}
