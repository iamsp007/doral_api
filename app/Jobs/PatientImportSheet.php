<?php

namespace App\Jobs;

use App\Imports\BulkImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PatientImportSheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $referral_id = null;
    public $service_id = null;
    public $file_type = null;
    public $form_id = null;
    public $filenameWithExt = null;
    public $file = null;
    // public $company_id = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($referral_id, $service_id, $file_type, $form_id, $filenameWithExt, $file)
    {
        $this->referral_id = $referral_id;
        $this->service_id = $service_id;
        $this->file_type = $file_type;
        $this->form_id = $form_id;
        $this->filenameWithExt = $filenameWithExt;
        $this->file = $file;
        // $this->company_id = $company_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $import = new BulkImport($this->referral_id, $this->service_id, $this->file_type, $this->form_id, $this->filenameWithExt);

        $import->Import($this->file);

        return $import;
       
    }
}
