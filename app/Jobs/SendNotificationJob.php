<?php

namespace App\Jobs;

use App\Events\SendClinicianPatientRequestNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data = null;
    public $clinicianList = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data,$clinicianList)
    {
        $this->data = $data;
        $this->clinicianList = $clinicianList;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        event(new SendClinicianPatientRequestNotification($this->data, $this->clinicianList));
    }
}
