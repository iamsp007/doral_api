<?php

namespace App\Http\Controllers;

use App\Events\SendingSMS;
use App\Jobs\SendEmailJob;
use App\Mail\AcceptedMail;
use App\Models\Appointment;
use App\Models\Demographic;
use App\Models\Patient;
use App\Models\PatientReferral;
use App\Models\PatientRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    /**
     * Search patient by name / Email / phone
     *
     * @return \Illuminate\Http\Response
     */
    public function getAppoinment($keyword)
    {
        $status = false;
        $data = [];
        $message = "";
        try {
            $res = Patient::searchByEmailNamePhone($keyword);
            dd($res);
            $status = true;
            $message = $res['message'];
            $data = [
                'data' => $res['data']
            ];
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getFile(). $e->getMessage(). $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
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
    public function store($request)
    {
        try {
            $data = PatientReferral::insert($request);
            return $data;
        } catch (\Exception $e) {
            \Log::error($e);
            $status = false;
            $message = $e->getMessage(). $e->getLine();
            return $this->generateResponse($status, $message, null);
        }
    }

    /**
     * StoreInformation is store pateint detailed information based on different steps
     * This services is call from App only. There is not web API for patent registation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeInfomation($step, Request $request)
    {
        $status = false;
        $resp = null;
        if ($step == 1) {
            $request->validate([
                'ssn' => 'required',
                'medicaid_number' => 'numeric',
                'medicare_number' => 'numeric',
                'address_1' => 'required',
                'address_2' => 'required',
                'Zip' => 'required',
                'service_id' => 'required|numeric'
            ]);
        }

        try {
            $request = $request->all();
            if (!$step) {
                throw new Exception("Invalid parameter are required");
            }
            if (!$request['id']) {
                throw new Exception("Invalid parameter Id are required");
            }
            $id = $request['id'];
            unset($request['id']);
            $patient = PatientReferral::with('user')->where('user_id', $id)->first();
            if (!$patient) {
                throw new Exception("Patient are not found into database");
            }
            switch ($step) {
                case '1':
                    $id = $patient->id;
                    $data = Patient::updatePatient($id, $request);
                    if ($data) {
                        $status = true;
                        $message = "Patient information saved Successfully";
                        return $this->generateResponse($status, $message, $resp);
                    }
                    break;
                case '2': // Insert services
                    $id = $patient->id;
                    $data = Patient::updateServices($id, $request);
                    if ($data) {
                        $status = true;
                        $message = "Patient serives saved Successfully";
                        return $this->generateResponse($status, $message, $resp);
                    }
                    break;
                case '3': // Insert Insurance
                    $id = $patient->id;
                    $data = Patient::updateInsurance($id, $request);
                    if ($data) {
                        $user = $patient->user;
                        $user->profile_verified_at = date('Y-m-d H:i:s');
                        $user->save();
                        $status = true;
                        $message = "Patient Insurance saved Successfully";
                        return $this->generateResponse($status, $message, $resp);
                    }
                    break;
                default:
                    throw new Exception("Invalid Parameters");
                    break;
            }
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage(). $e->getLine();
            return $this->generateResponse($status, $message, $resp);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientDieses  $patient
     * @return \Illuminate\Http\Response
     */
    public function roadlSelectedDisease(Request $request)
    {
        $patientRequest = PatientRequest::find($request->patient_request_id);
        if ($patientRequest){
            $patientRequest->dieses=$request->dieses;
            $patientRequest->symptoms=$request->symptoms;
            $patientRequest->is_parking=$request->is_parking;
            
            $patientRequest->save();
            return $this->generateResponse(true, 'Detail Update Successfully!', $patientRequest,200);
        }
        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }

    public function getPatientList(Request $request){
        // user table active patient list
        $patientList = PatientReferral::with('detail','service','filetype')
            ->whereHas('detail',function ($q){
                $q->where('status','=','1');
            })
            ->get();
        return $this->generateResponse(true,'get patient list',$patientList,200);
    }

    public function getNewPatientList(Request $request){
        // patient referral pending status patient list
        $patientList = PatientReferral::with('detail','service','filetype')
            ->where('first_name','!=',null)
            ->where('status','=','pending')
            ->get();
        //dd($patientList);
        return $this->generateResponse(true,'get new patient list',$patientList,200);
    }

//    public function getNewPatientListForAppointment(Request $request){
//        // patient referral accept status patient list
//        $patientList = PatientReferral::with('detail','service','filetype')
//            ->where('status','=','accept')
//            ->get();
//        return $this->generateResponse(true,'get new patient list',$patientList,200);
//    }

    public function scheduleAppoimentList(Request $request){
        // patient referral pending status patient list
        $appointmentList = Appointment::with(['bookedDetails' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
            ->with(['patients','meeting','service','filetype','roadl'])
            ->with(['provider1Details' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
            ->with(['provider2Details' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
            ->whereDate('start_datetime','>=',Carbon::now()->format('Y-m-d'))
            ->orderBy('start_datetime','asc')
            ->get()->toArray();
        return $this->generateResponse(true,'get schedule patient list',$appointmentList,200);
    }

    public function cancelAppoimentList(Request $request){
        // patient referral pending status patient list
        $appointmentList = Appointment::with(['bookedDetails' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
            ->with(['patients','cancelAppointmentReasons','service','filetype','cancelByUser'])
            ->with(['provider1Details' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
            ->with(['provider2Details' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
            ->where('status','=','cancel')
            ->orderBy('start_datetime','desc')
            ->get()->toArray();
        return $this->generateResponse(true,'get schedule patient list',$appointmentList,200);
    }

    public function getNewPatientListForAppointment(Request $request){
        // patient referral pending status patient list
        $patientList = PatientReferral::with('detail','service','filetype')
            ->where('first_name','!=',null)
            ->where('status','=','accept')
            ->get();
        return $this->generateResponse(true,'get new patient list',$patientList,200);
    }

    public function updatePatientPhone(Request $request)
    {
        $input = $request->all();
        $users = User::whereNotNull('phone')->where('phone', $request['phone'])->first();

        if ($users) {
            return $this->generateResponse(false, 'Phone number must unique', null, 400);
        }

        $user = User::where('id',$request['id'])->update([
            'status' => '0',
            'phone' => $request['phone'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
        ]);
        if ($user) {
            $user_id = DB::getPdo()->lastInsertId();
            $ssn = str_replace("-","",$input['ssn']);
            Demographic::where('user_id' ,$user_id)->update([
                'ssn' => $ssn,
                // 'address->City' => $request['city'],
                // 'address->State' => $request['state'],
            ]);
            return $this->generateResponse(true, 'Change Patient phone Successfully.', null, 200);
        }
        return $this->generateResponse(false, 'Patient Not Found', null, 400);
    }
    public function updatePatientStatus(Request $request)
    {
        $input = $request->all();
        $status = $input['status'];
        $ids = $input['id'];

        $statusData = '1';
        if ($status === '3') {
            $statusData = '3' ;
        }
        $users = User::whereIn('id',$ids);
        $user = $users->update(['status' => $statusData]);

        if ($user) {
            foreach ($users as $value) {
                if ($value->phone) {
                    $link=env("WEB_URL").'download-application';
                    $smsData[] = [
                        'to'=> $value->phone,
                        'message'=>'Congratulation! Your employer Housecalls home care has been enrolled to benefit plan where each employees will get certain medical facilities. If you have any medical concern or need annual physical please click on the link below and book your appointment now.
                        '.$link.'
                        Default Password : Patient@doral',
                    ];
                    
                    event(new SendingSMS($smsData));
                }
                if ($value->email) {
                    $password = Str::random(8);
                    
                    $user->update(['password' => setPassword($password)]);
                    if ($statusData === '1') {
                        $first_name = ($value->first_name) ? $value->first_name : '';
                        $last_name = ($value->last_name) ? $value->last_name : '';
                        $details = [
                            'name' => $first_name . ' ' . $last_name,
                            'password' => $password,
                            'email' => $value->email,
                            'login_url' => route('login'),
                        ];
                    
                        SendEmailJob::dispatch($value->email,$details,'AcceptedMail');
                    }
                }
            }
            
            return $this->generateResponse(true, 'Change Status Successfully.', null, 200);
        }

        return $this->generateResponse(false, 'Detail not Found', null, 400);
    }

    public function changePatientStatus(Request $request){
        $this->validate($request,[
            'id'=>'required',
            'status'=>'required'
        ]);
        $status='accept';
        if ($request->status==0){
            $status='reject';
        }

        $message='';
        $smsData=array();
        $patients = PatientReferral::whereIn('user_id',$request->id)->get();
        if (count($patients)>0){
            foreach ($patients as $value) {
                if ($status==="accept"){
                    $users = User::find($value->user_id);
                    if ($users){
                        $users->status = '1';
                        $users->save();
                        $link=env("WEB_URL").'download-application';
                        $smsData[]=array(
                            'to'=>$users->phone,
                            'message'=>'Congratulation! Your employer Housecalls home care has been enrolled to benefit plan where each employees will get certain medical facilities.
                            If you have any medical concern or need annual physical please click on the link below and book your appointment now.'.$link.'
Default Password : Patient@doral',
                        );
                    }
                }
                $patient = PatientReferral::find($value->id);
                if ($patient){
                    $patient->status = $status;
                    $patient->save();
                }
            }
            $message='Change Patient Status Successfully';
            event(new SendingSMS($smsData));
            return $this->generateResponse(true,$message,null,200);
        }
        return $this->generateResponse(false,'No Patient Referral Ids Found',null,422);
    }

    public function newpatientData(Request $request) {

         $requestData = $request->all();

         $patientList = User::with('patientDetail','roles')
            ->whereHas('roles',function ($q){
                $q->where('name','=','patient');
            })
            ->whereHas('patientDetail',function ($q){
                $q->where('status','=','pending')->whereNotNull('first_name');
            })
            ->where(DB::raw('concat(first_name," ",last_name)'), 'like', '%'.$requestData['searchTerm'].'%')
            ->get();
        return $this->generateResponse(true,'get new patient list',$patientList,200);
    }

    public function patientData(Request $request) {
         $requestData = $request->all();
          $patientList = User::with('patientDetail','roles')
            ->whereHas('roles',function ($q){
                $q->where('name','=','patient');
            })
            ->where('status','=','1')
             ->where(DB::raw('concat(first_name," ",last_name)'), 'like', '%'.$requestData['searchTerm'].'%')
            ->get();
        return $this->generateResponse(true,'get new patient list',$patientList,200);

    }

     public function scheduleAppoimentListData(Request $request){
        // patient referral pending status patient list
        $requestData = $request->all();
        $appointmentList = Appointment::with(['bookedDetails' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
            ->with(['meeting','service','filetype','roadl'])
            ->with(['patients' => function ($q) use($requestData) {
                $q->where(DB::raw('concat(first_name," ",last_name)'), 'like', '%'.$requestData['searchTerm'].'%');
            }])

            ->with(['provider1Details' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
            ->with(['provider2Details' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
            ->whereDate('start_datetime','>=',Carbon::now()->format('Y-m-d'))
            ->orderBy('start_datetime','asc')
            ->get()->toArray();
        return $this->generateResponse(true,'get schedule patient list',$appointmentList,200);
    }

      public function cancelAppoimentListData(Request $request){
        // patient referral pending status patient list
         $requestData = $request->all();
        $appointmentList = Appointment::with(['bookedDetails' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
            ->with(['cancelAppointmentReasons','service','filetype','cancelByUser'])
             ->with(['patients' => function ($q) use($requestData) {
                $q->where(DB::raw('concat(first_name," ",last_name)'), 'like', '%'.$requestData['searchTerm'].'%');
            }])
            ->with(['provider1Details' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
            ->with(['provider2Details' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
            ->where('status','=','cancel')
            ->orderBy('start_datetime','desc')
            ->get()->toArray();
        return $this->generateResponse(true,'get schedule patient list',$appointmentList,200);
    }

    public function calendarAppoimentListData(){
            // patient referral pending status patient list
            return $appointmentList = Appointment::select(DB::raw('count(*) as total'),DB::raw('DATE_FORMAT(start_datetime, "%Y-%m-%d") as start_datetime'),DB::raw('DATE_FORMAT(end_datetime, "%Y-%m-%d") as end_datetime'))->with(['bookedDetails' => function ($q) {
                    }])
                ->whereDate('start_datetime','>=',Carbon::now()->format('Y-m-d'))
                ->groupby('start_datetime','end_datetime')
                ->orderBy('start_datetime','asc')
                ->get()->toArray();
        }
}
