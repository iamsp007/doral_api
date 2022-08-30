<?php

namespace App\Events;

use App\Helpers\Helper;
use App\Models\PatientRequest;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
    public function __construct($data,$clinicianList, $arrived = '')
    {
        $first_name = ($data->patient && $data->patient->first_name) ? $data->patient->first_name : '';
        $last_name = ($data->patient && $data->patient->last_name) ? $data->patient->last_name : '';

        $address = $message = '';
        if($data->patient && $data->patient->demographic) {
            $addressData = $data->patient->demographic->address;

            if ($addressData['address1']){
                $address.= $addressData['address1'];
            }
            if ($addressData['city']){
                $address.=', '.$addressData['city'];
            }
            if ($addressData['state']){
                $address.=', '.$addressData['state'];
            }
            if ($addressData['zip_code']){
                $address.=', '.$addressData['zip_code'];
            }
            if ($address){
                $message="The roadL request came from this address: " . $address;
            }

            if ($arrived === '3') {
                $patientmessage = "RoadL request arrive";
                $patienttitle="RoadL request arrive";
                $patientToken=$data->patient->device_token;

                $helper = new Helper();
                if ($patientToken){
                    $helper->sendNotification($patientToken,$patienttitle,$patientmessage,$data,1);
                }
            }
        }

        foreach ($clinicianList as $item) {
            $message = $message;
            $title="RoadL request of " . $first_name . ' ' . $last_name;

            $token=$item->device_token;
            $web_token=$item->web_token;
            $helper = new Helper();
            if ($token){
                $helper->sendNotification($token,$title,$message,$data,1);
            }
            if ($web_token){
                $link=env('WEB_URL').'clinician/start-roadl/'.$data->id;
                $helper->sendWebNotification($web_token,$title,$message,$data,1,$link);
            }
        }
    }
}

