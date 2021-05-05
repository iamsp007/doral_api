<?php

namespace App\Http\Controllers;

use App\Events\sendAddressUpdateNotification;
use App\Events\SendAppointNotification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $clinicianDetail = User::where('id',$request->user_id)->first();
        if($clinicianDetail) { 
            event(new sendAddressUpdateNotification($clinicianDetail,$clinicianDetail));
            return $this->generateResponse(true,'Notification send Successfully!',null,200);
        }
        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }
}
