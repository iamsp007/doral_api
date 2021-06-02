<?php

namespace App\Jobs;

use App\Mail\UpdateStatusNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMailRoadlRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->data->patient && $this->data->patient->email) {
            Log::info('Patient email is'.$this->data->patient->email);
            $clinicianFirstName = ($this->data->detail && $this->data->detail->first_name) ? $this->data->detail->first_name : '';
            $clinicianLastName = ($this->data->detail && $this->data->detail->last_name) ? $this->data->detail->first_name : '';
            $details = [
                'first_name' => ($this->data->patient && $this->data->patient->first_name) ? $this->data->patient->first_name : '' ,
                'last_name' => ($this->data->patient && $this->data->patient->last_name) ? $this->data->patient->last_name : '',
                'status' => 'Accepted',
                'message' => 'You have sent roadL request to . ' . $clinicianFirstName . ' ' . $clinicianLastName. ', and By when will he reach you will get the details in the mail after . ' . $clinicianFirstName . ' ' . $clinicianLastName. ' accepts the request.'
            ];
            Mail::to($this->data->patient->email)->send(new UpdateStatusNotification($details));
        }

        // if ($this->data->detail && $this->data->detail->email) {
        //     log::info('clinician email is:'.$this->data->detail->email);
        //     $patientFirstName = ($this->data->patient && $this->data->patient->first_name) ? $this->data->patient->first_name : '';
        //     $patientLastName = ($this->data->patient && $this->data->patient->first_name) ? $this->data->patient->first_name : '';
        //     $details = [
        //         'first_name' => ($this->data->detail && $this->data->detail->first_name) ? $this->data->detail->first_name : '' ,
        //         'last_name' => ($this->data->detail && $this->data->detail->last_name) ? $this->data->detail->last_name : '',
        //         'status' => 'Request',
        //         'message' => 'You got a roadL request by ' . $patientFirstName . ' ' . $patientLastName .'
        //          manisha You have sent roadL request to manisha You have requested' . $patientFirstName . ' ' . $patientLastName .' After accepting the request, at what time you have to reach the patient’s house, they will get you in the mail.',
        //     ];
        //     Mail::to($this->data->detail->email)->send(new UpdateStatusNotification($details));
        // }
    }
}
