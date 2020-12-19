<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array('Nurse Practioner','Special Assistant','Medical Assistant','Physician','Physiotherapist');
        foreach ($data as $datum) {
            $designation = new Designation();
            $designation->name = $datum;
            $designation->role_id = '4';
            $designation->status = '1';
            $designation->save();
        }
    }
}
