<?php

namespace Database\Seeders;

use App\Models\Software;
use Illuminate\Database\Seeder;

class SoftwareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appointmentReasons = array('HHA Exchange');
        $authentication = [
            "AppName" => "HCHS257",
            "AppSecret" => "99473456-2939-459c-a5e7-f2ab47a5db2f",
            "AppKey" => "MQAwADcAMwAxADMALQAzADEAQwBDADIAQQA4ADUAOQA3AEEARgBDAEYAMwA1AEIARQA0ADQANQAyAEEANQBFADIAQgBDADEAOAA="
        ];
        
        foreach ($appointmentReasons as $fvalue) {
            $software = new Software();
            $software->name = $fvalue;
            $software->authentication = $authentication;
            $software->save();
        }
    }
}
