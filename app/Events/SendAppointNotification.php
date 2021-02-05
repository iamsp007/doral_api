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

    public function __construct($data,$clinicianList)
    {
        foreach ($clinicianList as $item) {
            $message="Your Appointment is booked by patient";
            $title="Your Appointment is booked by patient";
            $token=$item->device_token;
            $web_token=$item->web_token;
            $helper = new Helper();
            if ($token){
                $helper->sendNotification($token,$title,$data,1);
            }
            if ($web_token){
                $helper->sendWebNotification($web_token,$title,$data,1);
            }
        }
    }
}
