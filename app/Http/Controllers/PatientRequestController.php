<?php

namespace App\Http\Controllers;

use App\Events\SendClinicianPatientRequestNotification;
use App\Events\SendingSMS;
use App\Events\SendPatientNotificationMap;
use App\Http\Requests\CCMReadingRequest;
use App\Http\Requests\ClinicianRequestAcceptRequest;
use App\Http\Requests\PatientRequestAcceptRequest;
use App\Models\CCMReading;
use App\Models\RoadlInformation;
use App\Models\User;
use App\Models\PatientRequest;
use App\Http\Requests\PatientRequest as PatientRequestValidation;
use App\Notifications\BroadCastNotification;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\Types\Object_;

class PatientRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $patient = new PatientRequest();
            $patient->user_id = $request->user_id;
            $patient->latitude = $request->latitude;
            $patient->longitude = $request->longitude;
            $patient->reason = $request->reason;
            if ($patient->save()){
                $clinicianList = User::whereHas('roles',function ($q){
                    $q->where('name','=','clinician');
                })->where('is_available','=','1')->get();
                $data=PatientRequest::with('detail')
                    ->where('id','=',$patient->id)
                    ->first();
                event(new SendClinicianPatientRequestNotification($data,$clinicianList));
                return $this->generateResponse(true,'Add Request Successfully!');
            }
            return $this->generateResponse(false,'Something Went Wrong!');
        }catch (Exception $exception){
            return $this->generateResponse(false,$exception->getMessage());
        }
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
        $ccmReadingModel = new CCMReading();
        $ccmReadingModel->user_id = $request->user_id;
        $ccmReadingModel->reading_type = $request->reading_type;
        $ccmReadingModel->reading_value = $request->reading_value;
        $userDetails = User::getUserDetails($request->user_id);
//        dd($request->all());
        if($request->reading_type == 1) {
            $reading_level = 1;
            $explodeValue = explode("/",$request->reading_value);
            if($explodeValue[0] >= 130 && $explodeValue[0] <= 139) {
                $reading_level = 2;
            }else if($explodeValue[0] >= 140) {
                $reading_level = 3;
            }
            $ccmReadingModel->reading_level = $reading_level;
        }else if($request->reading_type == 2) {

            if($request->reading_value > 250) {
                $reading_level = 4;
            }else if($request->reading_level < 60) {
                $reading_level = 3;
            }
            $ccmReadingModel->reading_level = 3;
        }else if($request->reading_type == 3) {
            if($request->reading_value > 110) {
                $reading_level = 1;
            }
            $ccmReadingModel->reading_level = 3;
        }

        if ($request->reading_type==="1"){
            $meesages = array();
            $meesages[] =array(
                'to'=>env('SMS_TO'),
                'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' blood pressure is slightly higher than regular. https://app.doralhealthconnect.com/caregiver/1');

            if ($reading_level===3){
                $meesages[] =array(
                    'to'=>env('SMS_TO'),
                    'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' blood pressure is higher than regular. Need immediate attention. http://app.doralhealthconnect.com/caregiver/2');

            }
            event(new SendingSMS($meesages));
        }elseif ($request->reading_type==="2"){
            if($reading_level == 3) {
                $le = 'lower';
            }else {
                $le = 'higher';
            }

            $meesages = array();
            $meesages[] =array(
                'to'=>env('SMS_TO'),
                'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' sugar is slightly '.$le.' regular. http://app.doralhealthconnect.com/caregiver/'.$reading_level
            );

            if ($reading_level===3){
                $meesages[] =array(
                    'to'=>env('SMS_TO'),
                    'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' blood pressure is higher than regular. Need immediate attention. http://app.doralhealthconnect.com/caregiver/2');

            }
            event(new SendingSMS($meesages));
        }elseif ($request->reading_type==="3"){
            if($reading_level == 3) {
                $le = 'lower';
            }else {
                $le = 'higher';
            }

            $meesages = array();
            $meesages[] =array(
                'to'=>env('SMS_TO'),
                'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' sugar is slightly '.$le.' regular. http://app.doralhealthconnect.com/caregiver/'.$reading_level
            );
            event(new SendingSMS($meesages));
        }

        if ($ccmReadingModel->save()){

            return $this->generateResponse(true,'CCM Reading Success!',$ccmReadingModel);
        }
        return $this->generateResponse(false,'Something Went Wrong!');
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
                $users->save();

                $roadlInformation = new RoadlInformation();
                $roadlInformation->user_id = $patient->user_id;
                $roadlInformation->patient_requests_id = $patient->id;
                $roadlInformation->client_id = $request->user_id;
                $roadlInformation->latitude = $request->latitude;
                $roadlInformation->longitude = $request->longitude;
                $roadlInformation->status = "start";
                $roadlInformation->save();

                $clinician=User::where(['id'=>$patient->user_id])->whereHas('roles',function ($q){
                    $q->where('name','=','clinician');
                })->first();

                $data=array(
                    'latitude'=>$request->latitude,
                    'longitude'=>$request->longitude,
                    'clinician'=>$clinician
                );
                event(new SendPatientNotificationMap($data,$patient->user_id));

                $data=PatientRequest::with('detail')
                    ->where('id','=',$request->request_id)
                    ->first();
                return $this->generateResponse(true,'Request Accepted!',$data,200);
            }
        }

        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }

    public function clinicianPatientRequestList(Request $request){
        $patientRequestList = PatientRequest::with('detail','ccrm','patientDetail')
            ->where(function ($q){
                $q->where('clincial_id','=',null)->orWhere('clincial_id','=',Auth::user()->id);
            })
            ->where('is_active','=','1')
            ->orderBy('id','desc')
            ->get();
        if (count($patientRequestList)>0){
            return $this->generateResponse(true,'Patient Request List',$patientRequestList,200);
        }
        return $this->generateResponse(false,'Something Went Wrong',null,200);
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
//        $to = "5166000122";
        $to = "9173646218";
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
