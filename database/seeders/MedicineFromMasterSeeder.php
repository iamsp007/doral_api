<?php

namespace Database\Seeders;

use App\Models\MedicineFromMaster;
use Illuminate\Database\Seeder;

class MedicineFromMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = array('Tablet','Capsual');
        foreach ($names as $value) {
            $doseModel = new MedicineFromMaster();
            $doseModel->name = $value;
            $doseModel->save();
        }
    }
}
