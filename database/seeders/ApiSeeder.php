<?php

namespace Database\Seeders;

use App\Models\Api;
use Illuminate\Database\Seeder;

class ApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appointmentReasons = array('Get Patient Demographics');
       
        $field = [
            'label' => 'Patient ID',
            'name' => 'patient_id'
        ];
        foreach ($appointmentReasons as $fvalue) {
            $api = new Api();
            $api->name = $fvalue;
            $api->field = $field;
            $api->save();
        }
    }
}
