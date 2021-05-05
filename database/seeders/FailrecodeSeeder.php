<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FailRecodeImport;

class FailrecodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        sleep(1);
        //$this->command->getOutput()->progressAdvance();
        $value = array('last_name'=>'','first_name'=>'Roksana','middle_name'=>'','gender'=>'FEMALE','date_of_birth'=>'10/31/1984','caregiver_code'=>'HSC-49406','ssn'=>'113-94-2406');
        $fail_recode = new FailRecodeImport();
        $fail_recode->row = 2;
        $fail_recode->file_name = 'demo.xls';
        $fail_recode->service_id = 3;
        $fail_recode->attribute = 'last_name';
        $fail_recode->errors = 'The last_name field is required';
        $fail_recode->values = json_encode($value);
        $fail_recode->save();


    }
}
