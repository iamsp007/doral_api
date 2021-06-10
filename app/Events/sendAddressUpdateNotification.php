<?php

namespace App\Events;

use App\Helpers\Helper;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class sendAddressUpdateNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct($clinicianDetail,$clinicianData,$title="Add Your Current Address",$message="Doral - Clinician application need to know your current address. Please click this notification or login and add your address.")
    {
        $token = $clinicianData->device_token;
    
        $helper = new Helper();
        if ($token){
            $helper->sendNotification($token, $title, $message, $clinicianDetail, 4);
        }
    }
}
