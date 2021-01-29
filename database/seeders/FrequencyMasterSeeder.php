<?php

namespace Database\Seeders;

use App\Models\FrequencyMaster;
use Illuminate\Database\Seeder;

class FrequencyMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = array('Daily','1/2','2 times','3 times');
        foreach ($names as $value) {
            $doseModel = new FrequencyMaster();
            $doseModel->name = $value;
            $doseModel->save();
        }
    }
}
