<?php

namespace App\Jobs;

use App\Models\CareTeam;
use App\Models\Demographic;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Nexmo\Laravel\Facade\Nexmo;

class SendAlertSMS implements ShouldQueue
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
        Nexmo::message()->send([
            //'to'   =>'+1'.env('SEND_SMS'),
            'to'   =>'+1'.$this->detail['phone'],
            'from' => env('SMS_FROM'),
            'text' => $this->message
        ]); 
        Log::info('doral message send end');
        $this->sendEmailToVisitor('9170',$this->detail['message']);
    }

    public function sendEmailToVisitor($patient_id,$message)
    {
        $demographic = Demographic::where('user_id',$patient_id)->select('patient_id')->first();
        $input['patientId'] = $demographic->patient_id;
        $date = Carbon::now();// will get you the current date, time
        $today = $date->format("Y-m-d");

        $input['from_date'] = $today;
        $input['to_date'] = $today;
	
        $curlFunc = searchVisits($input);

        if (isset($curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits'])) {
            $viId = $curlFunc['soapBody']['SearchVisitsResponse']['SearchVisitsResult']['Visits']['VisitID'];
          	
           //foreach ($visitID as $viId) {
                $scheduleInfo = getScheduleInfo($viId);
                $getScheduleInfo = $scheduleInfo['soapBody']['GetScheduleInfoResponse']['GetScheduleInfoResult']['ScheduleInfo'];
                $caregiver_id = ($getScheduleInfo['Caregiver']['ID']) ? $getScheduleInfo['Caregiver']['ID'] : '' ;
                
                $demographicModal = Demographic::where('patient_id',$caregiver_id)->with('user', function($q){
                    $q->select('id','phone');
                })->first();
                if ($demographicModal && $demographicModal->user->phone != '') {
                    $phoneNumber = $demographicModal->user->phone;
                } else {
                    $getdemographicDetails = getCaregiverDemographics($caregiver_id);
                    $demographics = $getdemographicDetails['soapBody']['GetCaregiverDemographicsResponse']['GetCaregiverDemographicsResult']['CaregiverInfo'];
    
                    $phoneNumber = $demographics['Address']['HomePhone'] ? $demographics['Address']['HomePhone'] : '';
                }
		        Log::info('patient message send start');
                Nexmo::message()->send([
                    'to'   =>'+1'.setPhone($phoneNumber),
                    'from' => env('SMS_FROM'),
                    'text' => $message
                ]);
                Log::info('patient message send end');
            //}
        }
 
        $familyPhone = CareTeam::where([['patient_id', '=' ,$patient_id],['detail->texed', '=', 'on']])->whereIn('type',['1','2'])->get();
      	
        foreach ($familyPhone as $key => $value) {
            Log::info('care team message send start');
            Nexmo::message()->send([
                //'to'   =>'+1'.setPhone($value->detail['phone']),
                'to'   =>'+918511380657',
                'from' => env('SMS_FROM'),
                'text' => $message
            ]);
            Log::info('care team message send end');
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
