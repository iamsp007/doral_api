<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\OTPController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\UpdateDeviceTokenRequest;
use App\Models\Otp;
use App\Models\User;
use App\Models\Patient;
use App\Models\VirtualRoom;
use App\Models\VonageRoom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Models\referral;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenTok\MediaMode;
use OpenTok\OpenTok;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PatientController;
use App\Models\Country;
use App\Models\State;
use App\Models\City;


class AuthController extends Controller
{
    protected $employeeContoller, $patientController, $otps;
    public function __construct(EmployeeController $employeeContoller, PatientController $patientController,OTPController $otps)
    {
        $this->employeeContoller = $employeeContoller;
        $this->patientController = $patientController;
        $this->otps = $otps;
    }

    public function login(LoginRequest $request)
    {
        try {
            $username = $request->username;
            $password = $request->password;
            $field = 'email';
            if (is_numeric($request->username)) {
                $field = 'phone';
            }
            //$credentials = [$field => $username, 'password' => $password];
            $credentials = [$field => $username, 'password' => $password, 'status' => '1'];
            // print_r($credentials);die;
            if (!Auth::attempt($credentials)) {
                return $this->generateResponse(false, $field . ' or password are incorrect!', null);
            }
            $user = $request->user();
            $phone = preg_replace("/[^0-9]+/", "", $user->phone);
            $user->phone = (string)$phone;
            $user->isEmailVerified = $user->email_verified_at ? true : false;
            $user->isMobileVerified = $user->phone_verified_at ? true : false;
            $user->isProfileVerified = $user->profile_verified_at ? true : false;
            $user->isMobileExist = $user->phone ? true : false;
            $user->roles = $user->roles ? $user->roles->first() : null;
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addMinute(1);
            $token->save();
            $data = [
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'user' => $user,
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ];
            // update device token and type
            if ($request->has('device_token')) {
                $users = User::find($user->id);
                if ($users) {
                    $users->device_token = $request->device_token;
                    $users->device_type = $request->device_type;
                    $users->save();
                }
            }
            return $this->generateResponse(true, 'Login Successfully!', $data);
        } catch (\Exception $e) {
            dd($e);
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, null);
        }
    }

    public function register(RegistrationRequest $request)
    {
        try {
            $user = new User;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->dob = $request->dob;
            $user->phone = $request->phone;
            $user->status = '1';
            $user->assignRole($request->type)->syncPermissions(Permission::all());
            if ($user->save()) {
                $request = $request->toArray();
                $id = $user->id;
                if ($user->id) {
                    // PREVIOUS UNNECESSARY CODE
                    $request['user_id'] = $id;
                    if ($request['type'] == 'clinician' || $request['type'] == 'admin') {
                        unset($request['type']);
                        $result = $this->employeeContoller->store($request);
                    } else if ($request['type'] == 'patient') {
                        unset($request['type']);
                        $result = $this->patientController->store($request);
                    }
                    // Check the condition if error into database
                    if (!$result) {
                        throw new \ErrorException('Error in insert');
                    }
                    // BELOW FOR LOGIN
                    // $username = $request->username;
                    // $password = $request->password;
                    // $field = 'email';
                    // if (is_numeric($request->username)) {
                    //     $field = 'phone';
                    // }
                    // $credentials = [$field => $username, 'password' => $password, 'status' => '1'];
                    // if (!Auth::attempt($credentials)) {
                    //     return $this->generateResponse(false, $field . ' or Password are Incorrect!');
                    // }
                    $phone = preg_replace("/[^0-9]+/", "", $user->phone);
                    $user->phone = (string)$phone;
                    $user->isEmailVerified = $user->email_verified_at ? true : false;
                    $user->isMobileVerified = $user->phone_verified_at ? true : false;
                    $user->isProfileVerified = $user->profile_verified_at ? true : false;
                    // $user->roles = $user->roles ? $user->roles->first() : null;
                    // $tokenResult = $user->createToken('Personal Access Token');
                    // $token = $tokenResult->token;
                    // if ($request->remember_me)
                    //     $token->expires_at = Carbon::now()->addMinute(1);
                    // $token->save();
                    // $data = [
                    //     'access_token' => $tokenResult->accessToken,
                    //     'token_type' => 'Bearer',
                    //     'user' => $user,
                    //     'expires_at' => Carbon::parse(
                    //         $tokenResult->token->expires_at
                    //     )->toDateTimeString()
                    // ];
                    // // update device token and type
                    // if ($request->has('device_token')) {
                    //     $users = User::find($user->id);
                    //     if ($users) {
                    //         $users->device_token = $request->device_token;
                    //         $users->device_type = $request->device_type;
                    //         $users->save();
                    //     }
                    // }
                    $status = true;
                    $message = "Registration successful.";
                    return $this->generateResponse(true, $message, $user, 200);
                } else {
                    throw new \ErrorException('Error found');
                }
            }
            return $this->generateResponse(false, 'Something Went Wrong!', [
                'message' => 'Invalid Daata'
            ], 200);
        } catch (\Exception $e) {
            \Log::error($e);
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, null);
        }
    }

    public function createRoom($user)
    {
        try {
            // Instantiate a new OpenTok object with our api key & secret
            $opentok = new OpenTok(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));

            // Creates a new session (Stored in the Vonage API cloud)
            $session = $opentok->createSession(array('mediaMode' => MediaMode::RELAYED));

            // Create a new virtual class that would be stored in db
            $class = new VonageRoom();
            // Generate a name based on the name the teacher entered
            $class->name = 'Dr. ' . $user->first_name . " " . $user->last_name . " Room - " . $user->id;
            // Store the unique ID of the session
            $class->user_id = $user->id;
            $class->session_id = $session->getSessionId();
            // Save this class as a relationship to the teacher
            $user->myRoom()->save($class);
        } catch (\Exception $exception) {
        }
    }

    public function logout(Request $request)
    {
        $users = User::find($request->user()->id);
        if ($users) {
            $users->is_available = 0;
            $users->save();
        }
        $request->user()->token()->revoke();

        return $this->generateResponse(true, 'Successfully logged out');
    }

    public function user(Request $request)
    {
        return $this->generateResponse(true, 'user detail', $request->user());
    }

    public function forgotPassword(Request $request)
    {
        $data = [];
        $input = $request->all();
        $rules = array(
            'email' => "required|email",
        );
        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->generateResponse(false,$validator->errors()->first(),array(),200);
        } else {
            try {
                $response = \Password::sendResetLink($request->only('email'));

                switch ($response) {
                    case \Password::RESET_LINK_SENT:
                        $message = trans($response);
                        return $this->generateResponse(true, $message, $data,200);
                    case \Password::INVALID_USER:
                        $message = trans($response);
                        return $this->generateResponse(true, $message, $data,200);
                }
            } catch (\Swift_TransportException $ex) {
                $message = $ex->getMessage();
                return $this->generateResponse(false,$message, $data,200);
            } catch (Exception $ex) {
                $message = $ex->getMessage();
                return $this->generateResponse(false, $message, $data,200);
            }
        }
    }

    public function resetPassword(Request $request)
    {
        $input = $request->all();
        $data = array();
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            $message =  $validator->errors()->first();
            return $this->generateResponse(false, $message, $data);
        } else {
            try {
                $user = User::gethUserUsingEmail($input['email']);
                if (!$user) {
                    throw new Exception("Email not match with database");
                }
                $userid = $user->id;
                if ((Hash::check(request('old_password'), $user->password)) == false) {
                    $message = "Check your old password.";
                    return $this->generateResponse(false, $message, $data);
                } else if ((Hash::check(request('new_password'), $user->password)) == true) {
                    $message = "Please enter a password which is not similar then current password.";
                    return $this->generateResponse(false, $message, $data);
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                    $message = "Password updated successfully.";
                    return $this->generateResponse(true, $message, $data);
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $message = $ex->errorInfo[2];
                } else {
                    $message = $ex->getMessage();
                }
                return $this->generateResponse(false, $message, $data);
            }
        }
    }

    public function patientLogin(Request $request)
    {
        $input = $request->all();
        $data = array();
        $rules = array(
            'ssn' => 'required|min:4|max:4',
            'dob' => 'required|date',
        );
        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            $message =  $validator->errors()->first();
            return $this->generateResponse(false, $message, $data);
        } else {
            try {
                $patient = Patient::getPatientUsingSsnAndDob($input);
                if (!$patient) {
                    throw new Exception("SSN or DOB not match with database");
                }
                $count = Patient::where('id', '!=', $patient->id)->where('phone', $patient->phone)->where('status', 'active')->count();
                if ($count) {
                    $message = "Found ".$count." account with your registered mobile number";
                    $data = [
                        'isMultiplePatientExist' => true
                    ];
                    return $this->generateResponse(true, $message, $data);
                } else {
                    if (!Auth::loginUsingId($patient->user->id)) {
                        return $this->generateResponse(false, 'Something went wrong!');
                    }
                    $user = $request->user();
                    $user->isEmailVerified = $user->email_verified_at ? true : false;
                    $user->isMobileVerified = $user->phone_verified_at ? true : false;
                    $user->isProfileVerified = $user->profile_verified_at ? true : false;
                    $user->isMultiplePatientExist = false;
                    $user->roles = $user->roles ? $user->roles->first() : null;
                    $tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->token;
                    if ($request->remember_me)
                        $token->expires_at = Carbon::now()->addMinute(1);
                    $token->save();
                    $data = [
                        'access_token' => $tokenResult->accessToken,
                        'token_type' => 'Bearer',
                        'user' => $user,
                        'expires_at' => Carbon::parse(
                            $tokenResult->token->expires_at
                        )->toDateTimeString()
                    ];
                    // update device token and type
                    if ($request->has('device_token')) {
                        $users = User::find($user->id);
                        if ($users) {
                            $users->device_token = $request->device_token;
                            $users->device_type = $request->device_type;
                            $users->save();
                        }
                    }
                    return $this->generateResponse(true, 'Login Successfully!', $data);
                }
            } catch (\Exception $ex) {
                $message = $ex->getMessage();
                return $this->generateResponse(false, $message, $data);
            }
        }
    }

    public function updatePhone(Request $request)
    {
        $data = null;
        try {
            $validator = \Validator::make($request->all(),[
                'phone' => 'required|numeric|unique:users,phone',
            ]);
            if ($validator->fails()) {
                $status = 200;
                $success = false;
                $message = $validator->errors()->first();
                return $this->generateResponse($success, $message, $data, $status);
            }
            $verificationStart = \Nexmo::verify()->start([
                'number' => '+91'.$request->phone,
                'brand'  => config('nexmo.app.name'),
                'code_length' => 4,
                'lg' => 'en-us',
                'pin_expiry' => 60,
                'next_event_wait' => 60
            ]);
            $data = $verificationStart->getRequestId();
            $status = 200;
            $success = true;
            $message = "otp sent";
            return $this->generateResponse($success, $message, $data, $status);
        } catch (Nexmo\Client\Exception\Request $ex) {
            Log::error($ex);
            $status = 403;
            $success = false;
            $message = $ex->getMessage();
            return $this->generateResponse($success, $message, $data, $status);
        }
    }

    public function verifyPhone(Request $request)
    {
        $data = null;
        try {
            \Nexmo::verify()->check(
                $request->request_id,
                $request->code
            );

            $input = $request->all();
            $patient = Patient::getPatientUsingSsnAndDob($input);
            $patient->phone = $request->phone;
            $patient->save();

            $user = $patient->user;
            $user->phone = $request->phone;
            $user->phone_verified_at = date('Y-m-d H:i:s');
            $user->save();

            if (!Auth::loginUsingId($user->id)) {
                return $this->generateResponse(false, 'Something went wrong!');
            }
            $user = $request->user();
            $user->isEmailVerified = $user->email_verified_at ? true : false;
            $user->isMobileVerified = $user->phone_verified_at ? true : false;
            $user->isProfileVerified = $user->profile_verified_at ? true : false;
            $user->isMultiplePatientExist = false;
            $user->roles = $user->roles ? $user->roles->first() : null;
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addMinute(1);
            $token->save();
            $data = [
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'user' => $user,
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ];
            // update device token and type
            if ($request->has('device_token')) {
                $users = User::find($user->id);
                if ($users) {
                    $users->device_token = $request->device_token;
                    $users->device_type = $request->device_type;
                    $users->save();
                }
            }
            return $this->generateResponse(true, 'Login Successfully!', $data);
        } catch (Nexmo\Client\Exception\Request $ex) {
            Log::error($ex);
            $status = 403;
            $success = false;
            $message = $ex->getMessage();
            return $this->generateResponse($success, $message, $data, $status);
        }
    }

    public function countries()
    {
        return Country::find(226); // US only
    }

    public function states()
    {
        return State::all();
    }

    public function cities()
    {
        return City::all();
    }

    public function filterCities(Request $request)
    {
        return City::where('state_code', $request->state_code)->get();
    }
}
