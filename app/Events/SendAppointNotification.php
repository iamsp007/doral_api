<?php

namespace App\Events;

use App\Helpers\Helper;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendAppointNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct($data,$clinicianList,$title="Your Appointment is booked by patient",$message="Your Appointment is booked by patient")
    {
        foreach ($clinicianList as $item) {
            $token=$item->device_token;
            $web_token=$item->web_token;
            $helper = new Helper();
            if ($token){
                $helper->sendNotification($token,$title,$message,$data,4);
            }
            if ($web_token){
                $link=env('WEB_URL').'clinician/scheduled-appointment';
                $helper->sendWebNotification($web_token,$title,$message,$data,4,$link);
            }
        }
    }
}
