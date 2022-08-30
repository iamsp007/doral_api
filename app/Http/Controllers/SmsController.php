<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Jobs\SendNotificationJob;
use App\Mail\SendErrorEmail;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class SmsController extends Controller
{
 public function sendsmsToTwilio($message, $to) {
        // $account_sid = 'AC509601378833a11b18935bf0fe6387cc';
        // $auth_token = '7c6296070a54f124911fa4098467f03a';
        // $twilio_number = '+12184133934';

        $account_sid = 'ACbe214ebdec2d1af98ed69bea61d8c7de';
        $auth_token = '667678d9e907883b5e7ea4e1a332f9aa';
        $twilio_number = '+17472986910';

        $client = new Client($account_sid, $auth_token);

        if($to) {
            $client->messages->create('+1'.setPhone($to), [
                'from' => $twilio_number, 
                'body' => $message
            ]);
        }
    }
    public function sendsmsToMe($message, $to) {
        $to = $to;
        $from = "12089104598";	
        $api_key = "bb78dfeb";
        $api_secret = "PoZ5ZWbnhEYzP9m4";	
        $uri = 'https://rest.nexmo.com/sms/json';	
        $text = $message;	
        $fields = '&from=' . urlencode($from) .	
                '&text=' . urlencode($text) .	
                '&to=+1' . urlencode($to) .	
                '&api_key=' . urlencode($api_key) .	
                '&api_secret=' . urlencode($api_secret);	
        $res = curl_init($uri);	
        curl_setopt($res, CURLOPT_POST, TRUE);	
        curl_setopt($res, CURLOPT_RETURNTRANSFER, TRUE); // don't echo	
        curl_setopt($res, CURLOPT_SSL_VERIFYPEER, FALSE);	
        curl_setopt($res, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);	
        curl_setopt($res, CURLOPT_POSTFIELDS, $fields);	
        curl_exec($res);

        if (curl_errno($res)) {
            $error_msg = curl_error($res);
        }
        curl_close($res);

        if (isset($error_msg)) {
            $details = [
               'message' => $error_msg,
            ];

            Mail::to('shashikant@hcbspro.com')->send(new SendErrorEmail($details));
        }
        
    }

    public function sendSms($patientRequest,$status)
    {
        $clinicianFirstName = ($patientRequest->detail && $patientRequest->detail->first_name) ? $patientRequest->detail->first_name : '';
        $clinicianLastName = ($patientRequest->detail && $patientRequest->detail->last_name) ? $patientRequest->detail->last_name : '';
        $patientFirstName = ($patientRequest->patient && $patientRequest->patient->first_name) ? $patientRequest->patient->first_name : '';
        $patientLastName = ($patientRequest->patient && $patientRequest->patient->last_name) ? $patientRequest->patient->last_name : '';
	$role_name = '';
        if ($patientRequest->detail) {
        
        $role_name = implode(',',$patientRequest->detail->roles->pluck('name')->toArray());
	}
        $address = '';
        if ($patientRequest->patient->demographic && $patientRequest->patient->demographic->address) {
            $addressData = $patientRequest->patient->demographic->address;
            
            if ($addressData['address1']){
                $address.= $addressData['address1'];
            }
            if ($addressData['city']){
                $address.=', '.$addressData['city'];
            }
            if ($addressData['state']){
                $address.=', '.$addressData['state'];
            }
        
            if ($addressData['zip_code']){
                $address.=', '.$addressData['zip_code'];
            }
    
            if ($address){
                $address = $address;
            }
        }
      $patientMessage = $clinicianMessage = $requestMessage = $subject = '';
        if ($status === "1"){
            $subject = 'RoadL request created.';
            $patientMessage = 'You have sent roadL request to . ' . $clinicianFirstName . ' ' . $clinicianLastName. ', and By when will he reach you will get the details in the mail after . ' . $clinicianFirstName . ' ' . $clinicianLastName. ' accepts the request.';

            $clinicianMessage = 'You got a roadL request by ' . $patientFirstName . ' ' . $patientLastName .' After accepting the request, at what time you have to reach the patient’s house, they will get you in the mail.';

            $requestMessage = 'You have sent roadL request to . ' . $clinicianFirstName . ' ' . $clinicianLastName. ' of ' . $patientFirstName . ' ' . $patientLastName ;
        } else if ($status === "2") {
            $subject = 'RoadL request accepted';
            $patientMessage = $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ') has started RoadL request of ' . $patientFirstName . ' ' . $patientLastName . ' for patient address: ' . $address . '. You can track RoadL requests by RoadL id : ' . $patientRequest->parent_id;

            $clinicianMessage = 'You have accepted RoadL request of ' . $patientFirstName . ' ' . $patientLastName . '. You can track RoadL requests by RoadL id : ' . $patientRequest->parent_id;

            $requestMessage = 'RoadL request of ' . $patientFirstName . ' ' . $patientLastName . 'accepted by '  . $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ')';
        } elseif ($status === "3") {
            $subject = 'RoadL request arrived';
            $patientMessage = $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ') arrived at ' . $patientFirstName . ' ' . $patientLastName . ' addrress: ' . $address . '. for RoadL request.';

            $clinicianMessage = $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ') arrived at ' . $patientFirstName . ' ' . $patientLastName . ' addrress: ' . $address . '. for RoadL request.';

            $requestMessage = $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ') arrived at ' . $patientFirstName . ' ' . $patientLastName . ' addrress: ' . $address . '. for RoadL request.';

        } 
        elseif ($status === "4" || $status === 4) {
            $subject = 'RoadL request completed';
            $patientMessage = $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ') has completed RoadL request of ' . $patientFirstName . ' ' . $patientLastName;

            $clinicianMessage = 'You have completed RoadL request of ' . $patientFirstName . ' ' . $patientLastName;

            $requestMessage = 'RoadL request of ' . $patientFirstName . ' ' . $patientLastName . 'completed by '  . $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ')';
        } elseif ($status === "5") {
            $subject = 'RoadL request cancel';
            $patientMessage = $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ') has cancel RoadL request of ' . $patientFirstName . ' ' . $patientLastName;

            $clinicianMessage = 'You have cancel RoadL request of ' . $patientFirstName . ' ' . $patientLastName;

            $requestMessage = 'RoadL request of ' . $patientFirstName . ' ' . $patientLastName . 'cancel by'  . $clinicianLastName . '(' . $role_name . ')';
        } 

        if ($patientRequest->patient && $patientRequest->patient->email) {
            $phone = ($patientRequest->patient->phone) ? $patientRequest->patient->phone : '';
            $details = [
                'first_name' => ($patientRequest->patient->first_name) ? $patientRequest->patient->first_name : '' ,
                'last_name' => ($patientRequest->patient->last_name) ? $patientRequest->patient->last_name : '',
                'message' => $patientMessage,
                'phone' =>  setPhone($phone),
            ]; 
        
            SendEmailJob::dispatch($patientRequest->patient->email, $details, 'UpdateStatusNotification');
        }
        
        if ($patientRequest->detail && $patientRequest->detail->email) {
            $patientFirstName = ($patientRequest->patient->first_name) ? $patientRequest->patient->first_name : '';
            $patientLastName = ($patientRequest->patient->last_name) ? $patientRequest->patient->last_name : '';
            $phone = ($patientRequest->detail->phone) ? $patientRequest->detail->phone : '';
            $role_name = implode(',',$patientRequest->patient->roles->pluck('name')->toArray());
            
            $details = [
                'first_name' => ($patientRequest->detail->first_name) ? $patientRequest->detail->first_name : '' ,
                'last_name' => ($patientRequest->detail->last_name) ? $patientRequest->detail->last_name : '',
                'message' => $clinicianMessage,
                'phone' => setPhone($phone),
            ];

            SendEmailJob::dispatch($patientRequest->detail->email, $details, 'UpdateStatusNotification');
        }

        if ($patientRequest->request && $patientRequest->request->email) {
          
            $phone = ($patientRequest->request->phone) ? $patientRequest->request->phone : '';
            $role_name = implode(',',$patientRequest->patient->roles->pluck('name')->toArray());
            
            $details = [
                'first_name' => ($patientRequest->request->first_name) ? $patientRequest->request->first_name : '' ,
                'last_name' => ($patientRequest->request->last_name) ? $patientRequest->request->last_name : '',
                'message' => $requestMessage,
                'phone' =>  setPhone($phone),
            ];

            SendEmailJob::dispatch($patientRequest->request->email, $details, 'UpdateStatusNotification');
        }
        
    }

    public function sendNotificationBackground($data,$clinicianList)
    {
        SendNotificationJob::dispatch($data, $clinicianList);
    }
}
