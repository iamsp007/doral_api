<?php

namespace Database\Seeders;

use App\Models\Plans;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $filetypes = array('Plan-20','Plan-25','Plan-30','Plan-35','Plan-40');
        foreach ($filetypes as $fvalue) {
            $filetypesModel = new Plans();
            $filetypesModel->name = $fvalue;
            $filetypesModel->save();
        }
    }
}
