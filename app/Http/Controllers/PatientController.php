<?php

namespace App\Http\Controllers;

use App\Events\SendingSMS;
use App\Http\Requests\RoadlSelectedDiesesRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\PatientReferral;
use App\Models\PatientRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PatientController extends Controller
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
     * Display the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function show(Patient $patient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Patient $patient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient)
    {
        //
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
            $patientRequest->status='active';
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

    public function scheduleAppoimentList(Request $request){
        // patient referral pending status patient list
        $appointmentList = Appointment::with(['bookedDetails' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
            ->with(['patients','meeting','service','filetype'])
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

    public function changePatientStatus(Request $request){
        $this->validate($request,[
            'id'=>'required',
            'status'=>'required'
        ]);
        $status='accept';
        if ($request->status==0){
            $status='reject';
        }

        $updatePatient = PatientReferral::whereIn('id',$request->id)->update(['status'=>$status]);

        $ids = $request->id;

        if (count($ids)>0){
            $message='';
            $smsData=array();
            foreach ($ids as $id) {
                $patient = PatientReferral::find($id);
                if ($patient){
                    $patient->status = $status;
                    if ($status==="accept") {
                        $users = User::find($patient->user_id);
                        if ($users){
                            $users->status = '1';
                            $users->save();

                            $smsData[]=array(
                                'to'=>$users->phone,
                                'message'=>'Welcome To Doral Health Connect.
Please click below application link and download.
'.url("application/android/patientDoral.apk").'
Default Password : doral@123',
                            );
                        }
                    }
                    $patient->save();
                    $message='Change Patient Status Successfully';
                }
            }
            event(new SendingSMS($smsData));
            return $this->generateResponse(true,$message,null,200);
        }
        return $this->generateResponse(false,'No Patient Referral Ids Found',null,422);
    }
}
