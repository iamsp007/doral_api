<?php

namespace App\Http\Controllers;

use App\Models\Caregivers;
use App\Models\PatientInsurance;
use App\Models\PatientReferral;
use App\Models\UploadDocuments;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CCMReading;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PatientController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $employeeContoller, $patientController;
    public function __construct(EmployeeController $employeeContoller, PatientController $patientController)
    {
        $this->employeeContoller = $employeeContoller;
        $this->patientController = $patientController;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try {
            $users = User::all();
            if (!$users) {
                throw new Exception("No Users found into database");
            }
            $data = [
                'users' => $users
            ];
            $status = true;
            $message = "Compnay information";
            return response()->json([$status, $message, $data]);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
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
            $data = $request;
            dd($data);
            \DB::beginTransaction();
            $id = User::insert($data);
            if ($id) {
                $request['user_id'] = $id;
                if ($request['type'] == 'employee' || $request['type'] == 'admin') {
                    unset($request['type']);
                    $result = $this->employeeContoller->store($request);
                } else if ($request['type'] == 'patient') {
                    unset($request['type']);
                    $result = $this->patientController->store($request);
                }
                // Check the condition if error into database
                if (!$result) {
                    throw new \ErrorException('Error in-Insert');
                }
                \DB::commit();
                $resp = [
                    'user' => $result
                ];
                $status = true;
                $message = "Employee Added Successfully information";
                return $this->generateResponse($status, $message,$resp);
            } else {
                throw new \ErrorException('Error found');
            }
        } catch (\Exception $e) {
            \DB::rollback();
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $resp);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return User::find($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $User = User::findOrFail($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $User = User::findOrFail($user);
        $User->update($request->all());

        return response()->json($User, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $User = User::findOrFail($user);
        $User->delete();

        return response()->json(null, 204);
    }

    public function changeAvailability(Request $request)
    {
        try {
            $user = auth()->user();
            $user->is_available = $request->is_available;
            $user->save();
            $status = true;
            $message = "Availability changed";
            return $this->generateResponse($status, $message, $user, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $user);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $status = false;
        $user = [];
        try {
            $login = json_decode($request->getContent(), true);
            $data = $login['data'];
            $password = $data['password'];
            $user = User::login($data);
            // Check user exist into database or not
            if (!$user) {
                return response()->json(['status' => $status, 'message' => 'Login Fail, please check email id']);
            }
            // Check user password
            if (!Hash::check($password, $user->password)) {
                return response()->json(['status' => $status, 'message' => 'Login Fail, pls check password']);
            }
            $user = [
                'users' => $user
            ];
            $status = true;
            $message = "Employee Added Successfully information";
            return response()->json(['status' => $status, $user]);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $user);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ccmReadings(Request $request)
    {
        $status = false;
        $data = null;
        $message = "CCM Reading are not available.";
        try {
            $response = $request->user()->ccm;
            if (!$response) {
                throw new Exception($message);
            }
            $status = true;
            $message = "All CCM Readings.";
            return $this->generateResponse($status, $message, $response, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }

    public function documentVerification(Request $request)
    {
        $validator = Validator::make($request->all(),[
           'id_proof'=>'required|max:10000|mimes:pdf,xls,png,jpg,jpeg',
           'degree_proof'=>'required|max:10000|mimes:pdf,xls,png,jpg,jpeg',
           'medical_report'=>'required|max:10000|mimes:pdf,xls,png,jpg,jpeg',
           'insurance_report'=>'required|max:10000|mimes:pdf,xls,png,jpg,jpeg',
        ]);

        if ($validator->fails()){
            return $this->generateResponse(false,'Invalid Parameter!',$validator->errors(),200);
        }

        try {
            if($request->file('id_proof')){
                $uploadFolder = 'documents/'.auth()->user()->id.'/id_proof';
                $file = $request->file('id_proof');
                $file_uploaded_path = $file->store($uploadFolder, 'public');
                $uploadedFileResponse = [
                    "file_name" => basename($file_uploaded_path),
                    "file_url" => \Storage::disk('public')->url($file_uploaded_path),
                    "mime" => $file->getClientMimeType()
                ];
                $documents = UploadDocuments::where(['user_id'=>Auth::user()->id,'type'=>'1'])->first();
                if ($documents===null){
                    $documents = new UploadDocuments();
                }
                $documents->user_id = Auth::user()->id;
                $documents->file_name = $uploadedFileResponse['file_name'];
                $documents->type = '1';
                $documents->save();
            }

            if($request->file('degree_proof')){
                $uploadFolder = 'documents/'.auth()->user()->id.'/degree_proof';
                $file = $request->file('degree_proof');
                $file_uploaded_path = $file->store($uploadFolder, 'public');
                $uploadedFileResponse = [
                    "file_name" => basename($file_uploaded_path),
                    "file_url" => \Storage::disk('public')->url($file_uploaded_path),
                    "mime" => $file->getClientMimeType()
                ];
                $documents = UploadDocuments::where(['user_id'=>Auth::user()->id,'type'=>'2'])->first();
                if ($documents===null){
                    $documents = new UploadDocuments();
                }
                $documents->user_id = Auth::user()->id;
                $documents->file_name = $uploadedFileResponse['file_name'];
                $documents->type = '2';
                $documents->save();
            }

            if($request->file('medical_report')){
                $uploadFolder = 'documents/'.auth()->user()->id.'/medical_report';
                $file = $request->file('medical_report');
                $file_uploaded_path = $file->store($uploadFolder, 'public');
                $uploadedFileResponse = [
                    "file_name" => basename($file_uploaded_path),
                    "file_url" => \Storage::disk('public')->url($file_uploaded_path),
                    "mime" => $file->getClientMimeType()
                ];
                $documents = UploadDocuments::where(['user_id'=>Auth::user()->id,'type'=>'3'])->first();
                if ($documents===null){
                    $documents = new UploadDocuments();
                }
                $documents->user_id = Auth::user()->id;
                $documents->file_name = $uploadedFileResponse['file_name'];
                $documents->type = '3';
                $documents->save();
            }

            if($files=$request->file('insurance_report')){
                $uploadFolder = 'documents/'.auth()->user()->id.'/insurance_report';
                $file = $request->file('insurance_report');
                $file_uploaded_path = $file->store($uploadFolder, 'public');
                $uploadedFileResponse = [
                    "file_name" => basename($file_uploaded_path),
                    "file_url" => \Storage::disk('public')->url($file_uploaded_path),
                    "mime" => $file->getClientMimeType()
                ];
                $documents = UploadDocuments::where(['user_id'=>Auth::user()->id,'type'=>'4'])->first();
                if ($documents===null){
                    $documents = new UploadDocuments();
                }
                $documents->user_id = Auth::user()->id;
                $documents->file_name = $uploadedFileResponse['file_name'];
                $documents->type = '4';
                $documents->save();
            }

            return $this->generateResponse(true,'Document Upload Successfully!',null,200);
        }catch (\Exception $exception){

            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }

    public function getDocuments()
    {
        try {
            $documents = UploadDocuments::where('user_id', Auth::user()->id)->get();
            return $this->generateResponse(true, 'All Documents', $documents, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    public function removeDocument(Request $request)
    {
        try {
            $documents = UploadDocuments::where([
                'id' => $request->id,
                'user_id' => Auth::user()->id
            ])->delete();
            return $this->generateResponse(true, 'Document removed', $documents, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    public function getPatientDetail(Request $request,$patient_id)
    {
        $details = User::with('detail','leave','ccm','insurance','caseManager','primaryPhysician','specialistPhysician','caregiverHistory','caregivers')
            ->find($patient_id);
        if ($details){
            return $this->generateResponse(true,'Show Patient Detail Successfully!',$details,200);
        }
        return $this->generateResponse(false,'Patient Id Does not Exists',null,200);
    }

    public function demographyDataUpdate(Request $request)
    {
        if ($request->type==="1"){
            $demographyDetails = PatientReferral::where(['user_id'=>$request->patient_id])->first();
            if ($demographyDetails){
                $demographyDetails->phone1 = $request->phoneno;
                $demographyDetails->email = $request->emailId;
                $demographyDetails->start_date = $request->start_date;
//            $demographyDetails->ethnicity = $request->ethnicity;
                $demographyDetails->ssn = $request->SSN;
                $demographyDetails->patient_id = $request->admissionId;
                $demographyDetails->address_1 = $request->address;
                $demographyDetails->eng_name = $request->eng_name;
                $demographyDetails->eng_addres = $request->eng_address;
                $demographyDetails->emg_phone = $request->emg_phone;
                $demographyDetails->emg_relationship = $request->emg_relationship;
                $demographyDetails->work_name = $request->work_name;
                $demographyDetails->home_phone1 = $request->home_phone1;
                $demographyDetails->cell_phone1 = $request->cell_phone1;
                $demographyDetails->work_phone3 = $request->work_phone3;
//            $demographyDetails->nurse = $request->nurse;
                $demographyDetails->save();
                return $this->generateResponse(true,'Update Details Success',$demographyDetails,200);
            }
        }elseif ($request->type==="2"){
            $demographyDetails = PatientReferral::where(['user_id'=>$request->patient_id])->first();
            if ($demographyDetails){
                $demographyDetails->medicaid_number = $request->medicaid_number;
                $demographyDetails->medicare_number = $request->medicare_number;
                $demographyDetails->save();
                if (is_array($request->insurance_id)){
                    foreach ($request->insurance_id as $key=>$value) {
                        $patientInsurance = PatientInsurance::find($value);
                        if ($patientInsurance){
                            if ($request->has('name_'.$key)){
                                $patientInsurance->name = $request->input('name_'.$key);
                            }
                            if ($request->has('payerId_'.$key)){
                                $patientInsurance->payer_id = $request->input('payerId_'.$key);
                            }
                            if ($request->has('policy_no_'.$key)){
                                $patientInsurance->policy_no = $request->input('policy_no_'.$key);
                            }
                            if ($request->has('Phone_'.$key)){
                                $patientInsurance->phone = $request->input('Phone_'.$key);
                            }
                            $patientInsurance->save();
                        }
                    }
                }
                return $this->generateResponse(true,'Insurance Update Details Success',$request->input('payerId_'.$key),200);
            }
        }elseif ($request->type==="3"){
            if ($request->has('caregiver_id')){
                $caregivers = Caregivers::where('id','=',$request->caregiver_id)
                    ->where('patient_id','=',$request->patient_id)
                    ->first();
                if ($caregivers){
                    $caregivers->name = $request->c_name;
                    $caregivers->phone = $request->c_phone;
                    $caregivers->start_time = $request->start_time;
                    $caregivers->end_time = $request->end_time;
                    $caregivers->save();
                }
            }

            return $this->generateResponse(true,'Caregiver Detail Update Successfully!',$caregivers,200);
        }

        return $this->generateResponse(false,'Something Went Wrong',null,200);
    }
}
