<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\UserDevice;
use App\Models\UserDeviceLog;
use App\Models\Demographic;
use Carbon\Carbon;
use App\Models\CareTeam;
use App\Models\CaseManagement;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class UserDeviceController extends Controller
{
    public function addDevice(Request $request)
    {
        try {
            $input = $request->all();

            $readingval = $input["value"];

            $apiKey = ApiKey::where([['name', '=', $input['AppName']],['key', '=', $input['AppKey']],['secret', '=', $input['AppSecret']]])->first();
            
            if($apiKey && isset($input['patient_id'])) {
                $userDevice = UserDevice::with(['user','demographic.company','demographic'])->where([['patient_id', '=', $input['patient_id']],['device_type', '=', $input['device_type']]])->first();
          
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
            }
            return $this->generateResponse(true,'User device not found.',[],200);
        } catch (\Exception $exception){
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
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

    public function getDevice(Request $request)
    {
        try {
            $paient_id = $request->patient_id;
            $device_logs = UserDeviceLog::with('userDevice')->whereHas('userDevice',function ($q) use($paient_id) {
                $q->where('patient_id', $paient_id);
            })
            ->select('*', DB::raw('DATE(reading_time) as date'))
            ->WhereIn('user_device_logs.id',DB::table('user_device_logs AS udl')
                ->join('user_devices','user_devices.id','=','udl.user_device_id' )->where(
                    'user_devices.patient_id',$paient_id
                )
                ->groupBy('udl.user_device_id')
                ->orderBy('udl.id','DESC')->pluck(DB::raw('MAX(udl.id) AS id') )
            )
            ->get();

            return $this->generateResponse(true, 'CCM Readings!', $device_logs, 200);
        } catch (\Exception $ex) {
            return $this->generateResponse(false, $ex->getMessage(), null, 200);
        }
    }
    
    public function runScrap()
    {
        Artisan::call("send:dueReportNotification");        
        return $this->generateResponse(true, 'CCM Readings!', null, 200);
    }
}