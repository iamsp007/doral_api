<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServicePaymentPlanDetails;

class ServicePaymentPlanDetailsSeeder extends Seeder
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
				'service_payment_plan_id' 	=> '1',
				'name' 						=> 'Self Pay',
			],
			[
				'service_payment_plan_id' 	=> '1',
				'name' 						=> 'Home Care Pay',
			],
			[
				'service_payment_plan_id' 	=> '2',
				'name' 						=> 'Self Pay',
			],
			[
				'service_payment_plan_id' 	=> '2',
				'name' 						=> 'Home Care Pay',
			],
			[
				'service_payment_plan_id' 	=> '2',
				'name' 						=> 'N/A',
			],
			[
				'service_payment_plan_id' 	=> '4',
				'name' 						=> 'Wage Parity',
			],
			[
				'service_payment_plan_id' 	=> '4',
				'name' 						=> 'Self Pay',
			],
			[
				'service_payment_plan_id' 	=> '4',
				'name' 						=> 'Employer Pay',
			],
			[
				'service_payment_plan_id' 	=> '5',
				'name' 						=> 'Wage Parity',
			],
			[
				'service_payment_plan_id' 	=> '5',
				'name' 						=> 'Self Pay',
			],
			[
				'service_payment_plan_id' 	=> '5',
				'name' 						=> 'Employer Pay',
			],
			[
				'service_payment_plan_id' 	=> '5',
				'name' 						=> 'N/A',
			],
			[
				'service_payment_plan_id' 	=> '6',
				'name' 						=> 'Wage Parity',
			],
			[
				'service_payment_plan_id' 	=> '6',
				'name' 						=> 'Self Pay',
			],
			[
				'service_payment_plan_id' 	=> '6',
				'name' 						=> 'Employer Pay',
			],
			[
				'service_payment_plan_id' 	=> '6',
				'name' 						=> 'N/A',
			]
		];

        foreach ($data as $value) {
            $service_payment_plan_details = new ServicePaymentPlanDetails();
            $service_payment_plan_details->service_payment_plan_id = $value['service_payment_plan_id'];
            $service_payment_plan_details->name = $value['name'];
            $service_payment_plan_details->save();
        }
    }
}
