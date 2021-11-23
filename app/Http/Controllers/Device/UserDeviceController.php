<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Jobs\SendAlertSMS;
use App\Models\ApiKey;
use App\Models\UserDevice;
use App\Models\UserDeviceLog;
use App\Models\UserLatestDeviceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
                    $userDevice->patient_id = '9170';
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

                        $details = [
                            'message' => $message,
                            'phone' => env('SEND_SMS'),
                        ];
                        SendAlertSMS::dispatch($details);
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
}
