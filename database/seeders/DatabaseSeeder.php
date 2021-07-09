<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
            // LabReportTypeSeeder::class,
            // PartnerUser::class,
            SelectionTableSeeder::class
        ]);
    }
}
