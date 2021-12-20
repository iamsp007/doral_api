<?php

namespace App\Jobs;

use App\Models\NursePractitionerUsers;
use App\Models\PhysicianAssistantUsers;
use App\Models\PhysicianUsers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ScrapingData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $input;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //designation_name = 1(NP), 4(PA), 9(P)
        if ($this->input['designation_id'] === '9') {
            PhysicianUsers::create([
                'category_id' => '1',
                'speciality_id' => '2',
                'first_name' => $this->input['first_name'],
                'last_name' => $this->input['last_name'],
                'ssn_no' => $this->input['ssn_no'],
                'dea_no' => $this->input['dea_no'],
                'zip_code' => $this->input['zip_code'],
                'expire_month' => $this->input['expire_month'],
                'expire_year' => $this->input['expire_year'],
                'date_of_birth' => $this->input['date_of_birth'],
            ]);
        } else if ($this->input['designation_id'] === '1') {
            NursePractitionerUsers::create([
                'category_id' => '2',
                'speciality_id' => '1',
                'first_name' => $this->input['first_name'],
                'last_name' => $this->input['last_name'],
                'ssn_no' => $this->input['ssn_no'],
                'dea_no' => $this->input['dea_no'],
                'zip_code' => $this->input['zip_code'],
                'expire_month' => $this->input['expire_month'],
                'expire_year' => $this->input['expire_year'],
                'date_of_birth' => $this->input['date_of_birth'],
            ]);
        } else if ($this->input['designation_id'] === '4') {
            PhysicianAssistantUsers::create([
                'category_id' => '3',
                'speciality_id' => '2',
                'first_name' => $this->input['first_name'],
                'last_name' => $this->input['last_name'],
                'ssn_no' => $this->input['ssn_no'],
                'dea_no' => $this->input['dea_no'],
                'zip_code' => $this->input['zip_code'],
                'expire_month' => $this->input['expire_month'],
                'expire_year' => $this->input['expire_year'],
                'date_of_birth' => $this->input['date_of_birth'],
            ]);
        } 
    }
}
