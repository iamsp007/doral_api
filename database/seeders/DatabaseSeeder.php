<?php

namespace Database\Seeders;

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
        // \App\Models\User::factory(10)->create();
        $role = Role::create(['guard_name' => 'web', 'name' => 'administrator']);
        $role = Role::create(['guard_name' => 'web', 'name' => 'admin']);
        $role = Role::create(['guard_name' => 'web', 'name' => 'co-ordinator']);
        $role = Role::create(['guard_name' => 'web', 'name' => 'Supervisor']);
        $role = Role::create(['guard_name' => 'web', 'name' => 'Clinician']);
        $role = Role::create(['guard_name' => 'web', 'name' => 'Patient']);
        $role = Role::create(['guard_name' => 'web', 'name' => 'Referral']);

        $permission = Permission::create(['guard_name' => 'web', 'name' => 'Create']);
        $permission = Permission::create(['guard_name' => 'web', 'name' => 'edit']);
        $permission = Permission::create(['guard_name' => 'web', 'name' => 'update']);
    }
}
