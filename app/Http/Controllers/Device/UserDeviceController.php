<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use App\Models\UserDeviceLog;
use Illuminate\Http\Request;

class UserDeviceController extends Controller
{
    public function addDevice(Request $request)
    {
        try {
            $input = $request->all();
          
            $userDevice = UserDevice::where([['device_id', '=', $input['account']],['device_type', '=', $input['device_type']]])->first();
            if($userDevice) {
                $userDeviceLog = new UserDeviceLog();

                $userDeviceLog->user_device_id = $userDevice->id;
                $userDeviceLog->value = $input['value'];
                $userDeviceLog->reading_time = $input['datetime'];

                $userDeviceLog->save();
                return $this->generateResponse(true,'User device log added successfully.',$userDeviceLog,200);
            } else {
                return $this->generateResponse(true,'User device not found.',null,200);
            }

           
        }catch (\Exception $exception){
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }
}
