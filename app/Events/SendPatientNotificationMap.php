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
        $user = User::find($userid);
        if ($user){
            $message="Clinician RoadL Route";
            $title="Clinician RoadL Route";
            $token=$user->device_token;
            $helper = new Helper();
            $helper->sendNotification($token,$title,$data);
        }
    }
}
