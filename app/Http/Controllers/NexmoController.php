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
	    	$verificationStart = \Nexmo::verify()->start([
	            'number' => '+91'.$request->mobile,
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
	        $data = User::where('phone', $request->mobile)->first();
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