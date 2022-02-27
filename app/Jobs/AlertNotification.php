<?php

namespace App\Jobs;


use App\Models\CareTeam;
use App\Models\CaseManagement;
use App\Models\Demographic;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

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
        $this->sendEmailToVisitor($this->detail['patient_id'],$this->detail['message'],$this->detail['company']);
       
    }

    public function sendEmailToVisitor($patient_id,$message,$company)
    {
   
        if ($company->account_sid != '' && $company->auth_token != '' && $company->from_sms != '') {
            $account_sid = $company->account_sid;
            $auth_token = $company->auth_token;
            $twilio_number = '+1'.$company->from_sms;
        } else {
            $account_sid = 'AC509601378833a11b18935bf0fe6387cc';
            $auth_token = '7c6296070a54f124911fa4098467f03a';
            $twilio_number = '+12184133934';
        }       
        
        $client = new Client($account_sid, $auth_token);
        
        $demographic = Demographic::with(['user'=> function($q){
            $q->select('id','first_name', 'last_name');
        }])->where('user_id',$patient_id)->select('id', 'user_id', 'patient_id')->first();
       
        if ($demographic) {
            $input['patientId'] = $demographic->patient_id;
            $date = Carbon::now();// will get you the current date, time
            $today = $date->format("Y-m-d");

            $input['from_date'] = $today;
            $input['to_date'] = $today;
        
            $curlFunc = searchVisits($input);   
      
            if (isset($curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits'])) {
            $visitID = $curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits']['VisitID'];
                if(is_array($visitID)) {
                    foreach ($visitID as $viId) {
                        self::getSchedule($viId, $twilio_number, $client, $message);
                    }
                } else {
                    $viId = $curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits']['VisitID'];
                
                    self::getSchedule($viId, $twilio_number, $client, $message);
                }
            }
        }
        $caseManagers = CaseManagement::with('clinician')->where([['patient_id', '=' ,$patient_id]])->get();
        foreach ($caseManagers as $key => $caseManager) {
            try {
                $client->messages->create('+1'.setPhone($caseManager->clinician->phone), [
                    'from' => $twilio_number, 
                    'body' => $message
                ]);  
                            
            } catch (Exception $e) {
                Log::info($e);
            }    
        }

        $careTeams = CareTeam::where([['patient_id', '=' ,$patient_id],['detail->texed', '=', 'on']])->whereIn('type',['1','2'])->get();
      
        foreach ($careTeams as $key => $value) {
            try {
                $client->messages->create('+1'.setPhone($value->detail['phone']), [
                    'from' => $twilio_number, 
                    'body' => $message]);  	    
                
            }catch (\Exception $exception){
                Log::info($exception);
            }
        }
    }

    public static function getSchedule($viId, $twilio_number, $client, $message)
    {	
        $scheduleInfo = getScheduleInfo($viId);
        $getScheduleInfo = $scheduleInfo['soapBody']['GetScheduleInfoResponse']['GetScheduleInfoResult']['ScheduleInfo'];
        $caregiver_id = ($getScheduleInfo['Caregiver']['ID']) ? $getScheduleInfo['Caregiver']['ID'] : '' ;
        
        $demographicModal = Demographic::select('id','user_id','patient_id')->where('patient_id', $caregiver_id)->with(['user' => function($q) {
            $q->select('id', 'email', 'phone');
        }])->first();
        $phoneNumber = '';
        if ($demographicModal && $demographicModal->user->phone != '') {
            $phoneNumber = $demographicModal->user->phone;
        } else {
            $getdemographicDetails = getCaregiverDemographics($caregiver_id);
            if (isset($getdemographicDetails['soapBody']['GetCaregiverDemographicsResponse']['GetCaregiverDemographicsResult']['CaregiverInfo'])) {
                $demographics = $getdemographicDetails['soapBody']['GetCaregiverDemographicsResponse']['GetCaregiverDemographicsResult']['CaregiverInfo'];

                $phoneNumber = $demographics['Address']['HomePhone'] ? $demographics['Address']['HomePhone'] : '';
            }
        }
	
        if($phoneNumber) {
            try {
                $client->messages->create('+1'.setPhone($phoneNumber), [
                    'from' => $twilio_number, 
                    'body' => $message
                ]);
            } catch (\Twilio\Exceptions\RestException $e) {
                Log::info($e);
            }
        }
    }
}