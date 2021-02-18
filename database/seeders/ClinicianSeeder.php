<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class ClinicianSeeder extends Seeder
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

        $admin = new User();
        $admin->first_name = 'Doral';
        $admin->last_name = 'Clinician';
        $admin->email = 'clinician@doral.com';
        $admin->email_verified_at = now();
        $admin->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $admin->remember_token = Str::random(10);
        $admin->dob = Carbon::now();
        $admin->phone = NULL;
        $admin->status = '1';
        $admin->assignRole('clinician')->syncPermissions(Permission::all());
        $admin->save();

        $admin1 = new User();
        $admin1->first_name = 'Doral Assistant';
        $admin1->last_name = 'Clinician';
        $admin1->email = 'assistant@doral.com';
        $admin1->email_verified_at = now();
        $admin1->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $admin1->remember_token = Str::random(10);
        $admin1->dob = Carbon::now();
        $admin1->phone = NULL;
        $admin1->status = '1';
        $admin1->assignRole('clinician')->syncPermissions(Permission::all());
        $admin1->save();

        $admin1 = new User();
        $admin1->first_name = 'Doral Assistant';
        $admin1->last_name = 'Clinician';
        $admin1->email = 'assistant@doral.com';
        $admin1->email_verified_at = now();
        $admin1->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $admin1->remember_token = Str::random(10);
        $admin1->dob = Carbon::now();
        $admin1->phone = NULL;
        $admin1->status = '1';
        $admin1->assignRole('clinician')->syncPermissions(Permission::all());
        $admin1->save();
    }
}
