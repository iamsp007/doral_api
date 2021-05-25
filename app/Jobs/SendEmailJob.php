<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
        Mail::to($this->email)->send(new $this->mailType($this->detail));
    }
}
