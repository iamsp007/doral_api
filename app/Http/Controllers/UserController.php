<?php

namespace App\Http\Controllers;

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

    public function documentVerification(Request $request){
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
            if($files=$request->file('id_proof')){
                $name='id_proof_'.time().'.'.$files->getClientOriginalExtension();
                 $files->move('document',$name);
                $documents = UploadDocuments::where(['user_id'=>Auth::user()->id,'type'=>'1'])->first();
                if ($documents===null){
                    $documents = new UploadDocuments();
                }
                $documents->user_id = Auth::user()->id;
                $documents->file_name = $name;
                $documents->type = '1';
                $documents->save();
            }

            if($files=$request->file('degree_proof')){
                $name='degree_proof_'.time().'.'.$files->getClientOriginalExtension();
                 $files->move('document',$name);
                $documents = UploadDocuments::where(['user_id'=>Auth::user()->id,'type'=>'2'])->first();
                if ($documents===null){
                    $documents = new UploadDocuments();
                }
                $documents->user_id = Auth::user()->id;
                $documents->file_name = $name;
                $documents->type = '2';
                $documents->save();
            }

            if($files=$request->file('medical_report')){
                $name='medical_report_'.time().'.'.$files->getClientOriginalExtension();
                 $files->move('document',$name);
                $documents = UploadDocuments::where(['user_id'=>Auth::user()->id,'type'=>'3'])->first();
                if ($documents===null){
                    $documents = new UploadDocuments();
                }
                $documents->user_id = Auth::user()->id;
                $documents->file_name = $name;
                $documents->type = '3';
                $documents->save();
            }

            if($files=$request->file('insurance_report')){
                $name='insurance_report_'.time().'.'.$files->getClientOriginalExtension();
                 $files->move('document',$name);
                $documents = UploadDocuments::where(['user_id'=>Auth::user()->id,'type'=>'4'])->first();
                if ($documents===null){
                    $documents = new UploadDocuments();
                }
                $documents->user_id = Auth::user()->id;
                $documents->file_name = $name;
                $documents->type = '4';
                $documents->save();
            }

            return $this->generateResponse(true,'Document Upload Successfully!',null,200);
        }catch (\Exception $exception){

            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }

    public function getPatientDetail(Request $request,$patient_id){
        $details = User::with('detail','leave','ccm')->find($patient_id);
        if ($details){
            return $this->generateResponse(true,'Show Patient Detail Successfully!',$details,200);
        }
        return $this->generateResponse(false,'Patient Id Does not Exists',null,200);
    }
}
