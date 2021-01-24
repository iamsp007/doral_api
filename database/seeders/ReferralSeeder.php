<?php

namespace Database\Seeders;

use App\Models\FileTypeMaster;
use App\Models\Referral;
use Illuminate\Database\Seeder;

class ReferralSeeder extends Seeder
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
        $filetypes = array('Insurance','Home Care','Others');
        foreach ($filetypes as $fvalue) {
            $referralModel = new Referral();
            $referralModel->guard_name = 'referral';
            $referralModel->name = $fvalue;
            $referralModel->save();
        }
        sleep(1);
        $this->command->getOutput()->progressAdvance();
        $referralTypes = array('LAB','X-RAY','CHHA','Home Oxygen','Home Influsion','Wound Care','DME');
        foreach ($referralTypes as $rvalue) {
            $referralModel = new Referral();
            $referralModel->guard_name = 'partner';
            $referralModel->name = $rvalue;
            $referralModel->save();
        }

    }
}
