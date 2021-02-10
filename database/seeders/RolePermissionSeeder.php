<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        sleep(1);
        $this->command->getOutput()->progressStart();
        $this->command->getOutput()->progressAdvance();

        $data = [
			[
				'rl_module_name_id'	=> '1',
				'name' 				=> 'view_dashboard',
			],
			[
				'rl_module_name_id'	=> '2',
				'name' 				=> 'view_new_registered_referral',
			],
			[
				'rl_module_name_id' => '2',
				'name' 				=> 'view_activited_referral',
			],
			[
				'rl_module_name_id'	=> '2',
				'name' 				=> 'view_referral_profile',
			],
			[
				'rl_module_name_id' => '2',
				'name' 				=> 'edit_referral_profile',
			],
			[
				'rl_module_name_id' => '2',
				'name' 				=> 'referral_approval',
			],
			[
				'rl_module_name_id'	=> '3',
				'name' 				=> 'view_new_registered_clinician',
			],
			[
				'rl_module_name_id' => '3',
				'name' 				=> 'view_activited_clinician',
			],
			[
				'rl_module_name_id'	=> '3',
				'name' 				=> 'view_clinician_profile',
			],
			[
				'rl_module_name_id' => '3',
				'name' 				=> 'edit_clinician_profile',
			],
			[
				'rl_module_name_id' => '3',
				'name' 				=> 'clinician_approval',
			],
			[
				'rl_module_name_id' => '4',
				'name' 				=> 'view_vbc_patient',
			],
			[
				'rl_module_name_id' => '4',
				'name' 				=> 'vbc_patient_import',
			],
			[
				'rl_module_name_id' => '4',
				'name' 				=> 'view_md_order_patient',
			],
			[
				'rl_module_name_id' => '4',
				'name' 				=> 'md_order_patient_import',
			],
			[
				'rl_module_name_id' => '4',
				'name' 				=> 'view_occupational_health_patient',
			],
			[
				'rl_module_name_id' => '4',
				'name' 				=> 'occupational_health_patient_import',
			],
			[
				'rl_module_name_id' => '5',
				'name' 				=> 'add_patient',
			],
			[
				'rl_module_name_id' => '6',
				'name' 				=> 'view_profile',
			],
			[
				'rl_module_name_id' => '6',
				'name' 				=> 'edit_profile',
			],
			[
				'rl_module_name_id'	=> '7',
				'name' 				=> 'view_new_patient_list',
			],
			[
				'rl_module_name_id' => '7',
				'name' 				=> 'view_patient_list',
			],
			[
				'rl_module_name_id'	=> '7',
				'name' 				=> 'patient_approval',
			],
			[
				'rl_module_name_id' => '8',
				'name' 				=> 'upcoming_appointment_list',
			],
			[
				'rl_module_name_id' => '8',
				'name' 				=> 'canceled_appointment_list',
			],
			[
				'rl_module_name_id' => '8',
				'name' 				=> 'appointment_approval',
			],
			[
				'rl_module_name_id' => '9',
				'name' 				=> 'view_rodal_request',
			],
			[
				'rl_module_name_id' => '10',
				'name' 				=> 'view_clinical_requst',
			],
			[
				'rl_module_name_id' => '10',
				'name' 				=> 'view_technical_request',
			],
		];


        foreach ($data as $value) {
            $role_permission = new RolePermission();
            $role_permission->rl_module_name_id = $value['rl_module_name_id'];
            $role_permission->name = $value['name'];
            $role_permission->save();
        }
    }
}
