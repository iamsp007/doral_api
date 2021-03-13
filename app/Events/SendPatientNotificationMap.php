<?php

namespace App\Events;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendPatientNotificationMap
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data,$userid,$title='Clinician RoadL Route',$message='Clinician RoadL Route')
    {
        $user = User::find($userid);
        if ($user){
            $token=$user->device_token;
            $web_token=$user->web_token;
            $helper = new Helper();
            if ($token){
                $helper->sendNotification($token,$title,$message,$data,2);
            }
            if ($web_token && $isWeb===true){
                $link=env('WEB_URL').'clinician/running-roadl/'.$data['id'];
                $helper->sendWebNotification($web_token,$title,$message,$data,2,$link);
            }
        }
    }
}
