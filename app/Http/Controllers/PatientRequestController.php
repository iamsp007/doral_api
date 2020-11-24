<?php

namespace App\Http\Controllers;

use App\Events\SendClinicianPatientRequestNotification;
use App\Http\Requests\CCMReadingRequest;
use App\Http\Requests\ClinicianRequestAcceptRequest;
use App\Http\Requests\PatientRequestAcceptRequest;
use App\Models\CCMReading;
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
                $data=PatientRequest::with('detail')
                    ->where('user_id','=',$patient->id)
                    ->first();
                event(new SendClinicianPatientRequestNotification($data));
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
            $explodeValue = explode("/",$request->reading_value);
            if($explodeValue[0] >= 130 && $explodeValue[0] <= 139) {
                $this->sendNexmoMessage($userDetails,1);
            }else if($explodeValue[0] >= 140) {
                $this->sendNexmoMessage($userDetails,2);
            }
        }else if($request->reading_type == 2) {
            if($request->reading_value > 120) {
                $this->sendNexmoMessage($userDetails);
            }
        }else if($request->reading_type == 3) {
            if($request->reading_value > 110) {
                $this->sendNexmoMessage($userDetails);
            }
        }

        if ($ccmReadingModel->save()){
            return $this->generateResponse(true,'CCM Reading Success!');
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
                $data=PatientRequest::with('detail')
                    ->where('id','=',$request->request_id)
                    ->first();
                return $this->generateResponse(true,'Request Accepted!',$data,200);
            }
        }

        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }

    public function sendNexmoMessage($userDetails,$type){
        $from = "12089104598";
        $to = "5166000122";
        $api_key = "bb78dfeb";
        $api_secret = "PoZ5ZWbnhEYzP9m4";

        $text = 'Doral Health Connect | Caregiver : Your Patient '.$userDetails->first_name.' is having some issue. http://doralhealthconnect.com/caregiver/1';
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
        $api_key = "bb78dfeb";
        $api_secret = "PoZ5ZWbnhEYzP9m4";

        $text = 'Doral Health Connect | Clinician : Your Patient '.$userDetails->first_name.' is having some issue. http://doralhealthconnect.com/caregiver/1';
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
