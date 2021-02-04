<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LabReportType;

class LabReportTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LabReportType::truncate();

        $types =  [
            [
                'name' => 'TB Screen',
                'sequence' => 1,
                'status' => 1,
                'parent_id' => NULL,
            ],
            [
                'name' => 'PPD',
                'sequence' => 1,
                'status' => 1,
                'parent_id' => 1,
            ],
            [
                'name' => 'QuantiFERON',
                'sequence' => 2,
                'status' => 1,
                'parent_id' => 1,
            ],
            [
                'name' => 'Chest X-Ray',
                'sequence' => 3,
                'status' => 1,
                'parent_id' => 1,
            ],
            [
                'name' => 'TB(Gold)',
                'sequence' => 4,
                'status' => 1,
                'parent_id' => 1,
            ],
            [
                'name' => 'TB(Gold Plus)',
                'sequence' => 5,
                'status' => 1,
                'parent_id' => 1,
            ],
            [
                'name' => 'Immunization',
                'sequence' => 2,
                'status' => 1,
                'parent_id' => NULL,
            ],
            [
                'name' => 'Rubella',
                'sequence' => 1,
                'status' => 1,
                'parent_id' => 2,
            ],
            [
                'name' => 'Rubeola',
                'sequence' => 2,
                'status' => 1,
                'parent_id' => 2,
            ],
            [
                'name' => 'Rubella MMR',
                'sequence' => 3,
                'status' => 1,
                'parent_id' => 2,
            ],
            [
                'name' => 'Rubeola MMR',
                'sequence' => 4,
                'status' => 1,
                'parent_id' => 2,
            ],
            [
                'name' => 'Drug Screen',
                'sequence' => 3,
                'status' => 1,
                'parent_id' => NULL,
            ],
            [
                'name' => 'Drug Screen',
                'sequence' => 1,
                'status' => 1,
                'parent_id' => 3,
            ],
            [
                'name' => 'Drug Test',
                'sequence' => 2,
                'status' => 1,
                'parent_id' => 3,
            ],
        ];

        LabReportType::insert($types);
    }
}
