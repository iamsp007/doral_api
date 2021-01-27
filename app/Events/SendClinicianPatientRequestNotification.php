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
            $message="Patient RoadL Request ";
            $title="Patient RoadL Request ";
            $token=$item->device_token;
            $helper = new Helper();
            $helper->sendNotification($token,$title,$data);
        }
    }
}
