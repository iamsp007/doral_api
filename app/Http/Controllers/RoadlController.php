<?php

namespace App\Http\Controllers;

use App\Events\SendClinicianPatientRequestNotification;
use App\Events\SendingSMS;
use App\Events\SendPatientNotificationMap;
use App\Helpers\Helper;
use App\Http\Requests\PatientRequestOtpVerifyRequest;
use App\Http\Requests\RoadlInformationRequest;
use App\Http\Requests\RoadlInformationShowRequest;
use App\Jobs\SendEmailJob;
use App\Mail\UpdateStatusNotification;
use App\Models\AssignAppointmentRoadl;
use App\Models\PatientReferral;
use App\Models\PatientRequest;
use App\Models\RoadlInformation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RoadlController extends Controller
{
    //
    public function create(RoadlInformationRequest $request){
        if(isset($request->patient_request_id)) {
            $patientRequest = PatientRequest::find($request->patient_request_id);
        }else {
            $patientRequest = PatientRequest::find($request->patient_requests_id); 
        }
        if ($patientRequest){
            if ($request->status==="4" || $request->status===4){
                $patientRequest->status = $request->status;
                $patientRequest->complated_time = Carbon::now()->toDateTime();
                $patientRequest->notes = $request->notes;
                //                $patientRequest->otp=rand(1000,9999);
                $patientRequest->save();
                $allPatientRequest = PatientRequest::where('parent_id','=',$patientRequest->parent_id)->get();
                $collection = collect($allPatientRequest)->whereIn('status',['4','5'])->count();
                if ($collection===count($allPatientRequest)){
                    $patientRequestParent = PatientRequest::find($patientRequest->parent_id);
                    $patientRequestParent->status = '4';
                    $patientRequestParent->save();
                }
            }elseif ($request->status==="3"){
                $patientRequest->status = $request->status;
                $patientRequest->arrived_time = Carbon::now()->toDateTime();
                $patientRequest->save();

            }elseif ($request->status==="5"){
                $patientRequest->status = $request->status;
                $patientRequest->cancelled_time = Carbon::now()->toDateTime();
                $patientRequest->notes = $request->notes;
                $patientRequest->save();
            }elseif ($request->status==="6"){
                $patientRequest->prepare_time = $request->has('prepare_time')?$request->prepare_time:5;
            }
            // update is available field in user table start
           
            $user = User::where('id',$patientRequest->clincial_id)->first();
         
            if ($user){
                $user->latitude = $request->latitude;
                $user->longitude = $request->longitude;
                if ($request->status==='4' || $request->status==='5'){
                    $user->is_available = '1';
                    
                    $patientRequestdata = PatientRequest::where([['parent_id', $patientRequest->parent_id],['status', '!=', 4],['status', '!=', 5]])->get();
		     if(count($patientRequestdata) == 0) {
			PatientRequest::find($patientRequest->parent_id)->update([
				'status' => '4'
			]);
		     }
                }
                $user->save();
            }
            $roadlInformation = new RoadlInformation();
            $roadlInformation->user_id = $patientRequest->clincial_id;
            $roadlInformation->patient_requests_id = $patientRequest->id;
            $roadlInformation->client_id = $patientRequest->user_id;
            $roadlInformation->latitude = $request->latitude;
            $roadlInformation->longitude = $request->longitude;
            $roadlInformation->status = "start";
            $roadlInformation->is_status = $request->status;
            $roadlInformation->save();
            // update is available field in user table end
            
            $smsController = new SmsController();
            $smsController->sendSms($patientRequest,$request->status);
           
            return $this->generateResponse(true,'Your RoadL Visit Updated Successfully!',$patientRequest,200);
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
            $patient_requests = PatientRequest::where([['id','=',$patient_request_id],['status','=','1']])->first();

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

    public function calculateDistanceBetweenTwoAddresses($lat1, $lon1, $lat2, $lon2, $unit="K"){
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
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
            $data['roadl_id']=$roadlList->appointment_id;
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
                ->where([['id','=',$patient_request_id],['status','=','1']])
                ->first();
            $last_location = RoadlInformation::where('user_id','=',$datas->clincial_id)->where('patient_requests_id','=',$patient_request_id)->orderBy('id','desc')->first();
            $data['roadl_id']=$datas->id;
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

    public function getRoadLProccessNew(Request $request){

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(),[
            'parent_id'=>'required|exists:patient_requests,parent_id'
        ]);
        if ($validator->fails()){
            return $this->generateResponse(false,$validator->errors()->first(),$validator->errors()->messages(),200);
        }

        $patientRequest = PatientRequest::with(['detail','patient','requestType','roadlInformation' => function($q){
                $q->orderBy('id','DESC');
            }])
            ->where(function ($q) use ($request){
                if ($request->has('type_id')){
                    $q->where('type_id','=',$request->type_id);
                }
            })
            ->where('parent_id','=',$request->parent_id)
            ->whereNotNull('parent_id')
            ->get();

        if (count($patientRequest)>0){
            $arr = [];

            $clinicians = $patientRequest->map(function ( $lookup ) {
                $latitude = $longitude = '';
                if ($lookup->status === '1') {
                    $latitude = isset($lookup->patient->latitude) ? $lookup->patient->latitude : null;
                    $latitude = isset($lookup->patient->latitude) ? $lookup->patient->latitude : null;
                } else {
                    $roadlInfo = RoadlInformation::where([['patient_requests_id', '=', $lookup->id],['is_status','=',$lookup->status]])->first();
                    if($roadlInfo) {
                        $latitude = $roadlInfo->latitude;
                        $longitude = $roadlInfo->longitude;
                    }
                        
                }
              
                return [
                    'id' => isset($lookup->id) ? $lookup->id : null,
                    'user_id' => isset($lookup->user_id) ? $lookup->user_id : null,
                    'clincial_id' => isset($lookup->clincial_id) ? $lookup->clincial_id : null,
                    'preparation_time' => isset($lookup->preparation_time) ? $lookup->preparation_time : null,
                    'preparasion_date' => isset($lookup->preparasion_date) ? $lookup->preparasion_date : null,
                    'accepted_time' => isset($lookup->accepted_time) ? $lookup->accepted_time : null,
                    'travel_time' => isset($lookup->travel_time) ? $lookup->travel_time : null,
                    'parent_id' => isset($lookup->parent_id) ? $lookup->parent_id : 0,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'latitude' =>  isset($lookup->detail->latitude) ? $lookup->detail->latitude : null,
                    'longitude' => isset($lookup->detail->longitude) ? $lookup->detail->longitude : null,
                    'first_name' => isset($lookup->detail->first_name) ? $lookup->detail->first_name : null,
                    'last_name' => isset($lookup->detail->last_name) ? $lookup->detail->last_name : null,
                    'status' => isset($lookup->status) ? $lookup->status : null,
                    'referral_type' => isset($lookup->requestType->name) ? $lookup->requestType->name : null,
                    'icon' => isset($lookup->requestType->icon) ? $lookup->requestType->icon : '',
                    'color' => isset($lookup->requestType->color) ? $lookup->requestType->color : '#0000FF',

                ];
            });

            $patient = $patientRequest->map(function ( $lookup ) {
                
                return [
                    'id' => isset($lookup->patient->id) ? $lookup->patient->id : null,
                    'latitude' => $lookup->latitude,
                    'longitude' => $lookup->longitude,
                    'first_name' => isset($lookup->patient->first_name) ? $lookup->patient->first_name : null,
                    'last_name' => isset($lookup->patient->last_name) ? $lookup->patient->last_name : null,
                ];
            });

            $arr = [
                'clinicians' => $clinicians,
                'patient' => $patient[0],
            ];

            return $this->generateResponse(true, 'roadl request list', $arr, 200);
        }

        return $this->generateResponse(false,'No Request Found',null,200);
    }

    public function patientRequestOtpVerify(PatientRequestOtpVerifyRequest $request){
        $patientRequest = PatientRequest::find($request->id);
        $patientRequest->status='4';
        $patientRequest->save();
        return $this->generateResponse(true,'Your Reuest is done',$patientRequest);
    }
}