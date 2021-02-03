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
        $names = array('PPD/QuantiFERON', 'TB Screen', 'Rubeola', 'Rubeola MMR1', 'Rubeola MMR2', 'Rubella', 'Rubella MMR', 'Facemask Provided', 'Drug Screen', 'Annual Health Assessment');
        foreach ($names as $name) {
            $labReportTypeModel = new LabReportType();
            $labReportTypeModel->name = $name;
            $labReportTypeModel->save();
        }
    }
}
