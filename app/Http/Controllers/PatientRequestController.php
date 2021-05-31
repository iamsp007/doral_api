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
use App\Mail\UpdateStatusNotification;
use App\Models\NotificationHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        	// ->where('status','1')
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
            $patientRequest = PatientRequest::where('user_id', $request->patient_id)->whereNull('parent_id')->where('status', '1')->first();
            if(! $patientRequest) {
                $patient = new PatientRequest();
                $patient->user_id = $request->user_id;
                $patient->latitude = $request->latitude;
                $patient->longitude = $request->longitude;
                $patient->reason = $request->reason;
                $patient->save();

                $patientSecond = new PatientRequest();

                $patientSecond->user_id = $request->user_id;
                $patientSecond->type_id = $request->type_id;
                $patientSecond->latitude = $request->latitude;
                $patientSecond->longitude = $request->longitude;
                $patientSecond->reason = $request->reason;
                if($request->has('test_name')){
                    $patientSecond->test_name=$request->test_name;
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
                // $data=PatientRequest::with('detail', 'patient')
                // ->where('id','=',$patientSecond->id)
                // ->first();
                // Log::info('email start');
                // $mail = self::sendmail($data);
                // log::info('email send success'.$mail);
                // log::info('email end');
            } else {
                $patientSecond = new PatientRequest();

                $patientSecond->user_id = $request->user_id;
                $patientSecond->type_id = $request->type_id;
                $patientSecond->latitude = $request->latitude;
                $patientSecond->longitude = $request->longitude;
                $patientSecond->reason = $request->reason;
                if($request->has('test_name')){
                    $patientSecond->test_name=$request->test_name;
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
                
                // $data=PatientRequest::with('detail', 'patient')
                // ->where('id','=',$patientSecond->id)
                // ->first();
                // Log::info('email start');
                // $mail = self::sendmail($data);
                // log::info('email send success'.$mail);
                // log::info('email end');
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
                    ->where('is_available','=','1')->get();
                            
                }else {
                   $clinicianIds = User::where('designation_id','=',$request->type_id)->where('is_available','=','1')->get(); 
                }
                $markers = collect($clinicianIds)->map(function($item) use ($request){
                    $roadlController = new RoadlController();
                    $item['distance'] = $roadlController->calculateDistanceBetweenTwoAddresses($item->latitude, $item->longitude, $request->latitude,$request->longitude);
                    return $item;
                })
                // ->where('distance','<=',20)
                    ->pluck('id');
                $clinicianList = User::with('demographic')->whereIn('id',$markers)->get();
                // $clinicianList = User::where('designation_id','=',$request->type_id)->where('is_available','=','1')->get();

                $data=PatientRequest::with('detail')
                    ->where('id','=',$patientSecond->id)
                    ->first();
                event(new SendClinicianPatientRequestNotification($data,$clinicianList));

                NotificationHistory::create([
                    'type' => 'RoadL Request',
                    'sender_id' => $data->user_id,
                    'request_id' => $data->id,
                    'model_type' => 'PatientRequest',
                ]);
                return $this->generateResponse(true,'Add Request Successfully!',array('parent_id'=>$parent_id),200);
                // if ($request->has('type')){
                // foreach ($request->type as $value) {
                // $response = $this->createPatientRequest($request,$value);
                // }
                // }else{
                // $response = $this->createPatientRequest($request);
                // }
                // return $response;
            }else {
                $clinicianList = User::with('demographic')->where('id',$checkAssignId)->get();
                $data=PatientRequest::with('detail')
                    ->where('id','=',$patientSecond->id)
                    ->first();
                event(new SendClinicianPatientRequestNotification($data,$clinicianList));

                NotificationHistory::create([
                    'type' => 'RoadL Request',
                    'sender_id' => $data->user_id,
                    'request_id' => $data->id,
                    'model_type' => 'PatientRequest',
                ]);
                return $this->generateResponse(true,'Add Request Successfully!',array('parent_id'=>$parent_id),200);
            }
            
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

                $clinicianList = User::with('demographic')->whereHas('roles',function ($q) use ($request,$type){
                    $q->where('name','=',$type);
                })->where('is_available','=','1')->get();

                $data=PatientRequest::with('detail')
                    ->where('id','=',$patient->id)
                    ->first();
                event(new SendClinicianPatientRequestNotification($data,$clinicianList));
            }else{
                $clinicianList = User::with('demographic')->whereHas('roles',function ($q){
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
        $patient = PatientRequest::find($request->request_id);
        if ($patient){
            if(null!==$patient->clincial_id){
                return $this->generateResponse(false,'Request Already Accepted!',null,200);
            }
            $patient->clincial_id=$request->user_id;
            $patient->updated_at=Carbon::now()->toDateTime();
            $patient->status='2';
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

                //                $assignAppointemntRoadl = AssignAppointmentRoadl::where([
                //                    'patient_request_id'=>$patient->id
                //                ])->first();
                //                if ($assignAppointemntRoadl){
                //                    $patient->clinician = AssignAppointmentRoadl::where([
                //                        'appointment_id'=>$assignAppointemntRoadl->appointment_id
                //                    ])->with('requests',function ($q){
                //                          $q->select('id','clincial_id','latitude','longitude','reason','is_active','dieses','symptoms','is_parking','status');
                //                        })
                //                        ->select('appointment_id','patient_request_id','referral_type')
                //                        ->get()->toArray();
                //                    $patient->type = 1;
                //                }else{
                //                    $patient->clinician = $users;
                //                    $patient->type = 0;
                //                }
                $patient->clinician = $users;
                event(new SendPatientNotificationMap($patient->toArray(),$patient->user_id));
                event(new SendPatientNotificationMap($patient->toArray(),$patient->clincial_id));

                $data=PatientRequest::with('detail', 'patient')
                    ->where('id','=',$request->request_id)
                    ->first();


                $roadlController = new RoadlController();
                $distance = $roadlController->calculateDistanceBetweenTwoAddresses($patient->latitude, $patient->longitude, $request->latitude,$request->longitude);
                
                if ($data->patient && $data->patient->email) {
                    $clinicianFirstName = ($data->detail->first_name) ? $data->detail->first_name : '';
                    $clinicianLastName = ($data->detail->first_name) ? $data->detail->first_name : '';
                    $details = [
                        'first_name' => ($data->patient->first_name) ? $data->patient->first_name : '' ,
                        'last_name' => ($data->patient->last_name) ? $data->patient->last_name : '',
                        'status' => 'Accepted',
                        'message' => 'Your request has been accepted by ' . $clinicianFirstName . ' ' . $clinicianLastName. ', and will be arriving within ' . $distance . ' minutes',
                    ];
                    Mail::to($data->patient->email)->send(new UpdateStatusNotification($details));
                }

                if ($data->detail && $data->detail->email) {
                    $patientFirstName = ($data->patient->first_name) ? $data->patient->first_name : '';
                    $patientLastName = ($data->patient->first_name) ? $data->patient->first_name : '';
                    $details = [
                        'first_name' => ($data->detail->first_name) ? $data->detail->first_name : '' ,
                        'last_name' => ($data->detail->last_name) ? $data->detail->last_name : '',
                        'status' => 'Accepted',
                        'message' => 'You have accepted' . $patientFirstName . ' ' . $patientLastName .'RoadL request and you have to reach the patients house within ' . $distance . ' minutes',
                    ];
                    Mail::to($data->detail->email)->send(new UpdateStatusNotification($details));
                }

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
                ->orderBy('id','asc')
                ->get();
               
        } elseif (Auth::user()->is_available==='2'){
            $patientRequestList = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
                ->where('clincial_id','=',Auth::user()->id)
                ->whereNotNull('parent_id')
                ->where('type_id',$role)
                ->whereIn('status', $status)
                ->groupBy('parent_id')
                ->orderBy('id','asc')
                ->get();
        } else {
            if($status[0] == 1) {
                $patientRequestList = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
                    ->whereIn('status',$status)
                    ->whereNotNull('parent_id')
                    ->where('type_id',$role)
                    ->groupBy('parent_id')
                    ->orderBy('id','asc')
                    ->get();
                if($role == 4 && $designation !='') {
                    $directClinicins = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
                        ->whereIn('status',$status)
                        ->where('clincial_id','=',Auth::user()->id)
                        ->whereNotNull('parent_id')
                        ->where('type_id',$designation)
                        ->groupBy('parent_id')
                        ->orderBy('id','asc')
                        ->get();
                    if(count($directClinicins)>0) {
                        $patientRequestList = $directClinicins;
                    }
                }
             
            } else if($status[0] == 4) {
                $patientRequestList = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm'])
                   ->where('clincial_id','=',Auth::user()->id)
                   ->whereNotNull('parent_id')
                   ->whereIn('status', ['4'])
                   ->groupBy('parent_id')
                   ->orderBy('id','asc')
                   ->get();
            } else {
                $patientRequestList = PatientRequest::with(['requests','detail','patient','requestType','patientDetail','ccrm','patientDetail.demographic'])
                ->where('clincial_id','=',Auth::user()->id)
                ->whereNotNull('parent_id')
                ->whereIn('status', ['2','3','6','7'])
                ->groupBy('parent_id')
                ->orderBy('id','asc')
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
            //                ->orderBy('id','asc')
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
        if ($request->has('patient_id')){
            $vendorList = collect($vendorList)->map(function ($row) use ($request){
                $check = PatientRequest::where('user_id', $request->patient_id)
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

        $clinicianList = User::where([['designation_id','=',$request->role_id], ['status','=','1'], ['is_available','=','1']])->get();
        return $this->generateResponse(true,'Clinician List APi',$clinicianList,200);
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
                ->first();

            return $this->generateResponse(true, 'Fetched parent id', $parent, 200);
        } catch (\Exception $ex) {
            return $this->generateResponse(false, $ex->getMessage());
        }
    }

    public function updatePatientRequeststatus(Request $request)
    {
        try {
           
            $patientRequstModel = PatientRequest::where('id',$request['patient_request_id'])->with('patient', 'detail')->first();
            PatientRequest::find($request['patient_request_id'])->update([
                'status' => '4'
            ]);
        
            $patientRequstModel->detail()->update([
                'is_available' => 1,
            ]);

            $patientRequest = PatientRequest::where([['parent_id', $request['parent_id']],['status', '!=', 4]])->get();
            if(count($patientRequest) == 0) {
                PatientRequest::find($request['parent_id'])->update([
                    'status' => '4'
                ]);
            };

            // if ($patientRequstModel->patient && $patientRequstModel->patient->email) {
            //     $clinicianFirstName = ($patientRequstModel->detail->first_name) ? $patientRequstModel->detail->first_name : '';
            //     $clinicianLastName = ($patientRequstModel->detail->first_name) ? $patientRequstModel->detail->first_name : '';
            //     $details = [
            //         'first_name' => ($patientRequstModel->patient->first_name) ? $patientRequstModel->patient->first_name : '' ,
            //         'last_name' => ($patientRequstModel->patient->last_name) ? $patientRequstModel->patient->last_name : '',
            //         'status' => 'Completed',
            //         'message' => 'Your request has been accepted by ' . $clinicianFirstName . ' ' . $clinicianLastName. ', and will be arriving within 30 minutes',
            //     ];
            //     Mail::to($patientRequstModel->patient->email)->send(new UpdateStatusNotification($details));
            // }

            // if ($patientRequstModel->detail && $patientRequstModel->detail->email) {
            //     $patientFirstName = ($patientRequstModel->patient->first_name) ? $patientRequstModel->patient->first_name : '';
            //     $patientLastName = ($patientRequstModel->patient->first_name) ? $patientRequstModel->patient->first_name : '';
            //     $details = [
            //         'first_name' => ($patientRequstModel->detail->first_name) ? $patientRequstModel->detail->first_name : '' ,
            //         'last_name' => ($patientRequstModel->detail->last_name) ? $patientRequstModel->detail->last_name : '',
            //         'status' => 'Completed',
            //         'message' => 'You have accepted' . $patientFirstName . ' ' . $patientLastName .'RoadL request and you have to reach the patients house within 20 minutes',
            //     ];
            //     Mail::to($patientRequstModel->detail->email)->send(new UpdateStatusNotification($details));
            // }

            return $this->generateResponse(true, 'Status complated successfully', null, 200);
        } catch (\Exception $ex) {
            return $this->generateResponse(false, $ex->getMessage());
        }
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

    public static function sendmail($data)
    {
        // $roadlController = new RoadlController();
        // $distance = $roadlController->calculateDistanceBetweenTwoAddresses($patient->latitude, $patient->longitude, $request->latitude,$request->longitude);
        
        if ($data->patient && $data->patient->email) {
            log::info($data->patient->email);
            $clinicianFirstName = ($data->detail->first_name) ? $data->detail->first_name : '';
            $clinicianLastName = ($data->detail->first_name) ? $data->detail->first_name : '';
            $details = [
                'first_name' => ($data->patient->first_name) ? $data->patient->first_name : '' ,
                'last_name' => ($data->patient->last_name) ? $data->patient->last_name : '',
                'status' => 'Accepted',
                'message' => 'Your request has been accepted by ' . $clinicianFirstName . ' ' . $clinicianLastName. ', and will be arriving within minutes',
            ];
            Mail::to($data->patient->email)->send(new UpdateStatusNotification($details));
        }

        if ($data->detail && $data->detail->email) {
            log::info($data->detail->email);
            $patientFirstName = ($data->patient->first_name) ? $data->patient->first_name : '';
            $patientLastName = ($data->patient->first_name) ? $data->patient->first_name : '';
            $details = [
                'first_name' => ($data->detail->first_name) ? $data->detail->first_name : '' ,
                'last_name' => ($data->detail->last_name) ? $data->detail->last_name : '',
                'status' => 'Accepted',
                'message' => 'You have accepted' . $patientFirstName . ' ' . $patientLastName .'RoadL request and you have to reach the patients house within minutes',
            ];
            Mail::to($data->detail->email)->send(new UpdateStatusNotification($details));
        }
    }
}
