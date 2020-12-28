<?php

namespace Database\Seeders;

use App\Models\Designation;
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
        $role = Role::create(['guard_name' => 'web', 'name' => 'admin']);
        $role = Role::create(['guard_name' => 'web', 'name' => 'co-ordinator']);
        $role = Role::create(['guard_name' => 'web', 'name' => 'supervisor']);
        $role = Role::create(['guard_name' => 'web', 'name' => 'clinician']);
        $role = Role::create(['guard_name' => 'web', 'name' => 'patient']);
        $role = Role::create(['guard_name' => 'referral', 'name' => 'referral']);
      
        $this->call([
            AdminSeeder::class,
            ClinicianSeeder::class,
            PatientSeeder::class,
            SupervisorSeeder::class,
            CoordinatorSeeder::class,
            DesignationSeeder::class,
            DiesesMasterSeeder::class,
            FileTypeSeeder::class,
            ServicesSeeder::class,
            MDFormsSeeder::class,
            StatesFileSeeder::class,
            CitiesFileSeeder::class,
            CitiesExtendedFileSeeder::class
        ]);
    }
}
