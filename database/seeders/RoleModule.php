<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoleModuleName;

class RoleModule extends Seeder
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
				'name' 	=> 'Dashboard',
			],
			[
				'name' 	=> 'Referral',
			],
			[
				'name' 	=> 'Clinician',
			],
			[
				'name' 	=> 'Services',
			],
			[
				'name' 	=> 'Patient',
			],
			[
				'name' 	=> 'Profile',
			],
			[
				'name' 	=> 'PatientChart',
			],
			[
				'name' 	=> 'Appointment',
			],
			[
				'name' 	=> 'RodalRequest',
			],
			[
				'name' 	=> 'Request ',
			],
		];

		foreach ($data as $value) {
            $role_module = new RoleModuleName();
            $role_module->name = $value['name'];
            $role_module->save();
        }


    }
}
