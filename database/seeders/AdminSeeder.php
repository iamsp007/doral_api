<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new User();
        $admin->first_name = 'Doral';
        $admin->last_name = 'Admin';
        $admin->email = 'admin@doral.com';
        $admin->email_verified_at = now();
        $admin->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $admin->remember_token = Str::random(10);
        $admin->dob = Carbon::now();
        $admin->phone = NULL;
        $admin->status = '1';
        $admin->assignRole('admin')->syncPermissions(Permission::all());
        $admin->save();
    }
}
