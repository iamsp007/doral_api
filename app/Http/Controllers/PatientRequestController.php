<?php

namespace App\Http\Controllers;

use App\Events\SendClinicianPatientRequestNotification;
use App\Events\SendingSMS;
use App\Events\SendPatientNotificationMap;
use App\Helpers\Helper;
use App\Http\Requests\CCMReadingRequest;
use App\Http\Requests\ClinicianRequestAcceptRequest;
use App\Models\AssignAppointmentRoadl;
use App\Models\CCMReading;
use App\Models\Referral;
use App\Models\RoadlInformation;
use App\Models\User;
use App\Models\PatientRequest;
use App\Http\Requests\PatientRequest as PatientRequestValidation;
use App\Models\Category;
use App\Models\DiesesMaster;
use App\Models\NotificationHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\RoadlRequestTo;

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
            ->whereNotNull('parent_id')
        	->whereDate('created_at', Carbon::today())
        	->whereIn('status',['2','3'])
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
            $check = PatientRequest::where('user_id', $request->user_id)
                ->where('type_id','=',$request->type_id)
                ->whereIn('status', ['1','2','3','6','7'])->count();

            if ($check>0){
                return $this->generateResponse(false,'Already Create This Request',null,200);
            }

            if ($request->patient_id) {
                $latlong = $this->getLatlong($request->patient_id);
                $request['latitude'] = $latlong['latitude'];
                $request['longitude'] = $latlong['longitude'];
            }
            $request_id = Auth::user()->id;
            $patientRequest = PatientRequest::where('user_id', $request->patient_id)->whereNull('parent_id')->where('status', '1')->first();

            if(! $patientRequest) {

                $patient = new PatientRequest();
                $patient->user_id = $request->user_id;
                $patient->requester_id = $request_id;
                $patient->latitude = $request->latitude;
                $patient->longitude = $request->longitude;
                $patient->reason = $request->reason;
                $patient->save();

                $patientSecond = new PatientRequest();

                $patientSecond->user_id = $request->user_id;
                $patientSecond->requester_id = $request_id;
                $patientSecond->type_id = $request->type_id;
                $patientSecond->latitude = $request->latitude;
                $patientSecond->longitude = $request->longitude;
                $patientSecond->reason = $request->reason;
                if($request->has('test_name')){
                    $patientSecond->test_name=$request->test_name;
                }
                if($request->has('sub_test_name')){
                    $patientSecond->sub_test_name=$request->sub_test_name;
                }
                if($request->has('dieses')){
                    $patientSecond->dieses=$request->dieses;
                }
                if($request->has('symptoms')){
                    $patientSecond->symptoms=$request->symptoms;
                }
                if($request->has('is_parking')){
                    $patientSecond->is_parking=$request->is_parking;
                }

                if(isset($request->clinician_list_id) && $request->clinician_list_id !='' && $request->clinician_list_id !=0) {
                    $patientSecond->clincial_id = $request->clinician_list_id;
                }
                $patientSecond->parent_id = $patient->id;

                $patientSecond->save();
                $parent_id=$patient->id;

                $notificationHistory = new NotificationHistory();
                $notificationHistory->type = 'RoadL Request';
                $notificationHistory->sender_id = $request->user_id;
                $notificationHistory->request_id = $patientSecond->id;
                $notificationHistory->model_type = PatientRequest::class;
                $notificationHistory->status = 'active';

                $notificationHistory->save();
            } else {
                $patientSecond = new PatientRequest();

                $patientSecond->user_id = $request->user_id;
                $patientSecond->requester_id = $request_id;
                $patientSecond->type_id = $request->type_id;
                $patientSecond->latitude = $request->latitude;
                $patientSecond->longitude = $request->longitude;
                $patientSecond->reason = $request->reason;
                if($request->has('test_name')){
                    $patientSecond->test_name=$request->test_name;
                }
                if($request->has('sub_test_name')){
                    $patientSecond->sub_test_name=$request->sub_test_name;
                }
                if($request->has('dieses')){
                    $patientSecond->dieses=$request->dieses;
                }
                if($request->has('symptoms')){
                    $patientSecond->symptoms=$request->symptoms;
                }
                if($request->has('is_parking')){
                    $patientSecond->is_parking=$request->is_parking;
                }
                $patientSecond->parent_id = $patientRequest->id;

                if(isset($request->clinician_list_id) && $request->clinician_list_id !='' && $request->clinician_list_id !=0) {
                    $patientSecond->clincial_id = $request->clinician_list_id;
                }

                $patientSecond->save();
                $parent_id=$patientRequest->id;

                $notificationHistory = new NotificationHistory();
                $notificationHistory->type = 'RoadL Request';
                $notificationHistory->sender_id = $request->user_id;
                $notificationHistory->request_id = $patientSecond->id;
                $notificationHistory->model_type = PatientRequest::class;
                $notificationHistory->status = 'active';

                $notificationHistory->save();
            }

            // If assign clinician
            $checkAssignId = '';
            if($request->clinician_list_id !='' && $request->clinician_list_id !=0) {
                $checkAssignId = $request->clinician_list_id;
            }

            if($checkAssignId == '') {
                if($request->type_id == 4) {
                    $clinicianIds = User::with('roles')
                    ->whereHas('roles',function($q){
                        $q->where('name','=','clinician');
                    })
                    //->where('is_available','=','1')
                    ->get();

                } else if($request->type_id == 6) {
                    $clinicianIds = User::with('roles')
                    ->whereHas('roles',function($q) use($request){
                        $q->where('id','=', '18');
                    })
                    //->where('is_available','=','1')
                    ->get();

                } else if($request->type_id == 7) {
                    $clinicianIds = User::with('roles')
                    ->whereHas('roles',function($q) use($request){
                        $q->where('id','=', '19');
                    })
                    //->where('is_available','=','1')
                    ->get();

                } else if($request->type_id == 8) {
                    $clinicianIds = User::with('roles')
                    ->whereHas('roles',function($q) use($request){
                        $q->where('id','=', '20');
                    })
                    //->where('is_available','=','1')
                    ->get();

                } else if($request->type_id == 9) {
                    $clinicianIds = User::with('roles')
                    ->whereHas('roles',function($q) use($request){
                        $q->where('id','=', '21');
                    })
                    //->where('is_available','=','1')
                    ->get();

                } else if($request->type_id == 10) {
                    $clinicianIds = User::with('roles')
                    ->whereHas('roles',function($q) use($request){
                        $q->where('id','=', '22');
                    })
                    //->where('is_available','=','1')
                    ->get();

                } else if($request->type_id == 11) {
                    $clinicianIds = User::with('roles')
                    ->whereHas('roles',function($q) use($request){
                        $q->where('id','=', '23');
                    })
                    //->where('is_available','=','1')
                    ->get();

                } else if($request->type_id == 12) {
                    $clinicianIds = User::with('roles')
                    ->whereHas('roles',function($q) use($request){
                        $q->where('id','=', '24');
                    })
                    //->where('is_available','=','1')
                    ->get();
                }

                $markers = collect($clinicianIds)->map(function($item) use ($request){
                    $roadlController = new RoadlController();
                    $item['distance'] = $roadlController->calculateDistanceBetweenTwoAddresses($item->latitude, $item->longitude, $request->latitude,$request->longitude);
                    return $item;
                })
                // ->where('distance','<=',20)
                ->pluck('id');

                $clinicianList = User::whereIn('id',$markers)->get();
                // $clinicianList = User::where('designation_id','=',$request->type_id)->where('is_available','=','1')->get();


                // if ($request->has('type')){
                // foreach ($request->type as $value) {
                // $response = $this->createPatientRequest($request,$value);
                // }
                // }else{
                // $response = $this->createPatientRequest($request);
                // }
                // return $response;
            }else {
                $clinicianList = User::where('id',$checkAssignId)->get();
            }

            foreach ($clinicianList as $key => $list) {
                RoadlRequestTo::create([
                    'patient_request_id' => $patientSecond->id,
                    'clinician_id' => $list->id,
                ]);
            }

             $data = PatientRequest::with('detail','patient','request')
                ->where('id','=',$patientSecond->id)
                ->first();

                event(new SendClinicianPatientRequestNotification($data,$clinicianList));

                $smsController = new SmsController();
                $smsController->sendSms($data,'1');

                if (isset($request['roadlStatus']) && $request['roadlStatus'] == 'multipleRequest') {
                    return $parent_id;
                } else {
                    return $this->generateResponse(true,'Add Request Successfully!',array('parent_id'=>$parent_id),200);
                }

        } catch (Exception $exception){
            return $this->generateResponse(false,$exception->getMessage());
        }
    }

      /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMulti(PatientRequestValidation $request)
    {
        try {
            $user_ids = explode(",",$request->user_id);

            $parentIds = [];
            foreach ($user_ids as $key => $user_id) {

                $request['user_id'] = $user_id;
                $request['patient_id'] = $user_id;
                $request['roadlStatus'] = 'multipleRequest';
                $parentIds = $this->store($request);
            }
            return $this->generateResponse(true,'Add Request Successfully!',array('parent_id'=>$parentIds),200);

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
        if($request->has('test_name')){
            $patient->test_name=$request->test_name;
        }

        if($request->has('dieses')){
            $patient->dieses=$request->dieses;
        }
        if($request->has('symptoms')){
            $patient->symptoms=$request->symptoms;
        }
        if($request->has('is_parking')){
            $patient->is_parking=$request->is_parking;
        }

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

                $data=PatientRequest::with('detail','patient')
                    ->where('id','=',$patient->id)
                    ->first();
                event(new SendClinicianPatientRequestNotification($data,$clinicianList));
            }else{
                $clinicianList = User::whereHas('roles',function ($q){
                    $q->where('name','=','clinician');
                })->where('is_available','=','1')->get();
                $data=PatientRequest::with('detail','patient')
                    ->where('id','=',$patient->id)
                    ->first();
                event(new SendClinicianPatientRequestNotification($data,$clinicianList));
            }
            return $this->generateResponse(true,'Add Request Successfully!');
        }
        return $this->generateResponse(false,'Something Went Wrong!');
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
            $reading_type = 1;
            if ($reading_type == 0) {

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

            } elseif ($reading_type == 1) {

                $messages = array();

                if ($readingLevel == 3) {
                    $messages[] =array(
                        'to'=>env('SMS_TO'),
                        'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' blood sugar is higher than regular. Need immediate attention. http://app.doralhealthconnect.com/caregiver/'.$readingLevel
//                        'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' blood pressure is higher than regular. Need immediate attention. http://app.doralhealthconnect.com/caregiver/'.$readingLevel
                    );
                } else {
                    $messages[] =array(
                        'to'=>env('SMS_TO'),
                        'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' blood sugar is slightly higher than regular. http://app.doralhealthconnect.com/caregiver/'.$readingLevel
//                        'message'=>'Doral Health Connect | Your patient '.$userDetails->first_name.' blood pressure is higher than regular. Need immediate attention. http://app.doralhealthconnect.com/caregiver/'.$readingLevel
                    );
                }
                event(new SendingSMS($messages));

            } elseif ($reading_type == 2) {

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

        $patient = PatientRequest::find($request->request_id);
        if ($patient){
            if(null!==$patient->clincial_id && $patient->status !=1){
                return $this->generateResponse(false,'Request Already Accepted!',null,200);
            }
            $patient->clincial_id=$request->user_id;
            $patient->updated_at=Carbon::now()->toDateTime();
            $patient->status='2';
            $patient->accepted_time = Carbon::now()->toDateTime();
            $patient->distance = isset($request->distance) ? $request->distance : '';
            $patient->travel_time = isset($request->travel_time) ? $request->travel_time : '';
            if ($patient->save()){

                $notificationHistory = NotificationHistory::where('request_id',$patient->id)->first();
                $notificationHistory->receiver_id = $patient->clincial_id;
                $notificationHistory->status = 'accept';
                $notificationHistory->save();

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
                $roadlInformation->is_status = "2";
                $roadlInformation->save();
                $patient->clinician = $users;

                event(new SendPatientNotificationMap($patient->toArray(),$patient->user_id,$users));
                // event(new SendPatientNotificationMap($patient->toArray(),$patient->clincial_id,$users));

                $data = PatientRequest::with('detail', 'patient','request')->where('id','=',$request->request_id)->first();

                $smsController = new SmsController();
                $smsController->sendSms($data,'2');

                return $this->generateResponse(true,'Request Accepted!',$data,200);
            }
        }

        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }

    public function clinicianPatientRequestList(Request $request)
    {

        $type = $request['type'];
        $status = explode(",",$type);
        if(Auth::user()->hasRole('patient')) {
            $role = 5;
        }else if(Auth::user()->hasRole('clinician')) {
            $role = 4;
            $designation = '';
            if(Auth::user()->designation_id == 2) {
                $designation = 2;
            }else if(Auth::user()->designation_id == 1) {
                $designation = 1;
            }
        }else if(Auth::user()->hasRole('LAB')) {
            $role = 6;
        }else if(Auth::user()->hasRole('Radiology')) {
            $role = 7;
        }else if(Auth::user()->hasRole('CHHA')) {
            $role = 8;
        }else if(Auth::user()->hasRole('Home Oxygen')) {
            $role = 9;
        }else if(Auth::user()->hasRole('Home Influsion')) {
            $role = 10;
        }else if(Auth::user()->hasRole('Wound Care')) {
            $role = 11;
        }else if(Auth::user()->hasRole('DME')) {
            $role = 12;
        }

         $authUser = Auth::user();

        // $role_id = implode(',',$authUser->roles->pluck('id')->toArray());

        if (Auth::user()->hasRole('patient')){
            $patientRequestList = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
                // ->where(function ($q) use ($status){
                //     if ($status!=='all'){
                //         $q->where('status','=',$status);
                //     }
                // })
                ->whereIn('status',$status)
                ->whereNotNull('parent_id')
                ->where('user_id','=',Auth::user()->id)
                ->groupBy('parent_id')
                ->orderBy('id','desc')
                ->get();

        } elseif ($status[0] == 4){
            $patientRequestList = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
                   ->where('clincial_id','=',Auth::user()->id)
                   ->whereNotNull('parent_id')
                   ->where('status', 4)
                   ->groupBy('parent_id')
                   ->orderBy('id','desc')
                   ->get();
        } elseif ($status[0] == 5){
            $patientRequestList = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
                   ->where('clincial_id','=',Auth::user()->id)
                   ->whereNotNull('parent_id')
                   ->where('status', 5)
                   ->groupBy('parent_id')
                   ->orderBy('id','desc')
                   ->get();
        } elseif (Auth::user()->is_available==='2'){
            if($status[0] == 2 || $status[0] == 3) {
//                if($designation == 2) {
//                    $role = 2;
//                }
            $patientRequestList = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
                ->where('clincial_id','=',Auth::user()->id)
                ->whereNotNull('parent_id')
//                    ->where('type_id',$role)
                ->whereIn('status', $status)
                ->groupBy('parent_id')
                ->orderBy('id','desc')
                ->get();
            }else if($status[0] == 1) {
                $patientRequestList = '';
                return $this->generateResponse(false,'You have already accepted other visit so you should complete first.',$patientRequestList,200);
            }
        } else {
            if($status[0] == 1) {
                $patientRequestList = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
                    ->whereIn('status',$status)
                    ->whereNotNull('parent_id')
                    ->where('type_id',$role)
                    ->groupBy('parent_id')
                    ->selectRaw("*,
                        ( 6371 * acos( cos( radians(" .  $authUser->latitude . ") ) *
                        cos( radians(latitude) ) *
                        cos( radians(longitude) - radians(" . $authUser->longitude . ") ) +
                        sin( radians(" . $authUser->latitude . ") ) *
                        sin( radians(latitude) ) ) )
                        AS distance")
                    ->orderBy("distance", "asc")
                    ->get();

                if($role == 4 && $designation !='') {

                    $directClinicins = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
                        ->whereIn('status',$status)
                        ->where('clincial_id', Auth::user()->id)
                        ->whereNotNull('parent_id')
                        ->where('type_id',$designation)
                        ->groupBy('parent_id')
                        ->selectRaw("*,
                            ( 6371 * acos( cos( radians(" .  $authUser->latitude . ") ) *
                            cos( radians(latitude) ) *
                            cos( radians(longitude) - radians(" . $authUser->longitude . ") ) +
                            sin( radians(" . $authUser->latitude . ") ) *
                            sin( radians(latitude) ) ) )
                            AS distance")
                        ->orderBy("distance", "asc")
                        ->get();

                    if(count($directClinicins)>0) {
                        $patientRequestList = $directClinicins;
                    }
                }

            } else if($status[0] == 4) {
                $patientRequestList = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
                   ->where('clincial_id','=',Auth::user()->id)
                   ->whereNotNull('parent_id')
                   ->whereIn('status', ['4'])
                   ->groupBy('parent_id')
                   ->orderBy('id','desc')
                   ->get();
            } else {
                $patientRequestList = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
                ->where('clincial_id','=',Auth::user()->id)
                ->whereNotNull('parent_id')
                ->whereIn('status', ['2','3','6','7'])
                ->groupBy('parent_id')
                ->orderBy('id','desc')
                ->get();
            }

            //            $roles = Auth::user()->roles->pluck('id');
            //            $patientRequestList = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm'])
                            // ->where(function ($q) use ($status){
                            //     if ($status!=='all'){
                            //         $q->where('status','=',$status);
                            //     }
                            // })
            //                ->whereIn('status',$status)
            //                ->whereNotNull('parent_id')
                            // ->where(function ($q){
                            //     $q->where('clincial_id','=',Auth::user()->id)
                            //         ->orWhere(function ($q){
                            //            $q->whereNull('clincial_id')
                            //                ->where('type_id','=',Auth::user()->designation_id);
                            //         });
                            // })
            //                ->groupBy('parent_id')
            //                ->orderBy('id','desc')
            //                ->get();
        }


        //        $referral = Referral::where('guard_name','=','partner')
        //            ->whereIn('name',Auth::user()->roles->pluck('name'))
        //            ->pluck('name');
        //        if (count($referral)>0){
        //            $patientRequestList = PatientRequest::with(['appointmentType','detail','patient','patientDetail','ccrm'])
        //                ->whereHas('appointmentType',function ($q) use ($referral){
        //                    $q->whereIn('referral_type',$referral);
        //                })
        //                ->where(function ($q) use ($request){
        //                    if ($request->has('type') && $request->type==='pending'){
        //                        $q->whereNull('clincial_id');
        //                    }elseif ($request->has('type') && $request->type==='running'){
        //                        $q->whereNotNull('clincial_id')->where('status','!=','complete');
        //                    }elseif ($request->has('type') && $request->type==='complete'){
        //                        $q->where('status','=','complete');
        //                    }elseif ($request->has('type') && $request->type==='latest'){
        //                        $q->where('created_at', '>',
        //                            Carbon::now()->subHours(1)->toDateTimeString()
        //                        );
        //                    }
        //                })
        //                ->orderBy('id','desc')
        //                ->get();
        //
        //        }elseif (Auth::user()->hasRole('patient')){
        //            $patientRequestList = PatientRequest::with(['appointmentType','detail','patient','patientDetail','ccrm'])
        //                ->where(function ($q) use ($request){
        //                    if ($request->has('type') && $request->type==='pending'){
        //                        $q->whereNull('clincial_id');
        //                    }elseif ($request->has('type') && $request->type==='running'){
        //                        $q->whereNotNull('clincial_id')->where('status','!=','complete');
        //                    }elseif ($request->has('type') && $request->type==='complete'){
        //                        $q->where('status','=','complete');
        //                    }elseif ($request->has('type') && $request->type==='latest'){
        //                        $q->where('created_at', '>',
        //                            Carbon::now()->subHours(3)->toDateTimeString()
        //                        );
        //                    }
        //                })
        //                ->where('user_id','=',Auth::user()->id)
        //                ->orderBy('id','desc')
        //                ->get();
        //        }elseif (Auth::user()->hasRole('cashier')){
        //            $patientRequestList = PatientRequest::with(['appointmentType','detail','patient','patientDetail','ccrm'])
        //                ->where(function ($q) use ($request){
        //                    if ($request->has('type') && $request->type==='pending'){
        //                        $q->whereNull('clincial_id');
        //                    }elseif ($request->has('type') && $request->type==='running'){
        //                        $q->whereNotNull('clincial_id')->where('status','!=','complete');
        //                    }elseif ($request->has('type') && $request->type==='complete'){
        //                        $q->where('status','=','complete');
        //                    }elseif ($request->has('type') && $request->type==='latest'){
        //                        $q->where('created_at', '>',
        //                            Carbon::now()->subHours(3)->toDateTimeString()
        //                        );
        //                    }
        //                })
        //                ->whereDoesntHave('appointmentType')
        //                ->orderBy('id','desc')
        //                ->get();
        //        } else{
        //            $patientRequestList = PatientRequest::with(['appointmentType','detail','patient','patientDetail','ccrm'])
        //                ->where(function ($q) use ($request){
        //                    if ($request->has('type') && $request->type==='pending'){
        //                        $q->whereNull('clincial_id');
        //                    }elseif ($request->has('type') && $request->type==='running'){
        //                        $q->whereNotNull('clincial_id')->where('status','!=','complete');
        //                    }elseif ($request->has('type') && $request->type==='complete'){
        //                        $q->where('status','=','complete');
        //                    }elseif ($request->has('type') && $request->type==='latest'){
        //                        $q->where('created_at', '>',
        //                            Carbon::now()->subHours(3)->toDateTimeString()
        //                        );
        //                    }
        //                })
        //                ->orderBy('id','desc')
        //                ->get();
        //        }

        if (count($patientRequestList)>0){
            return $this->generateResponse(true,'Patient Request List',$patientRequestList,200);
        }
        return $this->generateResponse(false,'Patient Request Not Available',$patientRequestList,200);
    }

        public function latestClinicianRoadlRequest()
    {

        $directClinicins = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
            ->where('status',1)
            ->where('clincial_id', Auth::user()->id)
            ->whereNotNull('parent_id')
            ->orderBy("id", "desc")
            ->first();

            if ($directClinicins){
                return $this->generateResponse(true,'Patient Request List',$directClinicins,200);
            }
            return $this->generateResponse(false,'Patient Request Not Available',null,200);
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

    public function getVendorList(Request $request){

        $vendorList = Referral::where('guard_name','=','partner')
            ->where('status','=','active')
            ->get();
        if ($request->has('parent_id')){
            $vendorList = collect($vendorList)->map(function ($row) use ($request){
                $check = PatientRequest::where('parent_id', $request->parent_id)
                    ->whereNotNull('parent_id')
                    ->where('type_id','=',$row->role_id)
                    // ->where('status','!=','1')
                    ->first();
                $row->check = $check;
                return $row;
            });
        }
        return $this->generateResponse(true,'Vendor List APi',$vendorList,200);
    }

    public function getClinicianList(Request $request){

        $clinicianList = User::where([['designation_id','=',$request->role_id], ['status','=','1']])->get();
        // $clinicianList = User::where([['designation_id','=',$request->role_id], ['status','=','1'], ['is_available','=','1']])->get();

        $categories = Category::where('type_id',$request->role_id)->where('status',"1")->get();

        $dieses = DiesesMaster::where('status','=',1)->get();
        $data = [
            'clinicianList' => $clinicianList,
            'categories' => $categories,
            'dieses' => $dieses
        ];

        return $this->generateResponse(true,'Clinician List APi',$data,200);
    }

    /**
     * getParentIdUsingPatientId
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getParentIdUsingPatientId(Request $request)
    {
        try {
            $parent = PatientRequest::select('parent_id')
                ->where('user_id', $request->patient_id)
                ->whereNotNull('parent_id')
                ->orderBy('id','desc')
                ->first();

            return $this->generateResponse(true, 'Fetched parent id', $parent, 200);
        } catch (\Exception $ex) {
            return $this->generateResponse(false, $ex->getMessage());
        }
    }
    public function getLatlong($patient_id)
    {
        $details = User::with('demographic')->find($patient_id);

        if (isset($details->demographic->address) && $details->demographic){
            $addresses=$details->demographic->address;
            $address='';
            if (isset($addresses['address1'])){
                $address.=$addresses['address1'];
            }
            if (isset($addresses['address2'])){
                $address.=$addresses['address2'];
            }
            if (isset($addresses['city'])){
                $address.=','.$addresses['city'];
            }
            if (isset($addresses['state'])){
                $address.=','.$addresses['state'];
            }
            if (isset($addresses['country'])){
                $address.=','.$addresses['country'];
            }
            if (isset($addresses['zip'])){
                $address.=','.$addresses['zip'];
            }
            $helper = new Helper();
            $response = $helper->getLatLngFromAddress($address);
            if ($response->status==='REQUEST_DENIED'){
                $latitude=$details->latitude;
                $longitude=$details->longitude;
            }else{
                $latitude=$response->results[0]->geometry->location->lat;
                $longitude=$response->results[0]->geometry->location->lng;
            }
        }else{
            $latitude=$details->latitude;
            $longitude=$details->longitude;
        }
        return [
            'latitude' => $latitude,
            'longitude' => $longitude
        ];
    }
    public function updatePreperationTime(Request $request)
    {
        try {
            PatientRequest::find($request['patient_request_id'])->update([
                'preparation_time' => $request['preparation_time'],
                'preparasion_date' => $request['preparasion_date']
            ]);

            return $this->generateResponse(true, 'Preparation time updated successfully', null, 200);
        } catch (\Exception $ex) {
            return $this->generateResponse(false, $ex->getMessage());
        }
    }
}
