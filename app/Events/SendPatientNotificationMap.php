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
    public function __construct($data,$userid)
    {
        $clinician_name = '';
        if(isset($data->clinician) && !empty($data->clinician)) {
            $first_name = ($data->clinician->first_name) ? $data->clinician->first_name : '';
            $last_name = ($data->clinician->last_name) ? $data->clinician->last_name : '';
            $clinician_name = $first_name . ' ' . $last_name;
        }
        $title = 'Your Roadl Request accepted by: ' . $clinician_name;
        $message = 'Distance between you and clinician is: '. $data->distance . '.It will take ' . $data->travel_time . ' for the clinic to come to you';
        $user = User::find($userid);
        if ($user){
            $token=$user->device_token;
            $web_token=$user->web_token;
            $helper = new Helper();
            if ($token){
                $helper->sendNotification($token,$title,$message,$data,2);
            }
            if ($web_token){
                $link=env('WEB_URL').'clinician/running-roadl/'.$data['id'];
                $helper->sendWebNotification($web_token,$title,$message,$data,2,$link);
            }
        }
    }
}
