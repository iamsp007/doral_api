<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Nexmo\Laravel\Facade\Nexmo;

class SendingSMS
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        foreach ($message as $item) {
            \Log::info($item['to']);
            $this->smsSend($item['to'],$item['message']);
        }
//        $user = User::find($userid);
//        if ($user){
////            $message='Doral Health Connect | Your patient '.$user->first_name.'  is slightly higher than regular. '.env("APP_URL").'/caregiver/'.$type;;
//            if ($user->phone!==null){
//                $this->smsSend($user->phone,$message);
//            }
//            $this->smsSend($user->phone,$message);
//        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function smsSend($to,$text)
    {
        if (env('APP_ENV')==="local"){
            $to=env('SMS_TO');
        }
        try {
            Nexmo::message()->send([
                'to'   =>'+1'.$to,
                'from' => env('SMS_FROM'),
                'text' => $text
            ]);
        }catch (\Exception $exception){
            \Log::info($exception);
        }
    }
}
