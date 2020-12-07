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
            $this->sendPushNotification($token,$title,$message,$data);
        }
    }

    private function sendPushNotification( $token,$title,$message,$data ) {

        // Set POST variables

        $path_to_fcm='https://fcm.googleapis.com/fcm/send';
        $server_key=env('FIREBASE_CREDENTIALS');
        $key= $token;
        $headers=array(
            'Authorization:key='.$server_key,
            'Content-Type:application/json'
        );
//        $fields=array(
//            'message'=>array(
//                'token'=>$key,
//                'data'=>$data
//            )
//        );
        $fields=array(
            'to'=>$key,
            'notification'=>array(
                'title'=>$title,
                'body'=>$message,
            ),
            'data'=>$data
        );

        $payload=json_encode($fields);
        $curl_session=curl_init();
        curl_setopt($curl_session,CURLOPT_URL,$path_to_fcm);
        curl_setopt($curl_session,CURLOPT_POST,true);
        curl_setopt($curl_session,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl_session,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl_session,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl_session,CURLOPT_IPRESOLVE,CURL_IPRESOLVE_V4);
        curl_setopt($curl_session,CURLOPT_POSTFIELDS,$payload);

        $result=curl_exec($curl_session);
        \Log::info($result);
        curl_close($curl_session);
    }
}
