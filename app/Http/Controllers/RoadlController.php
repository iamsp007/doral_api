<?php

namespace App\Http\Controllers;

use App\Events\SendClinicianPatientRequestNotification;
use App\Http\Requests\RoadlInformationRequest;
use App\Http\Requests\RoadlInformationShowRequest;
use App\Models\AssignAppointmentRoadl;
use App\Models\PatientReferral;
use App\Models\PatientRequest;
use App\Models\RoadlInformation;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            if ($roadlInformation->status==="complete"){

                $patientRequest = PatientRequest::where('id','=',$request->patient_requests_id)->first();
                if ($patientRequest){
                    $patientRequest->status = 'complete';
                    $patientRequest->save();
                }

                $user = User::find($request->user_id);
                if ($user){
                    $user->is_available = 1;
                    $user->save();
                }
            }
            $user = User::find($request->user_id);
            $datas = PatientRequest::with(['detail','requestType'])
                ->where([['id','=',$request->patient_requests_id],['status','=','active']])
                ->first();
            $icon=env('WEB_URL').'assets/icon/'.'Clinician Request.png';
            $color='blue';
            if ($datas->requestType && $datas->requestType->referral){
                $icon=env('WEB_URL').'assets/icon/'.$datas->requestType->referral->icon;
                $color=$datas->requestType->referral->color;
            }
            $location=array(
                'referral_type'=>'LAB',
                'latitude'=>$request->latitude,
                'longitude'=>$request->longitude,
                'start_latitude'=>$datas->detail?$datas->detail->latitude:null,
                'end_longitude'=>$datas->detail?$datas->detail->longitude:null,
                'first_name'=>$datas->detail?$datas->detail->first_name:null,
                'last_name'=>$datas->detail?$datas->detail->last_name:null,
                'status'=>$request->has('status')?$request->input('status'):"start",
                'color'=>$color,
                'icon'=>$icon,
                'id'=>$request->patient_requests_id
            );
            $this->sendLocationEmit($location);
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

        $validator = \Illuminate\Support\Facades\Validator::make([
            'patient_request_id'=>$patient_request_id
        ],[
            'patient_request_id'=>'required|exists:patient_requests,id'
        ]);
        if ($validator->fails()){
            return $this->generateResponse(false,$validator->errors()->first(),$validator->errors()->messages(),200);
        }

        $roadlList = AssignAppointmentRoadl::where('patient_request_id','=',$patient_request_id)->first();
        $data=array();
        $data['type']=0;
        if ($roadlList){
            $data['type']=1;
            $locations=array();
            if (Auth::user()->hasRole('LAB')){
                $locations = AssignAppointmentRoadl::with(['requests','referral'])
                    ->where('appointment_id','=',$roadlList->appointment_id)
                    ->where('referral_type','=','LAB')
                    ->orderBy('id','desc')
                    ->get()->toArray();
            }elseif (Auth::user()->hasRole('X-RAY')){
                $locations = AssignAppointmentRoadl::with(['requests','referral'])
                    ->where('appointment_id','=',$roadlList->appointment_id)
                    ->where('referral_type','=','X-RAY')
                    ->orderBy('id','desc')
                    ->get()->toArray();
            }elseif (Auth::user()->hasRole('CHHA')){
                $locations = AssignAppointmentRoadl::with(['requests','referral'])
                    ->where('appointment_id','=',$roadlList->appointment_id)
                    ->where('referral_type','=','CHHA')
                    ->orderBy('id','desc')
                    ->get()->toArray();
            }elseif (Auth::user()->hasRole('Home Oxygen')){
                $locations = AssignAppointmentRoadl::with(['requests','referral'])
                    ->where('appointment_id','=',$roadlList->appointment_id)
                    ->where('referral_type','=','Home Oxygen')
                    ->orderBy('id','desc')
                    ->get()->toArray();
            }elseif (Auth::user()->hasRole('clinician') || Auth::user()->hasRole('patient')){
                $locations = AssignAppointmentRoadl::with(['requests','referral'])
                    ->where('appointment_id','=',$roadlList->appointment_id)
                    ->orderBy('id','desc')
                    ->get()->toArray();
            }

            $location=array();
            if (count($locations)>0){
                foreach ($locations as $value) {
                    $requests = $value['requests'];
                    $referral = $value['referral'];
                    $last_location = RoadlInformation::where('user_id','=',$value['requests']['clincial_id'])
                        ->where('patient_requests_id','=',$requests['id'])
                        ->orderBy('id','desc')
                        ->first();
                    $location[]=array(
                        'referral_type'=>$value['referral_type'],
                        'latitude'=>$last_location?$last_location->latitude:null,
                        'longitude'=>$last_location?$last_location->longitude:null,
                        'start_latitude'=>$requests['detail']?$requests['detail']['latitude']:null,
                        'end_longitude'=>$requests['detail']?$requests['detail']['longitude']:null,
                        'first_name'=>$requests['detail']?$requests['detail']['first_name']:null,
                        'last_name'=>$requests['detail']?$requests['detail']['last_name']:null,
                        'status'=>$requests['clincial_id']===null?'pending':($last_location?$last_location->status:$requests['status']),
                        'color'=>$referral?$referral['color']:'blue',
                        'icon'=>$referral?env('WEB_URL').'assets/icon/'.$referral['icon']:env('WEB_URL').'assets/icon/'.'Clinician Request.png',
                        'id'=>$requests['id'],
                        'user_id'=>$requests['clincial_id'],
                    );
                }
                $data['clinicians']=$location;
                $data['patient']=array(
                    'latitude'=>$requests['latitude'],
                    'longitude'=>$requests['longitude'],
                    'detail'=>$requests['patient'],
                );
                return $this->generateResponse(true,'Roadl Proccess Route List',$data,200);
            }
            return $this->generateResponse(false,'No Patient Request Found this user',null,200);
        }else{
            $datas = PatientRequest::with(['detail','routes','appointmentType'])
                ->where([['id','=',$patient_request_id],['status','=','active']])
                ->first();
            $last_location = RoadlInformation::where('user_id','=',$datas->clincial_id)->where('patient_requests_id','=',$patient_request_id)->orderBy('id','desc')->first();

            $location[]=array(
                'referral_type'=>'Doral',
                'latitude'=>$last_location?$last_location->latitude:null,
                'longitude'=>$last_location?$last_location->longitude:null,
                'start_latitude'=>$datas->detail?$datas->detail->latitude:null,
                'end_longitude'=>$datas->detail?$datas->detail->longitude:null,
                'first_name'=>$datas->detail?$datas->detail->first_name:null,
                'last_name'=>$datas->detail?$datas->detail->last_name:null,
                'status'=>$datas->clincial_id===null?'pending':($last_location?$last_location->status:$datas->status),
                'id'=>$datas->id,
                'color'=>'blue',
                'icon'=>env('WEB_URL').'assets/icon/'.'Clinician Request.png',
                'user_id'=>$datas->clincial_id,
            );
            $data['clinicians']=$location;
            $data['patient']=array(
                'latitude'=>$datas->latitude,
                'longitude'=>$datas->longitude,
                'detail'=>$datas->patient,
            );

            return $this->generateResponse(true,'Roadl Proccess Route List',$data,200);
        }

        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }

}
