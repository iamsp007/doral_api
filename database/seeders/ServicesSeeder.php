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
        $data = array('VBC','MD Order','Occupational Health','Telehealth','roadL');
        foreach ($data as $datum) {
            $designation = new Services();
            $designation->name = $datum;
            $designation->save();
        }
    }
}
