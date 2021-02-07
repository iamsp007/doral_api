<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PartnerUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->first_name = 'Doral';
        $user->last_name = 'LAB';
        $user->email = 'lab@doral.com';
        $user->email_verified_at = now();
        $user->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $user->remember_token = Str::random(10);
        $user->dob = Carbon::now();
        $user->status = '1';
        $user->assignRole('clinician','LAB')->syncPermissions(Permission::all());
        $user->save();

        $user = new User();
        $user->first_name = 'Doral';
        $user->last_name = 'X-RAY';
        $user->email = 'xray@doral.com';
        $user->email_verified_at = now();
        $user->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $user->remember_token = Str::random(10);
        $user->dob = Carbon::now();
        $user->status = '1';
        $user->assignRole('clinician','X-RAY')->syncPermissions(Permission::all());
        $user->save();

        $user = new User();
        $user->first_name = 'Doral';
        $user->last_name = 'CHHA';
        $user->email = 'chha@doral.com';
        $user->email_verified_at = now();
        $user->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $user->remember_token = Str::random(10);
        $user->dob = Carbon::now();
        $user->status = '1';
        $user->assignRole('clinician','CHHA')->syncPermissions(Permission::all());
        $user->save();

        $user = new User();
        $user->first_name = 'Doral';
        $user->last_name = 'DME';
        $user->email = 'dme@doral.com';
        $user->email_verified_at = now();
        $user->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $user->remember_token = Str::random(10);
        $user->dob = Carbon::now();
        $user->status = '1';
        $user->assignRole('clinician','DME')->syncPermissions(Permission::all());
        $user->save();
    }
}
