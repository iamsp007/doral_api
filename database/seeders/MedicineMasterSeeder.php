<?php

namespace Database\Seeders;

use App\Models\MedicineMaster;
use Illuminate\Database\Seeder;

class MedicineMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = array('Oral','Capsual');
        foreach ($names as $value) {
            $doseModel = new MedicineMaster();
            $doseModel->name = $value;
            $doseModel->save();
        }
    }
}
