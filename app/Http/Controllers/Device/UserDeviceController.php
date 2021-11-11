<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SmsController;
use App\Models\ApiKey;
use App\Models\Demographic;
use App\Models\UserDevice;
use App\Models\UserDeviceLog;
use App\Models\UserLatestDeviceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Nexmo\Laravel\Facade\Nexmo;
use DB;

class UserDeviceController extends Controller
{
    public function addDevice(Request $request)
    {
        try {
            $input = $request->all();
            $readingval = $input["value"];
            $apiKey = ApiKey::where([['name', '=', $input['AppName']],['key', '=', $input['AppKey']],['secret', '=', $input['AppSecret']]])->first();
            if ($apiKey) {
                $userDevice = UserDevice::with(['user','demographic' => function ($q) use($apiKey) {
                    $q->where('company_id', $apiKey->company_id);
                }])->where([['user_id', '=', $input['user_id']],['device_type', '=', $input['device_type']]])->first();
                
                if(! $userDevice) {
                    $userDevice = new UserDevice();
                    $userDevice->user_id = $input['user_id'];
                    $userDevice->device_type = $input['device_type'];
                    $userDevice->patient_id = '11979';
                    $userDevice->save();
                }
                
                if ($userDevice->demographic != '') {
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
                    
                    // Latest Device Reading Start
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
                        try {
                            Nexmo::message()->send([
                                'to'   =>'+1'.env('SEND_SMS'),
                                'from' => env('SMS_FROM'),
                                'text' => $message
                            ]);

                            $this->sendEmailToVisitor('826323',$message);

                        } catch (\Exception $exception){
                          
                            Log::info($exception);
                        }
                    }

                    return $this->generateResponse(true,'User device log added successfully.',$userDeviceLog,200);
                } else {
                    return $this->generateResponse(true,'Unauthenticated',null,401);
                }
                 
            } else {
                return $this->generateResponse(true,'Unauthenticated',null,401);
            }
        }catch (\Exception $exception){
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }

    public function sendEmailToVisitor($patient_id,$message)
    {
        $date = Carbon::now();// will get you the current date, time
        $today = $date->format("Y-m-d");
        $data = '<?xml version="1.0" encoding="utf-8"?><SOAP-ENV:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Body><SearchVisits xmlns="https://www.hhaexchange.com/apis/hhaws.integration"><Authentication><AppName>HCHS257</AppName><AppSecret>99473456-2939-459c-a5e7-f2ab47a5db2f</AppSecret><AppKey>MQAwADcAMwAxADMALQAzADEAQwBDADIAQQA4ADUAOQA3AEEARgBDAEYAMwA1AEIARQA0ADQANQAyAEEANQBFADIAQgBDADEAOAA=</AppKey></Authentication><SearchFilters><StartDate>' . $today .'</StartDate><EndDate>' . $today . '</EndDate><PatientID>' . $patient_id . '</PatientID></SearchFilters></SearchVisits></SOAP-ENV:Body></SOAP-ENV:Envelope>';
        $method = 'POST';
        $curlFunc = $this->curlCall($data, $method);
        if (isset($curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits'])) {
            $visitID = $curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits']['VisitID'];
          
            foreach ($visitID as $viId) {
                $vid = $viId;
                $scheduleInfo = $this->getScheduleInfo($viId);
                $getScheduleInfo = $scheduleInfo['soapBody']['GetScheduleInfoResponse']['GetScheduleInfoResult']['ScheduleInfo'];

                $caregiver_id = ($getScheduleInfo['Caregiver']['ID']) ? $getScheduleInfo['Caregiver']['ID'] : '' ;
                $demographic = Demographic::select('id','user_id','patient_id')->where('patient_id', $caregiver_id)->with(['user' => function($q) {
                    $q->select('id', 'email', 'phone');
                }])->first();

                $data = Nexmo::message()->send([
                    // 'to'   =>'+1'.$demographic->phone,
                    'to'   =>'+1'.env('SEND_SMS'),
                    'from' => env('SMS_FROM'),
                    'text' => $message
                ]);

            }
        }
    }

    public function curlCall($data, $method)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => config('patientDetailAuthentication.AppUrl'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
               'Content-Type: text/xml'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
        return json_decode(json_encode((array)$xml), TRUE);
    }
}
