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


        $this->call([
            // RoleSeeder::class,
            // AdminSeeder::class,
            // ClinicianSeeder::class,
            // PatientSeeder::class,
            // SupervisorSeeder::class,
            // CoordinatorSeeder::class,
            // DesignationSeeder::class,
            // DiesesMasterSeeder::class,
            // FileTypeSeeder::class,
            // ServicesSeeder::class,
            // MDFormsSeeder::class,
            // PlanSeeder::class,
            // ReferralSeeder::class,
            // CancelAppointmentReasonSeeder::class,
            // DoseMasterSeeder::class,
            // FrequencyMasterSeeder::class,
            // MedicineFromMasterSeeder::class,
            // MedicineMasterSeeder::class,
            // PreferredPharmacyMasterSeeder::class,
            LabReportTypeSeeder::class
        ]);
    }
}
