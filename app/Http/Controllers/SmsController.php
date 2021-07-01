<?php

namespace App\Http\Controllers;

use App\Mail\SendErrorEmail;
use App\Mail\UpdateStatusNotification;
use Illuminate\Support\Facades\Mail;

class SmsController extends Controller
{
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
      
        if ($status === "1"){
            
            $patientMessage = 'You have sent roadL request to . ' . $clinicianFirstName . ' ' . $clinicianLastName. ', and By when will he reach you will get the details in the mail after . ' . $clinicianFirstName . ' ' . $clinicianLastName. ' accepts the request.';

            $clinicianMessage = 'You got a roadL request by ' . $patientFirstName . ' ' . $patientLastName .' After accepting the request, at what time you have to reach the patient’s house, they will get you in the mail.';

            $requestMessage = 'You have sent roadL request to . ' . $clinicianFirstName . ' ' . $clinicianLastName. ' of ' . $patientFirstName . ' ' . $patientLastName ;
        } else if ($status === "2") {
            $patientMessage = $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ') has started RoadL request of ' . $patientFirstName . ' ' . $patientLastName . ' for patient address: ' . $address . '. You can track RoadL requests by RoadL id : ' . $patientRequest->parent_id;

            $clinicianMessage = 'You have accepted RoadL request of ' . $patientFirstName . ' ' . $patientLastName . '. You can track RoadL requests by RoadL id : ' . $patientRequest->parent_id;

            $requestMessage = 'RoadL request of ' . $patientFirstName . ' ' . $patientLastName . 'accepted by '  . $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ')';
        } elseif ($status === "3") {
            $patientMessage = $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ') arrived at ' . $patientFirstName . ' ' . $patientLastName . ' addrress: ' . $address . '. for RoadL request.';

            $clinicianMessage = $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ') arrived at ' . $patientFirstName . ' ' . $patientLastName . ' addrress: ' . $address . '. for RoadL request.';

            $requestMessage = $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ') arrived at ' . $patientFirstName . ' ' . $patientLastName . ' addrress: ' . $address . '. for RoadL request.';

        } 
        elseif ($status === "4" || $status === 4) {
            $patientMessage = $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ') has completed RoadL request of ' . $patientFirstName . ' ' . $patientLastName;

            $clinicianMessage = 'You have completed RoadL request of ' . $patientFirstName . ' ' . $patientLastName;

            $requestMessage = 'RoadL request of ' . $patientFirstName . ' ' . $patientLastName . 'completed by '  . $clinicianFirstName . ' ' . $clinicianLastName . '(' . $role_name . ')';
        } elseif ($status === "5") {
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
                'phone' => $phone,
            ]; 
        
            Mail::to($patientRequest->patient->email)->send(new UpdateStatusNotification($details));
           
            $this->sendsmsToMe($details['message'], setPhone($details['phone']));
        }
        
        if ($patientRequest->detail && $patientRequest->detail->email) {
            $patientFirstName = ($patientRequest->patient->first_name) ? $patientRequest->patient->first_name : '';
            $patientLastName = ($patientRequest->patient->last_name) ? $patientRequest->patient->last_name : '';
            $phone = ($patientRequest->detail->phone) ? $patientRequest->detail->phone : '';
            $role_name = implode(',',$patientRequest->patient->roles->pluck('name')->toArray());
            
            $details = [
                'first_name' => ($patientRequest->detail->first_name) ? $patientRequest->detail->first_name : '' ,
                'last_name' => ($patientRequest->detail->last_name) ? $patientRequest->detail->last_name : '',
                'message' => 'You have arrived RoadL request of ' . $patientFirstName . ' ' . $patientLastName,
               'message' => $clinicianMessage,
                'phone' => $phone,
            ];
            Mail::to($patientRequest->detail->email)->send(new UpdateStatusNotification($details));
            
            $this->sendsmsToMe($details['message'], setPhone($details['phone']));
        }

        if ($patientRequest->request && $patientRequest->request->email) {
          
            $phone = ($patientRequest->request->phone) ? $patientRequest->request->phone : '';
            $role_name = implode(',',$patientRequest->patient->roles->pluck('name')->toArray());
            
            $details = [
                'first_name' => ($patientRequest->request->first_name) ? $patientRequest->request->first_name : '' ,
                'last_name' => ($patientRequest->request->last_name) ? $patientRequest->request->last_name : '',
                'message' => 'You have arrived RoadL request of ' . $patientFirstName . ' ' . $patientLastName,
               'message' => $requestMessage,
                'phone' => $phone,
            ];

            Mail::to($patientRequest->request->email)->send(new UpdateStatusNotification($details));
           
            $this->sendsmsToMe($details['message'], setPhone($details['phone']));
        }
        
    }
}
