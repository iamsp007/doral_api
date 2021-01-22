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
        if ($user){
            $message="Start Your Video Meeting";
            $title="Start Your Video Meeting";
            $token=$user->device_token;
            if ($token){
                $helper = new Helper();
                $helper->sendNotification($token,$title,$data);
            }
        }
    }
}
