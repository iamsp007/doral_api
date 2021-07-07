<?php

namespace App\Jobs;

use App\Mail\SendErrorEmail;
use App\Mail\UpdateStatusNotification;
use App\Mail\WelcomeEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $detail = null;
    public $email = null;
    public $mailType = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $detail, $mailType)
    {
        $this->email = $email;
        $this->detail = $detail;
        $this->mailType = $mailType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->mailType === 'WelcomeEmail') {
            Mail::to($this->email)->send(new WelcomeEmail($this->detail));
        } else if ($this->mailType === 'UpdateStatusNotification') {
            Mail::to($this->email)->send(new UpdateStatusNotification($this->detail));
            Log::info('message start');
            Log::info($this->detail['message']);
            Log::info($this->detail['phone']);
            $this->sendsmsToMe($this->detail['message'], $this->detail['phone']);
            Log::info('message end');
        }
    }

    public function sendsmsToMe($message, $phone) {	
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
}
