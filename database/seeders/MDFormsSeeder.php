<?php

namespace Database\Seeders;

use App\Models\MDForms;
use Illuminate\Database\Seeder;

class MDFormsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        sleep(1);
        $this->command->getOutput()->progressAdvance();
        $filetypes = array('M11Q','DOH-4359','MD-485','CFEEC');
        foreach ($filetypes as $fvalue) {
            $filetypesModel = new MDForms();
            $filetypesModel->name = $fvalue;
            $filetypesModel->save();
        }
    }
}
