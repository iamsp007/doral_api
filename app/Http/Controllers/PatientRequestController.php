<?php

namespace App\Http\Controllers;

use App\Events\SendClinicianPatientRequestNotification;
use App\Events\SendingSMS;
use App\Events\SendPatientNotificationMap;
use App\Http\Requests\CCMReadingRequest;
use App\Http\Requests\ClinicianRequestAcceptRequest;
use App\Models\AssignAppointmentRoadl;
use App\Models\CCMReading;
use App\Models\Referral;
use App\Models\RoadlInformation;
use App\Models\User;
use App\Models\PatientRequest;
use App\Http\Requests\PatientRequest as PatientRequestValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PatientRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRoadLStatus()
    {
        $patientroadl = PatientRequest::
        	where('user_id', Auth::user()->id)
        	->whereDate('created_at', Carbon::today())
        	->where('status','active')
            ->orderBy('id','desc')
            ->first();
        return $this->generateResponse(true,'Patient Request Status',$patientroadl,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PatientRequestValidation $request)
    {
        try {
            if ($request->has('type')){

                foreach ($request->type as $value) {
                    $response = $this->createPatientRequest($request,$value);
                }
            }else{
                $response = $this->createPatientRequest($request);
            }
            return $response;
        }catch (Exception $exception){
            return $this->generateResponse(false,$exception->getMessage());
        }
    }

    public function createPatientRequest(Request $request,$type='patient'){
        $patient = new PatientRequest();
        $patient->user_id = $request->user_id;
        $patient->latitude = $request->latitude;
        $patient->longitude = $request->longitude;
        $patient->reason = $request->reason;
        if($request->has('dieses')){
            $patient->dieses=$request->dieses;
        }
        if($request->has('symptoms')){
            $patient->symptoms=$request->symptoms;
        }
        if($request->has('is_parking')){
            $patient->is_parking=$request->is_parking;
        }
        $patient->status='active';
        if ($patient->save()){

            if ($type!=='patient'){
                $assignAppointemntRoadl = AssignAppointmentRoadl::where([
                    'appointment_id'=>$request->appointment_id,
                    'patient_request_id'=>$patient->id,
                    'referral_type'=>$type
                ])->first();
                if ($assignAppointemntRoadl===null){
                    $assignAppointemntRoadl = new AssignAppointmentRoadl();
                }
                $assignAppointemntRoadl->appointment_id = $request->appointment_id;
                $assignAppointemntRoadl->patient_request_id = $patient->id;
                $assignAppointemntRoadl->referral_type = $type;
                $assignAppointemntRoadl->save();

                $clinicianList = User::whereHas('roles',function ($q) use ($request,$type){
                    $q->where('name','=',$type);
                })->where('is_available','=','1')->get();

                $data=PatientRequest::with('detail')
                    ->where('id','=',$patient->id)
                    ->first();
                event(new SendClinicianPatientRequestNotification($data,$clinicianList));
            }else{
                $clinicianList = User::whereHas('roles',function ($q){
                    $q->where('name','=','clinician');
                })->where('is_available','=','1')->get();
                $data=PatientRequest::with('detail')
                    ->where('id','=',$patient->id)
                    ->first();
                event(new SendClinicianPatientRequestNotification($data,$clinicianList));
            }
            return $this->generateResponse(true,'Add Request Successfully!');
        }
        return $this->generateResponse(false,'Something Went Wrong!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PatientRequest  $patientRequest
     * @return \Illuminate\Http\Response
     */
    public function show(PatientRequest $patientRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PatientRequest  $patientRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(PatientRequest $patientRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientRequest  $patientRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PatientRequest $patientRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientRequest  $patientRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientRequest $patientRequest)
    {
        //
    }

    public function ccmReading(CCMReadingRequest $request)
    {
        try {
            $ccmReadingModel = new CCMReading();
            $ccmReadingModel->user_id = $request->user_id;
            $ccmReadingModel->reading_type = $request->reading_type;
            $ccmReadingModel->reading_value = $request->reading_value;
            $userDetails = User::getUserDetails($request->user_id);

            if ($request->reading_type == 0) {

                $readingLevel = 1;
                $explodeValue = explode("/",$request->reading_value);
                if($explodeValue[0] >= 130 && $explodeValue[0] <= 139) {
                    $readingLevel = 2;
                } else if($explodeValue[0] >= 140) {
                    $readingLevel = 3;
                }
                $ccmReadingModel->reading_level = $readingLevel;

            } else if ($request->reading_type == 1) {

                if($request->reading_value > 250) {
                    $readingLevel = 4;
                } else if($request->readingLevel < 60) {
                    $readingLevel = 3;
                }
                $ccmReadingModel->reading_level = $readingLevel;

            } else if ($request->reading_type == 2) {

                if ($request->reading_value > 110) {
                    $readingLevel = 1;
                }
                $ccmReadingModel->reading_level = $readingLevel;
            }

            if ($request->reading_type == 0) {

                $messages = array();

                if ($readingLevel == 3){
                    $messages[] =array(
                        'to'=>env('SMS_TO'),
                        'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' blood pressure is higher than regular. Need immediate attention. http://app.doralhealthconnect.com/caregiver/'.$readingLevel
                    );

                } else {
                    $messages[] =array(
                        'to'=>env('SMS_TO'),
                        'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' blood pressure is slightly higher than regular. https://app.doralhealthconnect.com/caregiver/'.$readingLevel
                    );
                }
                // event(new SendingSMS($messages));

            } elseif ($request->reading_type == 1) {

                $messages = array();

                if ($readingLevel == 3) {
                    $messages[] =array(
                        'to'=>env('SMS_TO'),
                        'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' blood sugar is higher than regular. Need immediate attention. http://app.doralhealthconnect.com/caregiver/'.$readingLevel
                    );
                } else {
                    $messages[] =array(
                        'to'=>env('SMS_TO'),
                        'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' blood sugar is slightly higher than regular. http://app.doralhealthconnect.com/caregiver/'.$readingLevel
                    );
                }
                event(new SendingSMS($messages));

            } elseif ($request->reading_type == 2) {

                $messages = array();

                if ($readingLevel == 3) {
                    $messages[] =array(
                        'to'=>env('SMS_TO'),
                        'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' pulse oximetry is higher than regular. Need immediate attention. http://app.doralhealthconnect.com/caregiver/'.$readingLevel);

                } else {
                    $messages[] =array(
                        'to'=>env('SMS_TO'),
                        'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' pulse oximetry is slightly higher than regular. http://app.doralhealthconnect.com/caregiver/'.$readingLevel
                    );
                }
                event(new SendingSMS($messages));

            }

            if ($ccmReadingModel->save()) {

                return $this->generateResponse(true,'CCM Reading Success!',$ccmReadingModel);
            }
            return $this->generateResponse(false,'Something Went Wrong!');
        } catch (\Exception $ex) {
            return $this->generateResponse(false,$ex->getMessage());
        }
    }

    public function clinicianRequestAccept(ClinicianRequestAcceptRequest $request){
        $patient = \App\Models\PatientRequest::find($request->request_id);
        if ($patient){
            if(null!==$patient->clincial_id){
                return $this->generateResponse(false,'Request Already Accepted!',null,200);
            }
            $patient->clincial_id=$request->user_id;
            if ($patient->save()){
                $users = User::find($request->user_id);
                $users->is_available = 2;
                $users->latitude = $request->latitude;
                $users->longitude = $request->longitude;
                $users->save();

                $roadlInformation = new RoadlInformation();
                $roadlInformation->user_id = $request->user_id;
                $roadlInformation->patient_requests_id = $patient->id;
                $roadlInformation->client_id = $patient->user_id;
                $roadlInformation->latitude = $request->latitude;
                $roadlInformation->longitude = $request->longitude;
                $roadlInformation->status = "start";
                $roadlInformation->save();

                $assignAppointemntRoadl = AssignAppointmentRoadl::where([
                    'patient_request_id'=>$patient->id
                ])->first();
                if ($assignAppointemntRoadl){
                    $patient->clinician = AssignAppointmentRoadl::where([
                        'appointment_id'=>$assignAppointemntRoadl->appointment_id
                    ])->with('requests',function ($q){
                          $q->select('id','clincial_id','latitude','longitude','reason','is_active','dieses','symptoms','is_parking','status');
                        })
                        ->select('appointment_id','patient_request_id','referral_type')
                        ->get()->toArray();
                    $patient->type = 1;
                }else{
                    $patient->clinician = $users;
                    $patient->type = 0;
                }

                $clinician=User::where(['id'=>$request->user_id])
                    ->first();
//                $data=array(
//                    'latitude'=>$request->latitude,
//                    'longitude'=>$request->longitude,
//                    'patient_request_id'=>$request->request_id,
//                    'clinician'=>$clinician
//                );
                event(new SendPatientNotificationMap($patient->toArray(),$patient->user_id));
                event(new SendPatientNotificationMap($patient->toArray(),$patient->clincial_id));

                $data=PatientRequest::with('detail')
                    ->where('id','=',$request->request_id)
                    ->first();
                return $this->generateResponse(true,'Request Accepted!',$data,200);
            }
        }

        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }

    public function clinicianPatientRequestList(Request $request){

        $referral = Referral::where('guard_name','=','partner')
            ->whereIn('name',Auth::user()->roles->pluck('name'))
            ->pluck('name');
        if (count($referral)>0){
            $patientRequestList = PatientRequest::with(['appointmentType','detail','patient','patientDetail','ccrm'])
                ->whereHas('appointmentType',function ($q) use ($referral){
                    $q->whereIn('referral_type',$referral);
                })
                ->where(function ($q) use ($request){
                    if ($request->has('type') && $request->type==='pending'){
                        $q->whereNull('clincial_id');
                    }elseif ($request->has('type') && $request->type==='running'){
                        $q->whereNotNull('clincial_id')->where('status','!=','complete');
                    }elseif ($request->has('type') && $request->type==='complete'){
                        $q->where('status','=','complete');
                    }elseif ($request->has('type') && $request->type==='latest'){
                        $q->where('created_at', '>',
                            Carbon::now()->subHours(1)->toDateTimeString()
                        );
                    }
                })
                ->orderBy('id','desc')
                ->get();

        }elseif (Auth::user()->hasRole('patient')){
            $patientRequestList = PatientRequest::with(['appointmentType','detail','patient','patientDetail','ccrm'])
                ->where(function ($q) use ($request){
                    if ($request->has('type') && $request->type==='pending'){
                        $q->whereNull('clincial_id');
                    }elseif ($request->has('type') && $request->type==='running'){
                        $q->whereNotNull('clincial_id')->where('status','!=','complete');
                    }elseif ($request->has('type') && $request->type==='complete'){
                        $q->where('status','=','complete');
                    }elseif ($request->has('type') && $request->type==='latest'){
                        $q->where('created_at', '>',
                            Carbon::now()->subHours(3)->toDateTimeString()
                        );
                    }
                })
                ->where('user_id','=',Auth::user()->id)
                ->orderBy('id','desc')
                ->get();
        }elseif (Auth::user()->hasRole('cashier')){
            $patientRequestList = PatientRequest::with(['appointmentType','detail','patient','patientDetail','ccrm'])
                ->where(function ($q) use ($request){
                    if ($request->has('type') && $request->type==='pending'){
                        $q->whereNull('clincial_id');
                    }elseif ($request->has('type') && $request->type==='running'){
                        $q->whereNotNull('clincial_id')->where('status','!=','complete');
                    }elseif ($request->has('type') && $request->type==='complete'){
                        $q->where('status','=','complete');
                    }elseif ($request->has('type') && $request->type==='latest'){
                        $q->where('created_at', '>',
                            Carbon::now()->subHours(3)->toDateTimeString()
                        );
                    }
                })
                ->whereNull('appointmentType')
                ->orderBy('id','desc')
                ->get();
        } else{
            $patientRequestList = PatientRequest::with(['appointmentType','detail','patient','patientDetail','ccrm'])
                ->where(function ($q) use ($request){
                    if ($request->has('type') && $request->type==='pending'){
                        $q->whereNull('clincial_id');
                    }elseif ($request->has('type') && $request->type==='running'){
                        $q->whereNotNull('clincial_id')->where('status','!=','complete');
                    }elseif ($request->has('type') && $request->type==='complete'){
                        $q->where('status','=','complete');
                    }elseif ($request->has('type') && $request->type==='latest'){
                        $q->where('created_at', '>',
                            Carbon::now()->subHours(3)->toDateTimeString()
                        );
                    }
                })
                ->orderBy('id','desc')
                ->get();
        }

        if (count($patientRequestList)>0){
            return $this->generateResponse(true,'Patient Request List',$patientRequestList,200);
        }
        return $this->generateResponse(false,'Patient Request Not Available',$patientRequestList,200);
    }

    public function sendNexmoMessage($userDetails,$type){
        $from = "12089104598";
        //$to = "5166000122";
        $to = "9293989855";
        $api_key = "bb78dfeb";
        $api_secret = "PoZ5ZWbnhEYzP9m4";

        $text = 'Doral Health Connect | Your patient '.$userDetails->first_name.' blood pressure is slightly higher than regular. http://app.doralhealthconnect.com/caregiver/1';
        $uri 	= 'https://rest.nexmo.com/sms/json';
        $fields =
           '&from=' .  urlencode( $from ) .
           '&text=' . urlencode( $text ) .
           '&to=+1' . urlencode( $to ) .
           '&api_key=' . urlencode( $api_key ) .
           '&api_secret=' . urlencode( $api_secret );
        // start cURL
        $res = curl_init($uri);
        // set cURL options
        curl_setopt( $res, CURLOPT_POST, TRUE );
        curl_setopt( $res, CURLOPT_RETURNTRANSFER, TRUE ); // don't echo
        curl_setopt( $res, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $res, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
//        curl_setopt( $res, CURLOPT_USERPWD, $auth ); // authenticate
        curl_setopt( $res, CURLOPT_POSTFIELDS, $fields );
        // send cURL
        $result = curl_exec( $res );
        curl_close($res);
        if($type == 2){
            return $this->sendNexmoMessageClinician($userDetails);
        }

    }
    public function sendNexmoMessageForGluco($userDetails,$type) {
        if($type == 3) {
            $le = 'lower';
        }else {
            $le = 'higher';
        }
        $from = "12089104598";
        $to = "5166000122";
//        $to = "9173646218";
        $api_key = "bb78dfeb";
        $api_secret = "PoZ5ZWbnhEYzP9m4";

        $text = 'Doral Health Connect | Your patient '.$userDetails->first_name.' sugar is slightly '.$le.' regular. http://app.doralhealthconnect.com/caregiver/'.$type;
        $uri 	= 'https://rest.nexmo.com/sms/json';
        $fields =
           '&from=' .  urlencode( $from ) .
           '&text=' . urlencode( $text ) .
           '&to=+1' . urlencode( $to ) .
           '&api_key=' . urlencode( $api_key ) .
           '&api_secret=' . urlencode( $api_secret );
        // start cURL
        $res = curl_init($uri);
        // set cURL options
        curl_setopt( $res, CURLOPT_POST, TRUE );
        curl_setopt( $res, CURLOPT_RETURNTRANSFER, TRUE ); // don't echo
        curl_setopt( $res, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $res, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
//        curl_setopt( $res, CURLOPT_USERPWD, $auth ); // authenticate
        curl_setopt( $res, CURLOPT_POSTFIELDS, $fields );
        // send cURL
        $result = curl_exec( $res );
        curl_close($res);
        if($type == 2){
            return $this->sendNexmoMessageClinician($userDetails);
        }

    }
    public function sendNexmoMessageClinician($userDetails){
        $from = "12089104598";
        $to = "5166000122";
//        $to = "9173646218";
        $api_key = "bb78dfeb";
        $api_secret = "PoZ5ZWbnhEYzP9m4";

        $text = 'Doral Health Connect | Your patient '.$userDetails->first_name.' blood pressure is higher than regular. Need immediate attention. http://app.doralhealthconnect.com/caregiver/2';
        $uri 	= 'https://rest.nexmo.com/sms/json';
        $fields =
           '&from=' .  urlencode( $from ) .
           '&text=' . urlencode( $text ) .
           '&to=+1' . urlencode( $to ) .
           '&api_key=' . urlencode( $api_key ) .
           '&api_secret=' . urlencode( $api_secret );
        // start cURL
        $res = curl_init($uri);
        // set cURL options
        curl_setopt( $res, CURLOPT_POST, TRUE );
        curl_setopt( $res, CURLOPT_RETURNTRANSFER, TRUE ); // don't echo
        curl_setopt( $res, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $res, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
//        curl_setopt( $res, CURLOPT_USERPWD, $auth ); // authenticate
        curl_setopt( $res, CURLOPT_POSTFIELDS, $fields );
        // send cURL
        $result = curl_exec( $res );
        curl_close($res);

    }

}
