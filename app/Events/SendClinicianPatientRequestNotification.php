<?php

namespace App\Events;

use App\Helpers\Helper;
use App\Models\PatientRequest;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendClinicianPatientRequestNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\User  $order
     * @return void
     */
    public function __construct($data,$clinicianList)
    {
        foreach ($clinicianList as $item) {
            $first_name = ($item->first_name) ? $item->first_name : '';
            $last_name = ($item->first_name) ? $item->first_name : '';
            $address='';
            if($item->demographic && $item->demographic->address) {
                
                $addressData = $item->demographic->address;
                if ($addressData['address1']){
                    $address.= $addressData['address1'] . ',';
                }
                if ($addressData['city']){
                    $address.=', '.$addressData['city'] . ',';
                }
                if ($addressData['state']){
                    $address.=', '.$addressData['state'] . ',';
                }
                if ($addressData['zip_code']){
                    $address.=', '.$addressData['zip_code'] . ',';
                }
                $message = '';
                if ($address){
                    $message="The road request came from this address " . $address;
                }
            }
           
            $title="You have been requested by " . $first_name . ' ' . $last_name;
            $token=$item->device_token;
            $web_token=$item->web_token;
            $helper = new Helper();
            if ($token){
                $helper->sendNotification($token,$title,$message,$data,1);
            }
            if ($web_token){
                $link=env('WEB_URL').'clinician/start-roadl/'.$data->id;
                \Log::info($link);
                $helper->sendWebNotification($web_token,$title,$message,$data,1,$link);
            }
        }
    }
}
