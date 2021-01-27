<?php

namespace Database\Seeders;

use App\Models\PreferredPharmacyMaster;
use Illuminate\Database\Seeder;

class PreferredPharmacyMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = array('Aciclovir (including Zovirax)','Azathioprine');
        foreach ($names as $value) {
            $doseModel = new PreferredPharmacyMaster();
            $doseModel->name = $value;
            $doseModel->save();
        }
    }
}
