<?php

namespace Database\Seeders;

use App\Models\DoseMaster;
use Illuminate\Database\Seeder;

class DoseMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = array('81 MG','41 MG','Others');
        foreach ($names as $value) {
            $doseModel = new DoseMaster();
            $doseModel->name = $value;
            $doseModel->save();
        }
    }
}
