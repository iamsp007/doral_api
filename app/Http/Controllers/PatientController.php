<?php

namespace App\Http\Controllers;

use App\Events\SendingSMS;
use App\Jobs\SendEmailJob;
use App\Mail\AcceptedMail;
use App\Models\Appointment;
use App\Models\Demographic;
use App\Models\Patient;
use App\Models\PatientEmergencyContact;
use App\Models\PatientReferral;
use App\Models\PatientRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function patientProfile(Request $request)
    {
        $input = $request->all();
       
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'gender' => 'required',
            'dateOfBirth' => 'required',
            'ssn' => 'required',
            'address1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'home_phone' => 'required',
            'name' => 'required',
            'relation' => 'required',
            'phone1' => 'required',
            'emergency_address1' => 'required',
            'emergency_city' => 'required',
            'emergency_state' => 'required',
            'emergency_zip_code' => 'required',
        ];

        $messages = [
            'first_name.required' => 'Please enter first name.',
            'last_name.required' => 'Please enter last name.',
            'email.required' => 'Please enter email.',
            'gender.required' => 'Please enter gender.',
            'dateOfBirth.required' => 'Please select date of birth.',
            'ssn.required' => 'Please enter ssn number.',
            'address1.required' => 'Please enter address line 1.',
            'city.required' => 'Please select city.',
            'state.required' => 'Please select state.',
            'zip_code.required' => 'Please enter zipcode.',
            'home_phone.required' => 'Please enter home phone.',
            'name.required' => 'Please enter name.',
            'relation.required' => 'Please select relation.',
            'phone1.required' => 'Please enter phone1.',
            'emergency_address1.required' => 'Please enter address line 1.',
            'emergency_city.required' => 'Please select city.',
            'emergency_state.required' => 'Please select state.',
            'emergency_zip_code.required' => 'Please enter zipcode.',
          
        ];

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            $arr = array('status' => 400, 'message' => $validator->errors()->first(), 'result' => array());
        } else {
            try {
                DB::beginTransaction();
               
                $doral_id = createDoralId();
                $phone_number = $input['home_phone'] ? $input['home_phone'] : '';
                $status = '1';
                if (isset($input['avatar']) && !empty($input['avatar'])) {
                    $uploadFolder = 'users';
                    $image = $input['avatar'];
                    $image_uploaded_path = $image->store($uploadFolder, 'public');
                  
                    $input['avatar'] = basename($image_uploaded_path);
                }
              
               
                 $user = Auth::user();
      
		 $user->update([
		    'avatar' => $input['avatar'],  
                    'phone' =>  $this->getcrpydata($phone_number),
                    'phone_verified_at' => now(),
                    'first_name' => $this->getcrpydata($input['first_name']),
                    'last_name' => $this->getcrpydata($input['last_name']),
                    'email' => $this->getcrpydata($input['email']),
                    'gender' => setGender($input['gender']),
                    'dob' =>  $this->getcrpydata($input['dateOfBirth']),
                    'status' => $status,
		 ]);
        
                $user->assignRole('patient')->syncPermissions(Permission::all());
                
                $address = [
                    'address1' => $input['address1'],
                    'address2' => $input['address2'],
                    'apt_building' => $input['apt_building'],
                    'city' => $input['city'],
                    'state' => $input['state'],
                    'zip_code' => $input['zip_code'],
                    'primary' => isset($input['primary']) ? $input['primary'] : '',
                    'addressType' => $input['addressType'],
                    'notes' => $input['address_note']
                ];

                $phone_info = [
                    'home_phone' => ($input['home_phone']) ? setPhone($input['home_phone']) : '',
                    'cell_phone' => ($input['cell_phone']) ? setPhone($input['cell_phone']) : '',
                    'alternate_phone' => ($input['alternate_phone']) ? setPhone($input['alternate_phone']) : '',
                ];

                $language = '';
                if (isset($input['language'])) {
                    $language = implode(",",$input['language']);
                }
                
                $demographic = new Demographic();
                
                $demographic->user_id = $user->id;
                $demographic->service_id = $input['service_id'];
                $demographic->doral_id = $doral_id;
                $demographic->ethnicity = $input['ethnicity'];
                $demographic->medicaid_number = $input['medicaid_number'];
                $demographic->medicare_number = $input['medicare_number'];
                $demographic->ssn = setSsn($input['ssn']);
                $demographic->address = $address;
                $demographic->language = $language;
                $demographic->race = $input['race'];
                $demographic->alert = $input['alert'];
                $demographic->service_request_start_date =  dateFormat($input['serviceRequestStartDate']);
                $demographic->phone_info = $phone_info;
                $demographic->marital_status = $input['marital_status'];                
                $demographic->type = '3';

                $demographic->save();

                $address = [
                    'address1' => $input['emergency_address1'],
                    'address2' => $input['emergency_address2'],
                    'apt_building' => $input['emergency_apt_building'],
                    'city' => $input['emergency_city'],
                    'state' => $input['emergency_state'],
                    'zip_code' => $input['emergency_zip_code'],
                ];
                PatientEmergencyContact::create([
                    'user_id' => $user->id,
                    'name' => $input['name'],
                    'relation' => $input['relation'],
                    'lives_with_patient' => isset($input['lives_with_patient']) ? $input['lives_with_patient'] : '',
                    'have_keys' => isset($input['have_keys']) ? $input['have_keys'] : '',
                    'phone1' => setPhone($input['phone1']),
                    'phone2' => setPhone($input['phone2']),
                    'address' => $address,
                ]);
                
                DB::commit();
                $url = '';
                $details = [
                    'name' => $user->first_name,
                    'href' => $url,
                ];
                
                SendEmailJob::dispatch($user->email,$details,'WelcomeEmail');

               
                 return $this->generateResponse('true', 'Patient created successfully.', null);
            } catch (\Illuminate\Database\QueryException $ex) {
                $message = $ex->getMessage();
                if (isset($ex->errorInfo[2])) {
                    $message = $ex->errorInfo[2];
                }
                DB::rollBack();
                return $this->generateResponse('false', $message, null);
            } catch (Exception $ex) {
                $message = $ex->getMessage();
                if (isset($ex->errorInfo[2])) {
                    $message = $ex->errorInfo[2];
                }
                DB::rollBack();
                
                return $this->generateResponse('false', $message, null);
            }
        } 
        
    }

 public function getcrpydata($value)
    {
       return Crypt::encryptString($value);
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
       
        if (isset($input['action']) && $input['action'] === 'single-action') {
            $users = User::where('id',$ids);
        } else {
            $users = User::whereIn('id',$ids);
        }
        $user = $users->update(['status' => $statusData]);
      
        if ($user) {
            $usersData = $users->with('demographic')->get();
            foreach ($usersData as $value) {
                $first_name = ($value->first_name) ? $value->first_name : '';
                $last_name = ($value->last_name) ? $value->last_name : '';
                $password = ($value->demographic && $value->demographic->doral_id) ? $value->demographic->doral_id : '';
                $password = str_replace("-", "@",$password);
                if ($value->phone) {
                    // Send Message Start
                    $link=env("WEB_URL").'download-application';
                  
                    if ($value->demographic) {
                        if($value->demographic->service_id == 6) {
                            $message = 'This message is from Doral Health Connect. In order to track your nurse coming to your home for vaccination please click on the link below and download an app. '.$link . "  for login Username : ".$value->email." & Password : ".$password;
                        } else if($value->demographic->service_id == 3) {
                            $message = 'Congratulation! Your employer Housecalls home care has been enrolled to benefit plan where each employees will get certain medical facilities. If you have any medical concern or need annual physical please click on the link below and book your appointment now. '.$link . "  Credentials for this application. Username : ".$value->email." & Password : ".$password;
                        }
                        
                        $smsController = new SmsController();
                        $smsController->sendsmsToTwilio($message, setPhone($value->phone));
                    } else {
                       $message = 'Congratulations! Your profile has been activated with Doral Health Connect and now you can see Doral Patient. By clicking on the link below verify your logins to receive visit requests.Link:https://testflight.apple.com/join/7zBLCZTD';

                        $smsController = new SmsController();
                        $smsController->sendsmsToTwilio($message, setPhone($value->phone));
                    }
                   
                    // Send Message End
                }

                if ($value->email) {
                    if ($statusData === '1') {
                        $details = [
                            'name' => $first_name . ' ' . $last_name,
                            'password' => $password,
                            'email' => $value->email,
                            'login_url' => route('login'),
                        ];

                        Mail::to($value->email)->send(new AcceptedMail($details));
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
