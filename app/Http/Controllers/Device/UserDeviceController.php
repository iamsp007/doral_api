<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SmsController;
use App\Models\ApiKey;
use App\Models\UserDevice;
use App\Models\UserDeviceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Nexmo\Laravel\Facade\Nexmo;

class UserDeviceController extends Controller
{
    public function addDevice(Request $request)
    {
        try {
            $input = $request->all();

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

                    if ($readingLevel == 3) {
                      
                        $patient_name = $userDevice->user->first_name . ' ' . $userDevice->user->last_name;
                    
                        $message = 'Doral Health Connect | Your family member - ' . $patient_name . ' ' . $level_message . ' than regular. Need immediate attention. http://app.doralhealthconnect.com/ccm/'.$userDeviceLog->id;
                        try {
                            $data = Nexmo::message()->send([
                                'to'   =>'+1'.env('SEND_SMS'),
                                'from' => env('SMS_FROM'),
                                'text' => $message
                            ]);
                           
                        }catch (\Exception $exception){
                          
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
}
