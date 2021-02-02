<?php

namespace Database\Seeders;

use App\Models\Services;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
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
        $data = array('VBC'=>'1','MD Order'=>'1','Occupational Health'=>'1','Telehealth'=>'1','roadL'=>'0');
        foreach ($data as $name=>$value) {
            $designation = new Services();
            $designation->name = $name;
            $designation->display_type = $value;
            $designation->save();
        }
    }
}
