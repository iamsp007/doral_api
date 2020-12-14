<?php

namespace Database\Seeders;

use App\Models\FileTypeMaster;
use App\Models\ServiceMaster;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $role = Role::create(['guard_name' => 'web', 'name' => 'admin']);
//        $role = Role::create(['guard_name' => 'web', 'name' => 'co-ordinator']);
//        $role = Role::create(['guard_name' => 'web', 'name' => 'supervisor']);
//        $role = Role::create(['guard_name' => 'web', 'name' => 'clinician']);
//        $role = Role::create(['guard_name' => 'web', 'name' => 'patient']);
//        $role = Role::create(['guard_name' => 'referral', 'name' => 'referral']);
//
//        $this->call(
//            AdminSeeder::class,
//            DiesesMasterSeeder::class,
//        );

        // File type seeder
        $filetypes = array('Demographic','Clinical','COMPLIANCE DUE DATES','PREVIOUS MD ORDER');
        foreach ($filetypes as $fvalue) {
            $filetypesModel = new FileTypeMaster();
            $filetypesModel->name = $fvalue;
            $filetypesModel->save();
        }

        $data = array('VBC','MD Order','Occupational Health','Telehealth','roadL');
        foreach ($data as $datum) {
            $serviceModel = new ServiceMaster();
            $serviceModel->name = $datum;
            $serviceModel->save();
        }

    }
}
