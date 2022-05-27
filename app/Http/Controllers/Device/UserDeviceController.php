<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Jobs\AlertNotification;
use App\Jobs\SendAlertSMS;
use App\Models\ApiKey;
use App\Models\UserDevice;
use App\Models\UserDeviceLog;
use App\Models\UserLatestDeviceLog;
use App\Models\Demographic;
use Carbon\Carbon;
use App\Mail\SendErrorEmail;
use App\Models\CareTeam;
use App\Models\CaseManagement;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Nexmo\Laravel\Facade\Nexmo;
use Twilio\Rest\Client;

class UserDeviceController extends Controller
{
    public function addDevice(Request $request)
    {
        try {
            $input = $request->all();

            $readingval = $input["value"];

            $apiKey = ApiKey::where([['name', '=', $input['AppName']],['key', '=', $input['AppKey']],['secret', '=', $input['AppSecret']]])->first();
            
            $patient_id = '';
            if ($apiKey && $apiKey->id == '2') {
                $userDeviceModel = UserDevice::with(['user','demographic.company','demographic'])->where([['patient_id', '=', $input['patient_id']],['device_type', '=', $input['device_type']]])->first();
                $patient_id = $input['patient_id'];
            } else if ($apiKey && $apiKey->id != '2') {
                $userDeviceModel = UserDevice::with(['user','demographic.company','demographic' => function ($q) use($apiKey) {
                    $q->where('company_id', $apiKey->company_id);
                }])->where([['user_id', '=', $input['user_id']],['device_type', '=', $input['device_type']]])->first();
                $patient_id = '11797'      ;
            }
          
            $userDevice = $userDeviceModel;
            if(! $userDevice) {
                $userDevice = new UserDevice();
                $userDevice->user_id = $input['user_id'];
                $userDevice->device_type = $input['device_type'];
                $userDevice->patient_id = $patient_id;
                $userDevice->save();
            }              
                  	
            //if ($userDevice->demographic != '') {
                $userDeviceLog = new UserDeviceLog();
                $userDeviceLog->user_device_id = $userDevice->id;
                $userDeviceLog->value = $input['value'];
                $userDeviceLog->reading_time = date('Y-m-d H:i:s', strtotime($input['datetime']));

                $readingLevel = 1;
                $level_message = '';
                
                if ($input['device_type'] == 1) {
                    $readingLevel = 1;
                    if (Str::contains($input['value'], ['/'])) {
                        $explodeValue = explode("/",$input['value']);
                    } else if (Str::contains($input['value'], [':'])) {
                        $explodeValue = explode(":",$input['value']);
                    }
                        
                    if($explodeValue[0] >= 140 || $explodeValue[1] >= 90) {
                        $readingLevel = 3;
                        $level_message = 'blood pressure is higher';
                    } else if($explodeValue[0] <= 100 || $explodeValue[1] <= 60) {
                        $readingLevel = 3;
                        $level_message = 'blood pressure is lower';
                    }
                    
                } else if ($input['device_type'] == 2) {
                    $readingLevel = 1;
                    if($input['value'] >= 300) {
                        $readingLevel = 3;
                        $level_message = 'blood sugar is higher';
                    } else if($input['value'] <= 60) {
                        $readingLevel = 3;
                        $level_message = 'blood sugar is lower';
                    }
                }
                
                $userDeviceLog->level = $readingLevel;

                $userDeviceLog->save();
               
            	//Latest Device Reading Start
                $userLatestDevice = UserLatestDeviceLog::where([['patient_id', '=', $userDevice->patient_id],['device_type', '=', $input['device_type']]])->first();
            
                if(! $userLatestDevice) {
                    $userDeviceLatest = new UserLatestDeviceLog();
                    $userDeviceLatest->patient_id = $userDevice->patient_id;
                    $userDeviceLatest->user_device_id = $userDevice->id;
                    $userDeviceLatest->device_type = $input['device_type'];
                    $userDeviceLatest->level = $readingLevel;
                    $userDeviceLatest->value = $input['value'];
                    $userDeviceLatest->reading_time = date('Y-m-d H:i:s', strtotime($input['datetime']));
                    $userDeviceLatest->save();
                }else {
                    $userDeviceLatest = DB::table('user_latest_device_logs')
                    ->where(['patient_id' =>$userDevice->patient_id,'device_type' => $input['device_type']])
                    ->update(['user_device_id' => $userDevice->id, 'level' => $readingLevel, 'value' => $readingval, 'reading_time' => date('Y-m-d H:i:s', strtotime($input['datetime']))]);
                }
                // Latest Device Reading End
		
                if ($readingLevel == 3) {
                                        
                    $patient_name = $userDevice->user->first_name . ' ' . $userDevice->user->last_name;
                
                    $message = 'Doral Health Connect | Your family member - ' . $patient_name . ' ' . $level_message . ' than regular. Need immediate attention. http://app.doralhealthconnect.com/ccm/'.$userDeviceLog->id;

                    if ($apiKey && $apiKey->id == '2') {
                        $company_id = Demographic::where('user_id',$input['patient_id'])->first()->company_id;
                    } else if ($apiKey && $apiKey->id != '2') {
                        $company_id = $apiKey->company_id;
                    }
                    
                    
                  
                    if ($company_id) {
                        $company = Company::where('id',$company_id)->first();
                            $detail = [
                            'patient_id' => $userDevice->patient_id,
                            'message' => $message,
                            'company' => $company
                        ];
                        //AlertNotification::dispatch($detail);
                        //$this->sendEmailToVisitor($userDevice->patient_id,$message,$company);
                    }
                }

                return $this->generateResponse(true,'User device log added successfully.',$userDeviceLog,200);
            // } else {
            //     return $this->generateResponse(true,'Unauthenticated',null,401);
            // }
        }catch (\Exception $exception){
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }
    
    public function sendEmailToVisitor($patient_id,$message,$company)
    {
   
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

        // try {
        //     $client->messages->create('+19293989855', [
        //         'from' => $twilio_number, 
        //         'body' => $message]);  	    
            
        // }catch (\Exception $exception){
        //     \Log::info($exception);
        // }
        
        if ($company->texed === '1') {
            $demographic = Demographic::with(['user'=> function($q){
                $q->select('id','first_name', 'last_name');
            }])->where('user_id',$patient_id)->select('id', 'user_id', 'patient_id')->first();
       
            if ($demographic) {
                $input['patientId'] = $demographic->patient_id;
                $date = Carbon::now();// will get you the current date, time
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
            }
            $caseManagers = CaseManagement::with('clinician')->where([['patient_id', '=' ,$patient_id]])->get();
            foreach ($caseManagers as $key => $caseManager) {
                try {
                    $client->messages->create('+1'.setPhone($caseManager->clinician->phone), [
                        'from' => $twilio_number, 
                        'body' => $message
                    ]);  
                                
                } catch (Exception $e) {
                    dd("Error: ". $e->getMessage());
                }    
            }

            $careTeams = CareTeam::where([['patient_id', '=' ,$patient_id],['detail->texed', '=', 'on']])->whereIn('type',['1','2'])->get();
        
            foreach ($careTeams as $key => $value) {
                try {
                    $client->messages->create('+1'.setPhone($value->detail['phone']), [
                        'from' => $twilio_number, 
                        'body' => $message]);  	    
                    
                }catch (\Exception $exception){
                    \Log::info($exception);
                }
            }
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
         try {
             $client->messages->create('+1'.setPhone($phoneNumber), [
                'from' => $twilio_number, 
                'body' => $message
            ]);
     
	  } catch (\Twilio\Exceptions\RestException $e) {
		//echo "Couldn't send message to $phoneNumber\n";
	  }
           
        }
    }

    public function getDevice(Request $request) {
       
        try {
            $device_logs = UserLatestDeviceLog::with('userDevice')->where('patient_id',$request->patient_id)->get();

            return $this->generateResponse(true, 'CCM Readings!', $device_logs, 200);
        } catch (\Exception $ex) {
            return $this->generateResponse(false, $ex->getMessage(), null, 200);
        }
    }
    
     public function runScrap(Request $request)
    {
        Artisan::call("send:dueReportNotification");
        
       return $this->generateResponse(true, 'CCM Readings!', null, 200);
    }
}
