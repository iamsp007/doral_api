<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Demographic;
use App\Models\PatientEmergencyContact;
use Illuminate\Http\Request;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PatientController;
use App\Models\Company;
use App\Models\UserDevice;
use App\Models\UserDeviceLog;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    public function getPatientDetail($patient_id)
    {
        $details = User::with('detail','leave','ccm','insurance','caseManager','primaryPhysician','specialistPhysician','caregiverHistory','caregivers', 'caregiverInfo', 'demographic')->find($patient_id);
        if ($details){
            return $this->generateResponse(true,'Show Patient Detail Successfully!',$details,200);
        }
        return $this->generateResponse(false,'Patient Id Does not Exists',null,200);
    }

    public function demographyDataUpdate(Request $request)
    {
        $input = $request->all();
        
        if ($request->type==="1"){
            $parts = explode('-',$input['dob']);
            $yyyy_mm_dd = $parts[2] . '-' . $parts[0] . '-' . $parts[1];

            User::find($input['user_id'])->update([
                'gender' => $input['gender'],
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'dob' => $yyyy_mm_dd,
                'phone' => $input['home_phone'],
                'email' => $input['email'],
            ]);

            if (isset($input['notification']) && !empty($input['notification'])) {
                $notification = implode(',',$input['notification']);
            } else {
                $notification = '';
            }

            Demographic::where('user_id' ,$input['user_id'])->update([
                'ssn' => isset($input['ssn']) ? $input['ssn'] : '' ,
                'language' => isset($input['language']) ? $input['language'] : '' ,
                'address->address1' => isset($input['address1']) ? $input['address1'] : '' ,
                'address->address2' => isset($input['address2']) ? $input['address2'] : '' ,
                'address->city' => isset($input['city']) ? $input['city'] : '' ,
                'address->state' => isset($input['state']) ? $input['state'] : '' ,
                'address->zip_code' => isset($input['zip_code']) ? $input['zip_code'] : '' ,
                'address->apt_building' => isset($input['apt_building']) ? $input['apt_building'] : '' ,
                'ethnicity' => isset($input['ethnicity']) ? $input['ethnicity'] : '' ,
                'country_of_birth' => isset($input['country_of_birth']) ? $input['country_of_birth'] : '' ,
                'marital_status' => isset($input['marital_status']) ? $input['marital_status'] : '' ,
                // 'notification_preferences->email' => isset($input['email']) ? $input['email'] : '' ,
                // 'notification_preferences->method_name' => isset($input['method_name']) ? $input['method_name'] : '' ,
                // 'notification_preferences->mobile_or_sms' => isset($input['mobile_or_sms']) ? $input['mobile_or_sms'] : '' ,
                // 'notification_preferences->voice_message' => isset($input['voice_message']) ? $input['voice_message'] : '' ,
                'notification' => $notification,
            ]);

            $contactName = $input['contact_name'];
            $phone1 = $input['phone1'];
            $phone2 = $input['phone2'];
            $relation = $input['relationship_name'];
            // $address = $input['address'];
            // $emergencyAddress = [
            //     'apt_building' => $input['emergencyAptBuilding'],
            //     'address1' => $input['emergencyAddress1'],
            //     'address2' => $input['emergencyAddress2'],
            //     'city' => $input['emergencyAddress_city'],
            //     'state' => $input['emergencyAddress_state'],
            //     'zip_code' => $input['emergencyAddress_zip_code'],
            // ] ;

            $apt_building = $input['emergencyAptBuilding'];
            $address1 = $input['emergencyAddress1'];
            $address2 = $input['emergencyAddress2'];
            $city = $input['emergencyAddress_city'];
            $state = $input['emergencyAddress_state'];
            $zip_code = $input['emergencyAddress_zip_code'];
         
            PatientEmergencyContact::where('user_id', $input['user_id'])->delete();
            
            foreach ($contactName as $index => $value) {
                $emergencyAddress = [
                    'apt_building' => ($apt_building[$index]) ? $apt_building[$index] : '',
                    'address1' =>  ($address1[$index]) ? $address1[$index] : '',
                    'address2' =>  ($address2[$index]) ? $address2[$index] : '',
                    'city' => ($city[$index]) ? $city[$index] : '',
                    'state' => ($state[$index]) ? $state[$index] : '',
                    'zip_code' => ($zip_code[$index]) ? $zip_code[$index] : '',
                ];
               
                PatientEmergencyContact::create([
                    'user_id' => $input['user_id'],
                    'name' => ($contactName[$index]) ? $contactName[$index] : '',
                    'phone1' => ($phone1[$index]) ? $phone1[$index] : '',
                    'phone2' => ($phone2[$index]) ? $phone2[$index] : '',
                    'relation' => ($relation[$index]) ? $relation[$index] : '',
                    'address' => $emergencyAddress,
                ]);
            }

            return $this->generateResponse(true, 'Update Details Success', null, 200);
        } else if($request->type === "2") {
            Demographic::where('user_id' ,$input['user_id'])->update([
                'medicaid_number' => $input['medicaid_number'],
                'medicare_number' => $input['medicare_number'],
            ]);

            return $this->generateResponse(true, 'Update Details Success', $request->type, 200);
        } else if($request->type === "3") {
            $phone = preg_replace("/[^0-9]+/", "", $input['phone']);
            $administrator_phone_no = preg_replace("/[^0-9]+/", "", $input['administrator_phone_no']);
            Company::where('id' ,$input['company_id'])->update([
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $phone,
                'fax_no' => $input['fax_no'],
                'zip' => $input['zip'],
                'address1' => $input['address1'],
                'address2' => $input['address2'],
                'administrator_name' => $input['administrator_name'],
                'registration_no' => $input['registration_no'],
                'administrator_emailId' => $input['administrator_emailId'],
                'licence_no' => $input['licence_no'],
                'administrator_phone_no' => $administrator_phone_no,
                'insurance_id' => $input['insurance_id'],
                'expiration_date' => $input['expiration_date'],
                'services' => implode(",",$input['services'])
            ]);
            return $this->generateResponse(true, 'Update Details Success', null, 200);
        } else if($request->type === "4") {
            $user_ids = $input['doc_id'];
            $device_id = $input['device_id'];
            $ids = [];
             foreach ($user_ids as $index => $value) {
                $ud = UserDevice::where([['user_id', '=', $user_ids[$index]],['device_type', '=', $device_id[$index]],['patient_id', '=', $input['patient_id']]])->first();
              
                if ($ud){
                    $ud->update([
                   	'user_id' => $user_ids[$index],
                        'device_type' => ($device_id[$index]) ? $device_id[$index] : '',
                        'patient_id' => $input['patient_id'],
                   ]);
                } else {
                    $ud = new UserDevice();
       	            $ud->user_id = $user_ids[$index];
                    $ud->device_type = $device_id[$index];
                    $ud->patient_id = $input['patient_id'];
                    $ud->save();
                }
            } 
            return $this->generateResponse(true, 'Update Details Success', $ud, 200);
        }

        return $this->generateResponse(false, 'Something Went Wrong', null, 200);
    }

    public function ccmReadingLevelHigh()
    {
        try {
            $high = UserDeviceLog::where('level',3)->with('userDevice','userDevice.user')->whereHas('userDevice')
                ->WhereIn('user_device_logs.id',DB::table('user_device_logs AS udl')
                    ->join('user_devices','user_devices.id','=','udl.user_device_id' )                   
                    ->groupBy('udl.user_device_id', 'patient_id')
                    ->orderBy('udl.id','DESC')->pluck(DB::raw('MAX(udl.id) AS id'))
                )
                ->get();
            
            $low_midium = UserDeviceLog::whereIn('level',['1','2'])->take(10)
                ->with('userDevice','userDevice.user')->whereHas('userDevice')
                ->WhereIn('user_device_logs.id',DB::table('user_device_logs AS udl')
                    ->join('user_devices','user_devices.id','=','udl.user_device_id' )                   
                    ->groupBy('udl.user_device_id', 'udl.level')
                    ->orderBy('udl.id','DESC')->pluck(DB::raw('MAX(udl.id) AS id'))
                )
                ->get();

            $data = [
                'high' => $high,
                'low_midium' => $low_midium
            ];

            return $this->generateResponse(true, 'CCM Readings!', $data, 200);
        } catch (\Exception $ex) {
            return $this->generateResponse(false, $ex->getMessage(), null, 200);
        }
    }
}