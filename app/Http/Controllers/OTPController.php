<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

/**
 * Class OTPController
 * @package App\Http\Controllers
 */
class OTPController extends Controller
{

    /**
     * @var Otp
     */
    protected $otpModel;

    /**
     * OTPController constructor.
     * @param Otp $otpModel
     */
    public function __construct(Otp $otpModel){

        $this->otpModel = $otpModel;

    }

    /**
     *
     * Send OTP
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @route POST /api/send_otp
     *
     */
    public function sendOTP($occasion,$medium,$contact,$userId){
//dd($occasion,$medium,$contact);
//        $occasion = $request->input('occasion');
//        $medium = $request->input('medium');
//        $contact = $request->input('contact');
        $otp = $this->generateOTP($occasion, $contact,$userId);

        try {

            if($medium == 'email') {

                $emailAlreadyExist = User::where('email', $contact)->first();
                if($emailAlreadyExist->email_verified_at) {
                    Mail::send('emails.welcome', ["otp" => $otp,'user'=>User::find($userId)], function ($m) use ($contact){
                        $m->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                        $m->to($contact)->subject('Your Email verification OTP');
                    });
                }



            } else if($medium == 'mobile') {

                $mobileAlreadyExist = User::where('phone', $contact)->count();
//                if($mobileAlreadyExist > 0) {
//                    throw new \Exception("The mobile number is already associated with another account.");
//                }
                $response =  $this->sendViaMobileOtp($contact, "OTP for your mobile-verification is: {$otp}");
//                $response = \SMS::sendSMS($contact, "OTP for your mobile-verification is: {$otp}");
                $code = substr($response, 0, 7);
                if($code != 'success'){
                    throw new \Exception("Error while sending OTP.");
                }

            } else {
                throw new \Exception("Invalid otp type.");
            }

            return response()->json(['status' => true]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }

    }

    /**
     *
     * Generate OTP
     *
     * @param $occasion
     * @param $contact
     * @param $userID
     * @return int
     */
    public function generateOTP($occasion, $contact, $userID){

        $otp = mt_rand(100000, 999999);

        $data['occasion'] = $occasion;
        $data['token'] = $otp;
        $data['contact'] = $contact;
        $data['user_id'] = $userID;

        $this->otpModel->insertOTP($data);

        return $otp;

    }

//    /**
//     *
//     * Verify OTP
//     *
//     * @param Request $request
//     * @return \Illuminate\Http\JsonResponse
//     *
//     * @route POST /api/verify_otp
//     *
//     */
//    public function verifyOTP(Request $request){
//
//        $data['occasion'] = $request->input('occasion');
//        $data['contact'] = $request->input('contact');
//        $data['token'] = $request->input('token');
//
//        $response = $this->otpModel->verifyOTP($data);
//
//        if(is_array($response) && count($response)>0){
//
//            return response()->json(
//                array(
//                    'status' => true
//                )
//            );
//
//        } else{
//
//            return response()->json(
//                array(
//                    'status' => false
//                )
//            );
//
//        }
//
//    }

}
