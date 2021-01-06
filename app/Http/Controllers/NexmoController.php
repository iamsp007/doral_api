<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;

class NexmoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    	$data = null;
    	try {
            if ($request->isPatientVerify) {
                $validator = \Validator::make($request->all(),[
                    'phone' => 'required|numeric|unique:users,phone',
                ]);
            } else {
                $validator = \Validator::make($request->all(),[ 
                    'email' => 'required|email|unique:users,email',
                    'phone' => 'required|numeric|unique:users,phone',
                ]);
            }
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
    	$data = null;
    	try {
	    	\Nexmo::verify()->check(
                $request->request_id,
                $request->code
            );
            if ($request->isPatientVerify) {
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
            } else {
                $data = User::where('phone', $request->phone)->first();
                $data->phone_verified_at = date('Y-m-d H:i:s');
                $data->save();
                $status = 200;
                $success = true;
                $message = "verified";
                return $this->generateResponse($success, $message, $data, $status);
            }
        } catch (Nexmo\Client\Exception\Request $ex) {
        	Log::error($ex);
	        $status = 403;
            $success = false;
            $message = $ex->getMessage();
            return $this->generateResponse($success, $message, $data, $status);
        }
    }
}