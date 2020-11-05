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
        $role = Role::create(['guard_name' => 'admin', 'name' => 'administrator']);

        $permission = Permission::create(['guard_name' => 'admin', 'name' => 'Create']);
        $permission = Permission::create(['guard_name' => 'web', 'name' => 'Create']);
        $permission = Permission::create(['guard_name' => 'api', 'name' => 'Create']);
    }
}
