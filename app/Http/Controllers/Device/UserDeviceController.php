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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Nexmo\Laravel\Facade\Nexmo;

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
                $userDevice = UserDevice::with(['user','demographic' => function ($q) use($apiKey) {
                    $q->where('company_id', $apiKey->company_id);
                }])->where([['patient_id', '=', $input['patient_id']],['device_type', '=', $input['device_type']]])->first();
                $patient_id = $input['patient_id'];
            } else if ($apiKey && $apiKey->id != '2') {
                $userDevice = UserDevice::with(['user','demographic' => function ($q) use($apiKey) {
                    $q->where('company_id', $apiKey->company_id);
                }])->where([['user_id', '=', $input['user_id']],['device_type', '=', $input['device_type']]])->first();
                $patient_id = '11797'      ;
            } 
            
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
                $userDeviceLog->reading_time = $input['datetime'];

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
                    $userDeviceLatest->reading_time = $input['datetime'];
                    $userDeviceLatest->save();
                }else {
                    $userDeviceLatest = DB::table('user_latest_device_logs')
                    ->where(['patient_id' =>$userDevice->patient_id,'device_type' => $input['device_type']])
                    ->update(['user_device_id' => $userDevice->id, 'level' => $readingLevel, 'value' => $readingval, 'reading_time' => $input['datetime']]);
                }
                // Latest Device Reading End
	
                if ($readingLevel == 3) {
                                        
                    $patient_name = $userDevice->user->first_name . ' ' . $userDevice->user->last_name;
                
                    $message = 'Doral Health Connect | Your family member - ' . $patient_name . ' ' . $level_message . ' than regular. Need immediate attention. http://app.doralhealthconnect.com/ccm/'.$userDeviceLog->id;

                    $details = [
                        'message' => $message,
                        'phone' => env('SEND_SMS'),
                        //'phone' => '9293989855',
                        'patient_id' => $userDevice->patient_id,
                    ];      
                                    
                    $this->sendEmailToVisitor($userDevice->patient_id,$message,env('SEND_SMS'));
                    //AlertNotification::dispatch($details);
                }

                return $this->generateResponse(true,'User device log added successfully.',$userDeviceLog,200);
               
            //} else {
            //     return $this->generateResponse(true,'Unauthenticated',null,401);
            // }
        }catch (\Exception $exception){
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }
    
    public function sendEmailToVisitor($patient_id,$message,$phone)
    {
        Log::info('doral message send start');
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
        	
	        // if(count($visitID) > 1) {
            //     foreach ($visitID as $viId) {
                
            //         $scheduleInfo = getScheduleInfo($viId);
            //         $getScheduleInfo = $scheduleInfo['soapBody']['GetScheduleInfoResponse']['GetScheduleInfoResult']['ScheduleInfo'];
            //         $caregiver_id = ($getScheduleInfo['Caregiver']['ID']) ? $getScheduleInfo['Caregiver']['ID'] : '' ;
                   
            //         $demographicModal = Demographic::select('id','user_id','patient_id')->where('patient_id', $caregiver_id)->with(['user' => function($q) {
            //             $q->select('id', 'email', 'phone');
            //         }])->first();
                  
            //         if ($demographicModal && $demographicModal->user->phone != '') {
            //             $phoneNumber = $demographicModal->user->phone;
            //         } else {
            //             $getdemographicDetails = getCaregiverDemographics($caregiver_id);
            //             $demographics = $getdemographicDetails['soapBody']['GetCaregiverDemographicsResponse']['GetCaregiverDemographicsResult']['CaregiverInfo'];
        
            //             $phoneNumber = $demographics['Address']['HomePhone'] ? $demographics['Address']['HomePhone'] : '';
            //         }
                  
            //         if($phoneNumber) {
                  
		    //        //  Log::info('patient message send start');
		    //         //$this->sendsmsToMe($message, $phoneNumber);
		    //         //Log::info('patient message send end');
		    //         try {
		           
			// 	    $ms = Nexmo::message()->send([
			// 		'to'   =>'+1'.setPhone($phoneNumber),
			// 		'from' => env('SMS_FROM'),
			// 		'text' => $message
			// 	    ]);				    
				   
			// 	}catch (\Exception $exception){
				
			// 	    \Log::info($exception);
			// 	}
            //         }
                   
            //     }
            // } else {
                $viId = $curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits']['VisitID'];
            
                $scheduleInfo = getScheduleInfo($viId);
                $getScheduleInfo = $scheduleInfo['soapBody']['GetScheduleInfoResponse']['GetScheduleInfoResult']['ScheduleInfo'];
                $caregiver_id = ($getScheduleInfo['Caregiver']['ID']) ? $getScheduleInfo['Caregiver']['ID'] : '' ;
                
                $demographicModal = Demographic::select('id','user_id','patient_id')->where('patient_id', $caregiver_id)->with(['user' => function($q) {
                    $q->select('id', 'email', 'phone');
                }])->first();
                
                if ($demographicModal && $demographicModal->user->phone != '') {
                    $phoneNumber = $demographicModal->user->phone;
                } else {
                    $getdemographicDetails = getCaregiverDemographics($caregiver_id);
                    $demographics = $getdemographicDetails['soapBody']['GetCaregiverDemographicsResponse']['GetCaregiverDemographicsResult']['CaregiverInfo'];

                    $phoneNumber = $demographics['Address']['HomePhone'] ? $demographics['Address']['HomePhone'] : '';
                }
                Log::info('patient message send start');
                //$this->sendsmsToMe($message, $phoneNumber);
                   try {
		           
				    $ms = Nexmo::message()->send([
					//'to'   =>'+1'.setPhone($phoneNumber),
                    'to'   =>'+918511380657',
					'from' => env('SMS_FROM'),
					'text' => 'Patient'
				    ]);				    
				   
				}catch (\Exception $exception){
				
				    \Log::info($exception);
				}
                Log::info('patient message send end');
            //}
        }
	}
        $caseManagers = CaseManagement::with('clinician')->where([['patient_id', '=' ,$patient_id]])->get();
               
        foreach ($caseManagers as $key => $caseManager) {
       
            Log::info('case manager message send start');
            //$this->sendsmsToMe($message, $caseManager->clinician->phone);
               try {
				    Nexmo::message()->send([
                        //'to'   =>'+1'.setPhone($caseManager->clinician->phone),
                        'to'   =>'+918511380657',
                        'from' => env('SMS_FROM'),
                        'text' => 'case manager'
				    ]);				    
				}catch (\Exception $exception){
				
				    \Log::info($exception);
				}
            Log::info('case manager message send end');
        }

        $careTeams = CareTeam::where([['patient_id', '=' ,$patient_id],['detail->texed', '=', 'on']])->whereIn('type',['1','2'])->get();
      
        foreach ($careTeams as $key => $value) {
            Log::info('care team message send start');
            //$this->sendsmsToMe($message, setPhone($value->detail['phone']));
               try {
		           
				    Nexmo::message()->send([
                        //'to'   =>'+1'.setPhone($value->detail['phone']),
                        'to'   =>'+918511380657',
                        'from' => env('SMS_FROM'),
                        'text' => 'care team'
				    ]);				    
				   
				}catch (\Exception $exception){
				
				    \Log::info($exception);
				}
            Log::info('care team message send end');
        }
         Log::info('doral message send end');
    }

    public function sendsmsToMe($message, $phone) {	
        $to = $phone;
        $from = "12089104598";	
        $api_key = "bb78dfeb";
        $api_secret = "PoZ5ZWbnhEYzP9m4";	
        $uri = 'https://rest.nexmo.com/sms/json';	
        $text = $message;	
        $fields = '&from=' . urlencode($from) .	
                '&text=' . urlencode($text) .	
                '&to=+1' . urlencode($to) .	
                '&api_key=' . urlencode($api_key) .	
                '&api_secret=' . urlencode($api_secret);	
        $res = curl_init($uri);	
        curl_setopt($res, CURLOPT_POST, TRUE);	
        curl_setopt($res, CURLOPT_RETURNTRANSFER, TRUE); // don't echo	
        curl_setopt($res, CURLOPT_SSL_VERIFYPEER, FALSE);	
        curl_setopt($res, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);	
        curl_setopt($res, CURLOPT_POSTFIELDS, $fields);	
        curl_exec($res);

        if (curl_errno($res)) {
            $error_msg = curl_error($res);
        }
        curl_close($res);

        if (isset($error_msg)) {
            $details = [
               'message' => $error_msg,
            ];

            Mail::to('shashikant@hcbspro.com')->send(new SendErrorEmail($details));
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
}