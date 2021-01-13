<?php

namespace Database\Seeders;

use App\Models\CancelAppointmentReasons;
use App\Models\referral;
use Illuminate\Database\Seeder;

class CancelAppointmentReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        sleep(1);
        $this->command->getOutput()->progressAdvance();
        $appointmentReasons = array('Reason 1','Reason 2','Reason 3','Reason 4','Others');
        foreach ($appointmentReasons as $fvalue) {
            $appointmentReason = new CancelAppointmentReasons();
            $appointmentReason->name = $fvalue;
            $appointmentReason->save();
        }
        $this->command->getOutput()->progressFinish();
    }
}
