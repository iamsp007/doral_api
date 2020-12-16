<?php

namespace Database\Seeders;

use App\Models\FileTypeMaster;
use Illuminate\Database\Seeder;

class FileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
// File type seeder
        $filetypes = array('Demographic','Clinical','COMPLIANCE DUE DATES','PREVIOUS MD ORDER');
        foreach ($filetypes as $fvalue) {
            $filetypesModel = new FileTypeMaster();
            $filetypesModel->name = $fvalue;
            $filetypesModel->save();
        }
    }
}
