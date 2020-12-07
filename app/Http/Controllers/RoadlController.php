<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoadlInformationRequest;
use App\Http\Requests\RoadlInformationShowRequest;
use App\Models\PatientReferral;
use App\Models\PatientRequest;
use App\Models\RoadlInformation;
use Illuminate\Http\Request;

class RoadlController extends Controller
{
    //
    public function create(RoadlInformationRequest $request){
        $roadlInformation = new RoadlInformation();
        $roadlInformation->user_id = $request->user_id;
        $roadlInformation->patient_requests_id = $request->patient_requests_id;
        $roadlInformation->client_id = $request->client_id;
        $roadlInformation->latitude = $request->latitude;
        $roadlInformation->longitude = $request->longitude;
        $roadlInformation->status = $request->has('status')?$request->input('status'):"start";
        if ($roadlInformation->save()){
            return $this->generateResponse(true,'Adding RoadlInformation Successfully!',null,200);
        }
        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }

    public function show(RoadlInformationShowRequest $request){
        $roadlInformation = RoadlInformation::where(['patient_requests_id'=>$request->patient_requests_id])
            ->orderBy('id','desc')
            ->get();
        if (count($roadlInformation)>0){
            return $this->generateResponse(true,'Roadl Infomation Get Successfully!',$roadlInformation,200);
        }
        return $this->generateResponse(false,'No Any Roadl Information Exists',[],200);
    }
}
