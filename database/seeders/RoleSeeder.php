<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->getOutput()->progressStart(15);
        $noral_roles = ['admin','co-ordinator','supervisor','clinician','patient'];
        foreach ($noral_roles as $noral_role) {
            $role = Role::create(['guard_name' => 'web', 'name' => $noral_role]);
        }

        $referral_roles = ['referral'];
        foreach ($referral_roles as $referral_role) {
            $role = Role::create(['guard_name' => 'referral', 'name' => $referral_role]);
        }


        $partner_roles = ['admin','coordinator','supervisor','filedvisitor'];
        foreach ($partner_roles as $partner_role) {
            $role = Role::create(['guard_name' => 'partner', 'name' => $partner_role]);
        }
    }
}
