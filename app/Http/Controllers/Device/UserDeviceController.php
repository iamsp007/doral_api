<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SmsController;
use App\Models\ApiKey;
use App\Models\UserDevice;
use App\Models\UserDeviceLog;
use Illuminate\Http\Request;

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
               
                if($userDevice) {
                    if ($userDevice->demographic != '') {
                        $userDeviceLog = new UserDeviceLog();

                        $userDeviceLog->user_device_id = $userDevice->id;
                        $userDeviceLog->value = $input['value'];
                        $userDeviceLog->reading_time = $input['datetime'];

                        $readingLevel = 1;
                        $level_message = '';
                        if ($input['device_type'] == 1) {
                            $readingLevel = 1;
                            $explodeValue = explode("/",$input['value']);
                            if($explodeValue[0] <= 140) {
                                $readingLevel = 3;
                                $level_message = 'blood pressure is higher';
                            } else if($explodeValue[0] >= 100) {
                                $readingLevel = 3;
                                $level_message = 'blood pressure is lower';
                            }
                        } else if ($input['device_type'] == 2) {
                            $readingLevel = 1;
                            $explodeValue = explode("/",$input['value']);
                            if($explodeValue[0] <= 300) {
                                $readingLevel = 3;
                                $level_message = 'blood sugar is higher';
                            } else if($explodeValue[0] >= 60) {
                                $readingLevel = 3;
                                $level_message = 'blood sugar is lower';
                            }
                        } 

                        $userDeviceLog->level = $readingLevel;

                        $userDeviceLog->save();

                        $patient_name = $userDevice->user->first_name . ' ' . $userDevice->user->last_name;
                        
                        $message = 'Doral Health Connect | Your patient ' . $patient_name . ' ' . $level_message . ' than regular. Need immediate attention. http://app.doralhealthconnect.com/ccm/'.$userDeviceLog->id;
                        
                        $smsController = new SmsController();
                        $smsController->sendsmsToMe($message, env('SEND_SMS'));

                        return $this->generateResponse(true,'User device log added successfully.',$userDeviceLog,200);
                    } else {
                        return $this->generateResponse(true,'Unauthenticated',null,401);
                    }
                } else {
                    return $this->generateResponse(true,'User device not found.',null,200);
                }
            } else {
                return $this->generateResponse(true,'Unauthenticated',null,401);
            }
        }catch (\Exception $exception){
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }
}
