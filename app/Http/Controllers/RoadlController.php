<?php

namespace App\Http\Controllers;

use App\Events\SendClinicianPatientRequestNotification;
use App\Http\Requests\RoadlInformationRequest;
use App\Http\Requests\RoadlInformationShowRequest;
use App\Models\PatientReferral;
use App\Models\PatientRequest;
use App\Models\RoadlInformation;
use App\Models\User;
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

    public function getNearByClinicianList(Request $request,$patient_request_id){
        try {
            $patient_requests = PatientRequest::where([['id','=',$patient_request_id],['status','=','active']])->first();

            if ($patient_requests->clincial_id!==null){
                return $this->generateResponse(false,'Request Already Accepted',[],200);
            }

            $clinicianIds = $this->findNearestClinician($patient_requests->latitude,$patient_requests->longitude);

            $markers = collect($clinicianIds)->map(function($item) use ($patient_requests){
                $item['distance'] = $this->calculateDistanceBetweenTwoAddresses($item->latitude, $item->longitude, $patient_requests->latitude,$patient_requests->longitude);
                return $item;
            })
                // ->where('distance','<=',20)
                ->pluck('id');
            $clinicianList = User::whereIn('id',$markers)->get();
            $data=PatientRequest::with('detail')
                ->where('id','=',$patient_request_id)
                ->first();
            event(new SendClinicianPatientRequestNotification($data,$clinicianList));
            return $this->generateResponse(true,'Get Near Me Patient Request List',array('clinicianList'=>$clinicianList,'patientDetail'=>$data),200);
        }catch (\Exception $exception){
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }

    public function findNearestClinician($latitude, $longitude, $radius = 400)
    {
        /*
         * using eloquent approach, make sure to replace the "Restaurant" with your actual model name
         * replace 6371000 with 6371 for kilometer and 3956 for miles
         */
        $clinicians = User::where('is_available', '=', 1)
            ->whereHas('roles',function ($q){
                $q->where('name','=','clinician');
            })
            ->get();

        return $clinicians;
    }

    public function calculateDistanceBetweenTwoAddresses($lat1, $lng1, $lat2, $lng2){
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);

        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $delta_lat = $lat2 - $lat1;
        $delta_lng = $lng2 - $lng1;

        $hav_lat = (sin($delta_lat / 2))**2;
        $hav_lng = (sin($delta_lng / 2))**2;

        $distance = 2 * asin(sqrt($hav_lat + cos($lat1) * cos($lat2) * $hav_lng));

        $distance = 6371*$distance;
        // If you want calculate the distance in miles instead of kilometers, replace 6371 with 3959.

        return $distance;
    }

    public function getRoadLProccess(Request $request,$patient_request_id){

        $data = PatientRequest::with('routes')
            ->where([['id','=',$patient_request_id],['status','=','active']])
            ->first();
        if ($data){
            if (count($data->routes)>0){
                $data->destination = array(
                    'latitude'=>$data->routes[count($data->routes)-1]->latitude,
                    'longitude'=>$data->routes[count($data->routes)-1]->longitude);
            }else{
                $data->destination = array(
                    'latitude'=>$data->latitude,
                    'longitude'=>$data->longitude);
            }
            return $this->generateResponse(true,'Roadl Proccess Route List',$data,200);
        }

        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }

}
