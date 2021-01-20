<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PatientReferral;
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
            if (!$request->user()->phone) {
                $validator = \Validator::make($request->all(),[
                    'phone' => 'required|numeric|unique:users,phone'
                ]);
            } elseif (!$request->user()->email) {
                $validator = \Validator::make($request->all(),[
                    'email' => 'required|email|unique:users,email'
                ]);
            } else {
                $validator = \Validator::make($request->all(),[
                    'email' => 'required|email',
                    'phone' => 'required|numeric'
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
            $validator = \Validator::make($request->all(),[
                'email' => 'required|email',
                'phone' => 'required|numeric',
                'request_id' => 'required',
                'code' => 'required|min:4|max:4'
            ]);
            if ($validator->fails()) {
                $status = 200;
                $success = false;
                $message = $validator->errors()->first();
                return $this->generateResponse($success, $message, $data, $status);
            }
            \Nexmo::verify()->check(
                $request->request_id,
                $request->code
            );
            $data = $request->user();
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->phone_verified_at = date('Y-m-d H:i:s');
            $data->save();
            $status = 200;
            $success = true;
            $message = "verified";
            return $this->generateResponse($success, $message, $data, $status);
        } catch (Nexmo\Client\Exception\Request $ex) {
        	Log::error($ex);
	        $status = 403;
            $success = false;
            $message = $ex->getMessage();
            return $this->generateResponse($success, $message, $data, $status);
        }
    }
}