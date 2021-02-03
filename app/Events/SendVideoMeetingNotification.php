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

class SendVideoMeetingNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId,$data)
    {
        $user = User::find($userId);
        \Log::info($user);
        if ($user){
            $message="Start Your Video Meeting";
            $title="Start Your Video Meeting";
            $token=$user->device_token;
            $web_token=$user->web_token;
            $helper = new Helper();
            if ($token){
                $helper->sendNotification($token,$title,$data,2);
            }
            if ($web_token){
                $helper->sendWebNotification($web_token,$title,$data,2);
            }
        }
    }
}
