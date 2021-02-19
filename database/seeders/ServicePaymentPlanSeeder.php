<?php

namespace Database\Seeders;

use App\Models\ServicePaymentPlan;
use Illuminate\Database\Seeder;

class ServicePaymentPlanSeeder extends Seeder
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
				'service_id' 	=> '2',
				'name' 			=> 'Plan 1',
				'status' 		=> '1',
			],

			[
				'service_id' 	=> '2',
				'name' 			=> 'Plan 2',
				'status' 		=> '1',
			],
			[
				'service_id' 	=> '2',
				'name' 			=> 'Plan 3',
				'status' 		=> '1',
			],

			[
				'service_id' 	=> '3',
				'name' 			=> 'Plan 1',
				'status' 		=> '1',
			],
			[
				'service_id' 	=> '3',
				'name' 			=> 'Plan 2',
				'status' 		=> '1',
			],
			[
				'service_id' 	=> '3',
				'name' 			=> 'Plan 3',
				'status' 		=> '1',
			]
		];

        foreach ($data as $value) {
            $service_payment_plan = new ServicePaymentPlan();
            $service_payment_plan->service_id = $value['service_id'];
            $service_payment_plan->name = $value['name'];
            $service_payment_plan->status = $value['status'];
            $service_payment_plan->save();
        }
    }
}
