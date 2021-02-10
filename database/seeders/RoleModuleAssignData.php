<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoleModuleAssign;

class RoleModuleAssignData extends Seeder
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
				'role_id' 	=> 1,
				'rl_module_name_id'=>1
			],
			[
				'role_id' 	=> 1,
				'rl_module_name_id'=>2
			],
			[
				'role_id' 	=> 1,
				'rl_module_name_id'=>3
			],
			[
				'role_id' 	=> 6,
				'rl_module_name_id'=>1
			],
			[
				'role_id' 	=> 6,
				'rl_module_name_id'=>4
			],
			[
				'role_id' 	=> 6,
				'rl_module_name_id'=>5
			],
			[
				'role_id' 	=> 6,
				'rl_module_name_id'=>6
			],
			[
				'role_id' 	=> 4,
				'rl_module_name_id'=>1
			],
			[
				'role_id' 	=> 4,
				'rl_module_name_id'=>7
			],
			[
				'role_id' 	=> 4,
				'rl_module_name_id'=>5
			],
			[
				'role_id' 	=> 4,
				'rl_module_name_id'=>8
			],
			[
				'role_id' 	=> 4,
				'rl_module_name_id'=>9
			],
			[
				'role_id' 	=> 4,
				'rl_module_name_id'=>10
			],

         ];

         foreach ($data as $value) {
            $role_module_assign = new RoleModuleAssign();
            $role_module_assign->role_id = $value['role_id'];
            $role_module_assign->rl_module_name_id = $value['rl_module_name_id'];
            $role_module_assign->save();
        }
    }
}
