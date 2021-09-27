<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
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
//                $userDevice = UserDevice::with(['demographic' => function ($q) use($apiKey) {
//                    $q->where('company_id', $apiKey->company_id);
//                }])->where([['id', '=', $input['user_id']],['device_type', '=', $input['device_type']]])->first();
//               
//                if($userDevice) {
//                    if ($userDevice->demographic != '') {
                        $userDeviceLog = new UserDeviceLog();
                        $userDeviceLog->user_id = $input['user_id'];
                        $userDeviceLog->device_type = $input['device_type'];
                        $userDeviceLog->value = $input['value'];
                        $userDeviceLog->reading_time = $input['datetime'];
                        $userDeviceLog->save();
                        return $this->generateResponse(true,'User device log added successfully.',$userDeviceLog,200);
//                    } else {
//                        return $this->generateResponse(true,'Unauthenticated',null,401);
//                    }
//                } else {
//                    return $this->generateResponse(true,'User device not found.',null,200);
//                }
            } else {
                return $this->generateResponse(true,'Unauthenticated',null,401);
            }
        }catch (\Exception $exception){
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }
}
