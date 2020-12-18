<?php

namespace Database\Seeders;

use App\Models\ServiceMaster;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array('VBC','MD Order','Occupational Health','Telehealth','roadL');
        foreach ($data as $datum) {
            $serviceModel = new ServiceMaster();
            $serviceModel->name = $datum;
            $serviceModel->save();
        }
    }
}
