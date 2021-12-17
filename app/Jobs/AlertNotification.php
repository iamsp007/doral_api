<?php

namespace App\Jobs;


use App\Mail\SendErrorEmail;
use App\Models\CareTeam;
use App\Models\CaseManagement;
use App\Models\Demographic;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Nexmo\Laravel\Facade\Nexmo;

class AlertNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $detail = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($detail)
    {
        $this->detail = $detail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('doral message send start');
        $this->sendsmsToMe($this->detail['message'], $this->detail['phone']);
        Log::info('doral message send end');
       //$this->sendEmailToVisitor($this->detail['patient_id'],$this->detail['message'],$this->detail['phone']);
    }

    public function sendEmailToVisitor($patient_id,$message,$phone)
    {
        Log::info('doral message send start');
        $demographic = Demographic::with(['user'=> function($q){
            $q->select('id','first_name', 'last_name');
        }])->where('user_id',$patient_id)->select('id', 'user_id', 'patient_id')->first();
        $input['patientId'] = $demographic->patient_id;
        $date = Carbon::now();// will get you the current date, time
        $today = $date->format("Y-m-d");

        $input['from_date'] = $today;
        $input['to_date'] = $today;
	
        $curlFunc = searchVisits($input);   
        
        if (isset($curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits'])) {
        $visitID = $curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits']['VisitID'];
	        if(count($visitID) > 1) {
                foreach ($visitID as $viId) {
                    $scheduleInfo = getScheduleInfo($viId);
                    $getScheduleInfo = $scheduleInfo['soapBody']['GetScheduleInfoResponse']['GetScheduleInfoResult']['ScheduleInfo'];
                    $caregiver_id = ($getScheduleInfo['Caregiver']['ID']) ? $getScheduleInfo['Caregiver']['ID'] : '' ;
                    
                    $demographicModal = Demographic::select('id','user_id','patient_id')->where('patient_id', $caregiver_id)->with(['user' => function($q) {
                        $q->select('id', 'email', 'phone');
                    }])->first();
                    
                    if ($demographicModal && $demographicModal->user->phone != '') {
                        $phoneNumber = $demographicModal->user->phone;
                    } else {
                        $getdemographicDetails = getCaregiverDemographics($caregiver_id);
                        $demographics = $getdemographicDetails['soapBody']['GetCaregiverDemographicsResponse']['GetCaregiverDemographicsResult']['CaregiverInfo'];
        
                        $phoneNumber = $demographics['Address']['HomePhone'] ? $demographics['Address']['HomePhone'] : '';
                    }
                    Log::info('patient message send start');
                    //$this->sendsmsToMe($message, $phoneNumber);
                    $this->sendsmsToMe($message . ' Message for caregiver' , '8511380657');
                    Log::info('patient message send end');
                }
            } else {
                $viId = $curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits']['VisitID'];
            
                $scheduleInfo = getScheduleInfo($viId);
                $getScheduleInfo = $scheduleInfo['soapBody']['GetScheduleInfoResponse']['GetScheduleInfoResult']['ScheduleInfo'];
                $caregiver_id = ($getScheduleInfo['Caregiver']['ID']) ? $getScheduleInfo['Caregiver']['ID'] : '' ;
                
                $demographicModal = Demographic::select('id','user_id','patient_id')->where('patient_id', $caregiver_id)->with(['user' => function($q) {
                    $q->select('id', 'email', 'phone');
                }])->first();
                
                if ($demographicModal && $demographicModal->user->phone != '') {
                    $phoneNumber = $demographicModal->user->phone;
                } else {
                    $getdemographicDetails = getCaregiverDemographics($caregiver_id);
                    $demographics = $getdemographicDetails['soapBody']['GetCaregiverDemographicsResponse']['GetCaregiverDemographicsResult']['CaregiverInfo'];

                    $phoneNumber = $demographics['Address']['HomePhone'] ? $demographics['Address']['HomePhone'] : '';
                }
                Log::info('patient message send start');
                //$this->sendsmsToMe($message, $phoneNumber);
                $this->sendsmsToMe($message . ' Message for caregiver' , '8511380657');
                Log::info('patient message send end');
            }
        }
 
        $caseManagers = CaseManagement::with('clinician')->where([['patient_id', '=' ,$patient_id],['texed', '=', '1']])->get();
        foreach ($caseManagers as $key => $caseManager) {
            Log::info('case manager message send start');
            //$this->sendsmsToMe($message, $caseManager->clinician->phone);
             $this->sendsmsToMe($message . ' Message for case manager', '8511380657');
            Log::info('case manager message send end');
        }

        $careTeams = CareTeam::where([['patient_id', '=' ,$patient_id],['detail->texed', '=', 'on']])->whereIn('type',['1','2'])->get();
      	
        foreach ($careTeams as $key => $value) {
            Log::info('care team message send start');
            //$this->sendsmsToMe($message, setPhone($value->detail['phone']));
            $this->sendsmsToMe($message. ' Message for case careteam('. $value->type . ')', '8511380657');
            Log::info('care team message send end');
        }
         Log::info('doral message send end');
    }

    public function sendsmsToMe($message, $phone) {	
        $to = $phone;
        $from = "12089104598";	
        $api_key = "bb78dfeb";
        $api_secret = "PoZ5ZWbnhEYzP9m4";	
        $uri = 'https://rest.nexmo.com/sms/json';	
        $text = $message;	
        $fields = '&from=' . urlencode($from) .	
                '&text=' . urlencode($text) .	
                '&to=+91' . urlencode($to) .	
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

    public function curlCall($data, $method)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => config('patientDetailAuthentication.AppUrl'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
               'Content-Type: text/xml'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = new \SimpleXMLElement($response);
        return json_decode(json_encode((array)$xml), TRUE);
    }
}
